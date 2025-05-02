from flask import Flask, request, jsonify
from flask_cors import CORS
import pytesseract
from PIL import Image
import io
import logging
import re
import cv2
import numpy as np

pytesseract.pytesseract.tesseract_cmd = r"C:\Program Files\Tesseract-OCR\tesseract.exe"

app = Flask(__name__)
CORS(app)

logging.basicConfig(
    level=logging.DEBUG,
    format='%(asctime)s [%(levelname)s] %(message)s',
    handlers=[
        logging.FileHandler("cin_verification.log"),
        logging.StreamHandler()
    ]
)

@app.route('/verify-cin', methods=['POST'])
def verify_cin():
    logging.info("Incoming request to /verify-cin")

    if 'cin_image' not in request.files:
        logging.warning("No file part found in request")
        return jsonify({'error': 'No file part'}), 400

    file = request.files['cin_image']

    if file.filename == '':
        logging.warning("File uploaded with empty filename")
        return jsonify({'error': 'No selected file'}), 400

    try:
        logging.debug("Reading image from uploaded file")
        image_bytes = np.frombuffer(file.read(), np.uint8)
        img = cv2.imdecode(image_bytes, cv2.IMREAD_COLOR)

        # Preprocessing: Convert to grayscale and apply threshold
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        _, thresh = cv2.threshold(gray, 150, 255, cv2.THRESH_BINARY)

        logging.debug("Running Tesseract OCR")
        text = pytesseract.image_to_string(thresh, config='--psm 6')

        logging.info("OCR Text Extracted:\n%s", text)

        # Extract the CIN from the OCR text
        cin = extract_cin(text)
        logging.info("Extracted CIN: %s", cin)

        if cin:
            return jsonify({'cin': cin, 'ocr_text': text}), 200
        else:
            logging.warning("CIN extraction failed from OCR text")
            return jsonify({'error': 'CIN extraction failed', 'ocr_text': text}), 400

    except Exception as e:
        logging.error("Exception occurred during CIN verification: %s", str(e), exc_info=True)
        return jsonify({'error': 'Internal server error: ' + str(e)}), 500

# Regex to extract CIN 
def extract_cin(text):
    matches = re.findall(r'\b[A-Z]\d{6}\b', text)
    return matches[0] if matches else None

if __name__ == '__main__':
    app.run(debug=True)
