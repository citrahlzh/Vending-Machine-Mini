@extends('games.layouts.app', [
    'title' => 'Guess Image Game'
])

@section('content')
    <div class="w-full max-w-[860px] text-center flex flex-col items-center gap-8 lg:gap-10">
        <div class="space-y-3">
            <h1 class="text-[26px] sm:text-[32px] lg:text-[36px] font-semibold text-[#4a2a6c] leading-snug">
                Tebak gambar berikut dengan benar dan dapatkan hadiahnya!
            </h1>
            <div class="flex items-center justify-center gap-4 text-sm text-[#6b4a87] font-semibold">
                <span id="quizProgress">Soal 1/1</span>
                <span id="quizTimer" class="hidden">Sisa waktu: <span id="quizTimerValue">00:00</span></span>
            </div>
        </div>

        <div class="w-full max-w-[520px]">
            <div id="quizPrompt"
                class="bg-[#f7f1ff] px-10 py-6 rounded-[22px] text-[20px] sm:text-[22px] font-semibold text-[#2d1b40] border-2 border-[#5A2F7E] shadow-[8px_8px_0px_#5A2F7E] min-h-[110px] flex items-center justify-center text-center">
            </div>
        </div>

        <div class="w-full max-w-[420px] flex items-center justify-center">
            <div
                class="h-[200px] w-[200px] sm:h-[230px] sm:w-[230px] rounded-[28px] bg-[#e8def5] border-2 border-[#5A2F7E] shadow-[8px_8px_0px_#5A2F7E] flex items-center justify-center">
                <img id="quizImage" alt="Gambar soal" class="max-h-[170px] w-auto object-contain" />
            </div>
        </div>

        <div id="quizOptions" class="grid grid-cols-1 sm:grid-cols-2 gap-5 w-full max-w-[680px]"></div>

        <div id="quizTextAnswer" class="hidden w-full max-w-[520px]">
            <input id="quizTextInput" type="text"
                class="h-12 w-full rounded-[18px] border-2 border-[#5A2F7E] bg-white px-4 text-[18px] text-[#2d1b40] shadow-[6px_6px_0px_#5A2F7E] outline-none focus:border-[#4b1f74]"
                placeholder="Masukkan jawabanmu">
        </div>

        <div id="quizKeyboard" class="hidden w-full max-w-[520px]">
            <div
                class="mt-3 w-full rounded-[20px] border-2 border-[#5A2F7E] bg-[#f7f1ff] px-4 py-4 shadow-[6px_6px_0px_#5A2F7E]">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-center gap-2">
                        <button type="button" data-key="Q"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">Q</button>
                        <button type="button" data-key="W"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">W</button>
                        <button type="button" data-key="E"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">E</button>
                        <button type="button" data-key="R"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">R</button>
                        <button type="button" data-key="T"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">T</button>
                        <button type="button" data-key="Y"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">Y</button>
                        <button type="button" data-key="U"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">U</button>
                        <button type="button" data-key="I"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">I</button>
                        <button type="button" data-key="O"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">O</button>
                        <button type="button" data-key="P"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">P</button>
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <button type="button" data-key="A"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">A</button>
                        <button type="button" data-key="S"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">S</button>
                        <button type="button" data-key="D"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">D</button>
                        <button type="button" data-key="F"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">F</button>
                        <button type="button" data-key="G"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">G</button>
                        <button type="button" data-key="H"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">H</button>
                        <button type="button" data-key="J"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">J</button>
                        <button type="button" data-key="K"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">K</button>
                        <button type="button" data-key="L"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">L</button>
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <button type="button" data-key="Z"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">Z</button>
                        <button type="button" data-key="X"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">X</button>
                        <button type="button" data-key="C"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">C</button>
                        <button type="button" data-key="V"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">V</button>
                        <button type="button" data-key="B"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">B</button>
                        <button type="button" data-key="N"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">N</button>
                        <button type="button" data-key="M"
                            class="h-10 w-10 rounded-[10px] bg-[#5A2F7E] text-white text-[16px] font-semibold shadow-[3px_3px_0px_#3f1f60]">M</button>
                        <button type="button" data-action="backspace"
                            class="h-10 px-3 rounded-[10px] bg-[#3f1f60] text-white text-[14px] font-semibold shadow-[3px_3px_0px_#2d1545]">Hapus</button>
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <button type="button" data-action="space"
                            class="h-10 flex-1 rounded-[10px] bg-[#5A2F7E] text-white text-[14px] font-semibold shadow-[3px_3px_0px_#3f1f60]">Spasi</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex w-full justify-end mt-4">
            <button id="quizNextButton" type="button"
                class="inline-flex items-center justify-right rounded-full bg-[#5A2F7E] px-7 py-2 text-white shadow-[0_10px_24px_rgba(90,47,126,0.25)] transition hover:-translate-y-0.5 ml-auto disabled:opacity-60 disabled:cursor-not-allowed">
                <span id="quizNextLabel">Lanjut</span>
                <img src="{{ asset('assets/icons/landing/next.svg') }}" alt="" class="h-[30px] w-auto ml-2">
            </button>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (() => {
            const state = {
                gameId: @json($game->id ?? null),
                playId: @json($play->id ?? null),
                questions: @json($questions ?? []),
                config: @json($config ?? []),
                index: 0,
                answers: {},
                finishing: false,
                timer: null,
                remainingSeconds: 0,
            };

            const promptEl = document.getElementById('quizPrompt');
            const optionsEl = document.getElementById('quizOptions');
            const progressEl = document.getElementById('quizProgress');
            const nextButton = document.getElementById('quizNextButton');
            const nextLabel = document.getElementById('quizNextLabel');
            const textWrapper = document.getElementById('quizTextAnswer');
            const textInput = document.getElementById('quizTextInput');
            const timerEl = document.getElementById('quizTimer');
            const timerValueEl = document.getElementById('quizTimerValue');
            const imageEl = document.getElementById('quizImage');
            const keyboardEl = document.getElementById('quizKeyboard');
            const storageBase = @json(asset('storage'));

            const apiAnswerUrl = @json(url('/api/game/answer'));
            const apiFinishUrl = @json(url('/api/game/finish'));

            if (!state.playId || !state.questions.length) {
                window.location = @json(route('games.result.fail'));
                return;
            }

            const resolveImage = (path) => {
                if (!path) return null;
                if (path.startsWith('http://') || path.startsWith('https://')) return path;
                if (path.startsWith('/')) return path;
                return `${storageBase}/${path}`;
            };

            const formatTimer = (seconds) => {
                const mm = String(Math.floor(seconds / 60)).padStart(2, '0');
                const ss = String(seconds % 60).padStart(2, '0');
                return `${mm}:${ss}`;
            };

            const updateTimer = () => {
                if (state.remainingSeconds <= 0) {
                    clearInterval(state.timer);
                    state.timer = null;
                    finishGame();
                    return;
                }
                state.remainingSeconds -= 1;
                timerValueEl.textContent = formatTimer(state.remainingSeconds);
            };

            const startTimer = () => {
                const limit = parseInt(state.config.time_limit || 0, 10);
                if (!limit || limit <= 0) return;
                state.remainingSeconds = limit;
                timerEl.classList.remove('hidden');
                timerValueEl.textContent = formatTimer(state.remainingSeconds);
                state.timer = setInterval(updateTimer, 1000);
            };

            const setNextDisabled = (disabled) => {
                nextButton.disabled = disabled;
            };

            const insertAtCursor = (input, text) => {
                const start = input.selectionStart ?? input.value.length;
                const end = input.selectionEnd ?? input.value.length;
                const value = input.value;
                input.value = value.slice(0, start) + text + value.slice(end);
                const nextPos = start + text.length;
                input.setSelectionRange(nextPos, nextPos);
                input.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            };

            const backspaceAtCursor = (input) => {
                const start = input.selectionStart ?? input.value.length;
                const end = input.selectionEnd ?? input.value.length;
                if (start !== end) {
                    const value = input.value;
                    input.value = value.slice(0, start) + value.slice(end);
                    input.setSelectionRange(start, start);
                    input.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                    return;
                }
                if (start <= 0) return;
                const value = input.value;
                input.value = value.slice(0, start - 1) + value.slice(end);
                const nextPos = start - 1;
                input.setSelectionRange(nextPos, nextPos);
                input.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            };

            const renderOptions = (question) => {
                optionsEl.innerHTML = '';
                const options = Array.isArray(question.option) ? question.option : [];
                options.forEach((opt) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.dataset.key = opt.key;
                    button.className =
                        'flex items-center gap-4 rounded-[20px] border-2 border-[#5A2F7E] bg-[#f7f1ff] px-5 py-4 text-left shadow-[6px_6px_0px_#5A2F7E] transition hover:-translate-y-1 hover:shadow-[4px_4px_0px_#5A2F7E]';
                    button.innerHTML = `
                        <span class="flex h-12 w-12 items-center justify-center rounded-[12px] text-[18px] font-semibold bg-[#f7f1ff] text-[#5A2F7E] border-2 border-[#5A2F7E]">
                            ${opt.key}
                        </span>
                        <span class="text-[18px] font-semibold text-[#1e132b]">
                            ${opt.text}
                        </span>
                    `;
                    button.addEventListener('click', () => {
                        state.answers[question.id] = opt.key;
                        optionsEl.querySelectorAll('button').forEach((btn) => {
                            btn.classList.remove('ring-4', 'ring-[#5A2F7E]');
                        });
                        button.classList.add('ring-4', 'ring-[#5A2F7E]');
                        setNextDisabled(false);
                    });
                    optionsEl.appendChild(button);
                });
            };

            const renderQuestion = () => {
                const question = state.questions[state.index];
                if (!question) {
                    finishGame();
                    return;
                }

                const progressText = `Soal ${state.index + 1}/${state.questions.length}`;
                progressEl.textContent = progressText;
                promptEl.textContent = question.prompt || '-';

                const imageUrl = resolveImage(question.image_url || '');
                if (imageUrl) {
                    imageEl.src = imageUrl;
                } else {
                    imageEl.removeAttribute('src');
                }

                const isText = question.type === 'text';
                textWrapper.classList.toggle('hidden', !isText);
                optionsEl.classList.toggle('hidden', isText);
                keyboardEl.classList.toggle('hidden', !isText);

                setNextDisabled(true);
                if (isText) {
                    textInput.value = state.answers[question.id] || '';
                    textInput.oninput = () => {
                        state.answers[question.id] = textInput.value;
                        setNextDisabled(textInput.value.trim().length === 0);
                    };
                    setNextDisabled(textInput.value.trim().length === 0);
                } else {
                    textInput.oninput = null;
                    renderOptions(question);
                    const existing = state.answers[question.id];
                    if (existing) {
                        optionsEl.querySelectorAll('button').forEach((btn) => {
                            if (btn.dataset.key === String(existing)) {
                                btn.classList.add('ring-4', 'ring-[#5A2F7E]');
                                setNextDisabled(false);
                            }
                        });
                    }
                }

                nextLabel.textContent = (state.index + 1) === state.questions.length ? 'Selesai' : 'Lanjut';
            };

            const submitAnswer = async (question) => {
                const answer = state.answers[question.id];
                if (answer === undefined || answer === null || String(answer).trim() === '') {
                    return false;
                }
                await fetch(apiAnswerUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        play_id: state.playId,
                        quest_id: question.id,
                        answer: answer,
                    }),
                });
                return true;
            };

            const finishGame = async () => {
                if (state.finishing) return;
                state.finishing = true;
                try {
                    const response = await fetch(apiFinishUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            play_id: state.playId,
                        }),
                    });
                    const data = await response.json();
                    if (response.ok && data.success_url) {
                        window.location = data.success_url;
                        return;
                    }
                    window.location = data.fail_url || @json(route('games.result.fail'));
                } catch (e) {
                    window.location = @json(route('games.result.fail'));
                }
            };

            nextButton.addEventListener('click', async () => {
                const question = state.questions[state.index];
                if (!question) {
                    finishGame();
                    return;
                }

                nextButton.disabled = true;
                await submitAnswer(question);

                if (state.index + 1 >= state.questions.length) {
                    finishGame();
                    return;
                }

                state.index += 1;
                renderQuestion();
            });

            keyboardEl.addEventListener('click', (event) => {
                const target = event.target.closest('button');
                if (!target) return;
                textInput.focus();
                const key = target.dataset.key;
                const action = target.dataset.action;
                if (key) {
                    insertAtCursor(textInput, key);
                    return;
                }
                if (action === 'backspace') {
                    backspaceAtCursor(textInput);
                    return;
                }
                if (action === 'space') {
                    insertAtCursor(textInput, ' ');
                }
            });

            startTimer();
            renderQuestion();
        })();
    </script>
@endpush
