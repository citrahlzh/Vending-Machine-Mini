from flask import Flask, request, jsonify
import serial
from serial.tools import list_ports
import threading
import time

app = Flask(__name__)

BAUDRATE = 9600
READ_TIMEOUT = 1.0
MOTOR_SPIN_SECONDS = 1.5

lock = threading.Lock()

# global serial connection
ser = None


def find_serial_port():
    """
    Cari COM port otomatis.
    Prioritas:
    - USB Serial
    - CH340
    - CP210x
    - FTDI
    """

    ports = list_ports.comports()

    print("=== AVAILABLE PORTS ===", flush=True)

    for port in ports:

        print(f"{port.device} - {port.description}", flush=True)

        desc = port.description.lower()

        if (
            "usb" in desc
            or "serial" in desc
            or "ch340" in desc
            or "cp210" in desc
            or "ftdi" in desc
        ):
            return port.device

    return None


def get_serial():
    """
    Ambil serial connection.
    Kalau belum connect, connect dulu.
    """

    global ser

    # kalau serial masih valid
    if ser is not None:
        try:
            if ser.is_open:
                return ser
        except:
            ser = None

    port_name = find_serial_port()

    if not port_name:
        raise Exception("Serial device not found")

    print(f"Connecting to {port_name}", flush=True)

    ser = serial.Serial(
        port=port_name,
        baudrate=BAUDRATE,
        bytesize=serial.EIGHTBITS,
        parity=serial.PARITY_NONE,
        stopbits=serial.STOPBITS_ONE,
        timeout=READ_TIMEOUT,
    )

    time.sleep(1)

    print(f"Connected to {port_name}", flush=True)

    return ser


def send_motor(cell_id: str) -> dict:

    if cell_id not in ["A", "B", "C", "D", "E", "F"]:
        return {"ok": False, "error": "invalid cell_id"}

    cmd_bytes = bytes([ord(cell_id), ord("A"), 0x0D])

    try:

        with lock:

            ser_conn = get_serial()

            ser_conn.reset_input_buffer()

            print(f"Sending command: {cmd_bytes}", flush=True)

            ser_conn.write(cmd_bytes)
            ser_conn.flush()

            # baca response
            resp = ser_conn.readline()

            resp_raw = resp.hex() if resp else ""

            print(f"Response: {resp_raw}", flush=True)

            # tunggu motor selesai
            time.sleep(MOTOR_SPIN_SECONDS)

        if resp:
            return {
                "ok": True,
                "accepted": True,
                "resp_raw_hex": resp_raw
            }
        else:
            return {
                "ok": True,
                "accepted": False,
                "resp_raw_hex": "",
                "warning": "no response (timeout)"
            }

    except Exception as e:

        # reset serial kalau error
        try:
            if ser and ser.is_open:
                ser.close()
        except:
            pass

        return {
            "ok": False,
            "error": str(e)
        }


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

    detected_port = find_serial_port()

    return jsonify({
        "ok": True,
        "port": detected_port,
        "baudrate": BAUDRATE
    })


if __name__ == "__main__":

    print("Starting Flask API...", flush=True)

    app.run(
        host="127.0.0.1",
        port=9000
    )
