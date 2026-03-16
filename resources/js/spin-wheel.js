const wheel = document.getElementById("spinWheel");
const spinButton = document.getElementById("spinButton");
const emptyNotice = document.getElementById("spinEmptyNotice");
const statusNotice = document.getElementById("spinStatusNotice");
const defaultSpinLabel = spinButton ? spinButton.textContent.trim() : "";

let isSpinning = false;
let currentRotation = 0;

const center = 180;
const radius = 170;
const segments = Array.isArray(window.spinSegments) ? window.spinSegments : [];
const segmentCount = segments.length;
const spinConfig = window.spinConfig || {};
const spinDurationMs = Math.max(
    2000,
    Number(spinConfig.spin_duration || 0) * 1000 || 5000,
);
const spinUrl = window.spinUrl || "";
const spinAudioUrl = window.spinAudioUrl || "";
const spinAudio = spinAudioUrl ? new Audio(spinAudioUrl) : null;
if (spinAudio) {
    spinAudio.preload = "auto";
}

renderWheel();

function renderWheel() {
    if (!wheel) return;

    wheel.innerHTML = "";
    wheel.style.transformBox = "fill-box";
    wheel.style.transformOrigin = "50% 50%";

    if (!segmentCount) {
        if (spinButton) {
            spinButton.disabled = true;
            spinButton.classList.add("opacity-60", "cursor-not-allowed");
        }
        if (emptyNotice) {
            emptyNotice.classList.remove("hidden");
        }
        if (statusNotice) {
            statusNotice.classList.add("hidden");
        }
        return;
    }

    const angle = (2 * Math.PI) / segmentCount;

    segments.forEach((segment, i) => {
        const start = i * angle;
        const end = start + angle;

        const x1 = center + radius * Math.cos(start);
        const y1 = center + radius * Math.sin(start);

        const x2 = center + radius * Math.cos(end);
        const y2 = center + radius * Math.sin(end);

        const path = document.createElementNS(
            "http://www.w3.org/2000/svg",
            "path",
        );

        const d = `
            M ${center} ${center}
            L ${x1} ${y1}
            A ${radius} ${radius} 0 0 1 ${x2} ${y2}
            Z
        `;

        path.setAttribute("d", d);
        path.setAttribute("fill", i % 2 ? "#F7F3FF" : "#DDB1ED");
        path.setAttribute("stroke", "#8D6B9A");
        path.setAttribute("stroke-width", "1");

        wheel.appendChild(path);

        // TEXT LABEL
        const textAngle = start + angle / 2;

        const textX = center + radius * 0.6 * Math.cos(textAngle);
        const textY = center + radius * 0.6 * Math.sin(textAngle);

        const text = document.createElementNS(
            "http://www.w3.org/2000/svg",
            "text",
        );

        text.setAttribute("x", textX);
        text.setAttribute("y", textY);
        text.setAttribute("text-anchor", "middle");
        text.setAttribute("alignment-baseline", "middle");
        const label = String(segment.label || "");
        const fontSize = label.length > 12 ? 12 : label.length > 8 ? 14 : 16;

        text.setAttribute("fill", "#572F75");
        text.setAttribute("font-size", String(fontSize));
        text.setAttribute("pointer-events", "none");

        text.textContent = label;

        wheel.appendChild(text);
    });

    const border = document.createElementNS(
        "http://www.w3.org/2000/svg",
        "circle",
    );

    border.setAttribute("cx", center);
    border.setAttribute("cy", center);
    border.setAttribute("r", radius);
    border.setAttribute("fill", "none");
    border.setAttribute("stroke", "#8D6B9A");
    border.setAttribute("stroke-width", "10");

    wheel.appendChild(border);
}

if (spinButton) {
    spinButton.addEventListener("click", async () => {
        if (isSpinning || !segmentCount || !spinUrl) return;

        playSpinSound();
        setSpinningState(true);

        try {
            const response = await fetch(spinUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                },
            });

            const result = await response.json();

            if (!response.ok) {
                const error = new Error(result.message || "Gagal memutar.");
                error.code = result.code;
                error.retryAfterSeconds = result.retry_after_seconds;
                throw error;
            }

            const index = segments.findIndex(
                (s) => s.id === result.segment_id,
            );

            if (index < 0) {
                throw new Error("Segment tidak valid.");
            }

            spinTo(index, result);
        } catch (err) {
            console.error(err);
            const message = err.message || "Terjadi kesalahan. Coba lagi.";
            if (window.Swal && typeof window.Swal.fire === "function") {
                window.Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: message,
                });
            } else {
                alert(message);
            }
            if (err.code === "cooldown_active" && err.retryAfterSeconds) {
                startCooldown(err.retryAfterSeconds);
            } else if (err.code === "max_spin_reached") {
                setSpinningState(false);
                lockSpinButton();
                setStatusNotice(err.message || "Kesempatan spin kamu sudah habis.");
            } else {
                setSpinningState(false);
                setStatusNotice(err.message || "Terjadi kesalahan.");
            }
        }
    });
}

function spinTo(index, result) {
    if (!wheel || !segmentCount) return;

    const segmentAngle = 360 / segmentCount;
    const centerAngle = (index + 0.5) * segmentAngle;
    const jitter = (Math.random() * 0.4 - 0.2) * segmentAngle;
    const desiredRotation = 270 - (centerAngle + jitter);

    const currentNormalized =
        ((currentRotation % 360) + 360) % 360;
    let delta = desiredRotation - currentNormalized;
    if (delta < 0) delta += 360;

    const spins = 5 + Math.floor(Math.random() * 2);
    const rotation = currentRotation + spins * 360 + delta;

    wheel.style.transition = `transform ${spinDurationMs}ms cubic-bezier(0.16, 1, 0.3, 1)`;
    wheel.style.transform = `rotate(${rotation}deg)`;

    currentRotation = rotation;

    setTimeout(() => {
        if (result.success_url) {
            window.location = result.success_url;
        } else if (result.reward) {
            window.location = "/games/result/success/" + result.reward.id;
        } else {
            window.location = "/games/result/fail";
        }
    }, spinDurationMs);
}

function setSpinningState(value) {
    isSpinning = value;

    if (!spinButton) return;

    spinButton.disabled = value;
    spinButton.setAttribute("aria-busy", value ? "true" : "false");
    if (value) {
        spinButton.classList.add("opacity-70", "cursor-not-allowed");
    } else {
        spinButton.classList.remove("opacity-70", "cursor-not-allowed");
    }
}

function lockSpinButton() {
    if (!spinButton) return;
    spinButton.disabled = true;
    spinButton.classList.add("opacity-70", "cursor-not-allowed");
}

function startCooldown(seconds) {
    if (!spinButton) return;

    isSpinning = false;
    const endTime = Date.now() + seconds * 1000;

    spinButton.disabled = true;
    spinButton.classList.add("opacity-70", "cursor-not-allowed");

    const tick = () => {
        const remainingMs = endTime - Date.now();
        if (remainingMs <= 0) {
            spinButton.textContent = defaultSpinLabel || "Putar Sekarang";
            spinButton.disabled = false;
            spinButton.classList.remove("opacity-70", "cursor-not-allowed");
            setStatusNotice("");
            return;
        }

        const totalSeconds = Math.ceil(remainingMs / 1000);
        const minutes = Math.floor(totalSeconds / 60);
        const secs = String(totalSeconds % 60).padStart(2, "0");
        spinButton.textContent = `Tunggu ${minutes}:${secs}`;
        setStatusNotice(`Tunggu ${minutes}:${secs} sebelum spin lagi.`);
        setTimeout(tick, 1000);
    };

    tick();
}

function setStatusNotice(message) {
    if (!statusNotice) return;
    if (!message) {
        statusNotice.textContent = "";
        statusNotice.classList.add("hidden");
        return;
    }
    statusNotice.textContent = message;
    statusNotice.classList.remove("hidden");
}

function playSpinSound() {
    if (!spinAudio) return;
    try {
        spinAudio.currentTime = 0;
        const playPromise = spinAudio.play();
        if (playPromise && typeof playPromise.catch === "function") {
            playPromise.catch(() => {});
        }
    } catch (_) {
        // ignore playback errors
    }
}
