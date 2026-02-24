from flask import Flask, request, jsonify
import serial
import threading
import time

app = Flask(__name__)

SERIAL_PORT = "COM5"
BAUDRATE = 9600
READ_TIMEOUT = 1.0
MOTOR_SPIN_SECONDS = 1.5

lock = threading.Lock()

ser = serial.Serial(
    port=SERIAL_PORT,
    baudrate=BAUDRATE,
    bytesize=serial.EIGHTBITS,
    parity=serial.PARITY_NONE,
    stopbits=serial.STOPBITS_ONE,
    timeout=READ_TIMEOUT,
)

def send_motor(cell_id: str) -> dict:
    if cell_id not in ["A", "B", "C", "D", "E", "F"]:
        return {"ok": False, "error": "invalid cell_id"}

    cmd_bytes = bytes([ord(cell_id), ord("A"), 0x0D])

    with lock:
        ser.reset_input_buffer()
        ser.write(cmd_bytes)
        ser.flush()

        # controller "merespon jika perintah sudah diterima"
        resp = ser.readline()  # baca sampai newline atau timeout (tergantung controller)
        resp_raw = resp.hex() if resp else ""

        # motor berputar 1 putaran (dokumen pakai sleep)
        time.sleep(MOTOR_SPIN_SECONDS)

    # Karena format respons tidak dijelaskan di dokumen,
    # kita anggap "ada respons" = accepted. Kalau ternyata device tidak newline,
    # kita bisa ganti strategi baca (read(size) / read_until / dll).
    if resp:
        return {"ok": True, "accepted": True, "resp_raw_hex": resp_raw}
    else:
        # bisa jadi tetap accepted tapi device tidak mengirim newline;
        # ini perlu dites di lapangan.
        return {"ok": True, "accepted": False, "resp_raw_hex": "" , "warning": "no response (timeout)"}

@app.post("/dispense")
def dispense():
    data = request.get_json(force=True)
    transaction_id = data.get("transaction_id")
    cell_id = data.get("cell_id")

    result = send_motor(cell_id)
    result["transaction_id"] = transaction_id
    result["cell_id"] = cell_id
    return jsonify(result), (200 if result.get("ok") else 400)

@app.get("/health")
def health():
    return jsonify({"ok": True, "port": SERIAL_PORT, "baudrate": BAUDRATE})

if __name__ == "__main__":
    app.run(host="127.0.0.1", port=9000)
