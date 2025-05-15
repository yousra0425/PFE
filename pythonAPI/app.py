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

        # Preprocessing: Convert to grayscale and threshold
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        gray = cv2.medianBlur(gray, 3)
        _, thresh = cv2.threshold(gray, 150, 255, cv2.THRESH_BINARY)

        logging.debug("Running Tesseract OCR")
        text = pytesseract.image_to_string(thresh, config='--psm 6')

        logging.info("OCR Text Extracted:\n%s", text)

        # Extract CIN and name
        cin = extract_cin(text)
        name_data = extract_name(text)

        logging.info("Extracted CIN: %s", cin)
        logging.info("Extracted Name: %s", name_data)

        if cin and name_data:
            return jsonify({
                'cin': cin,
                'first_name': name_data['first_name'],
                'last_name': name_data['last_name'],
                'ocr_text': text
            }), 200
        elif cin:
            return jsonify({
                'cin': cin,
                'first_name': None,
                'last_name': None,
                'ocr_text': text
            }), 200
        else:
            logging.warning("CIN extraction failed from OCR text")
            return jsonify({'error': 'CIN extraction failed', 'ocr_text': text}), 400

    except Exception as e:
        logging.error("Exception during CIN verification: %s", str(e), exc_info=True)
        return jsonify({'error': 'Internal server error: ' + str(e)}), 500

# Extract CIN using regex
def extract_cin(text):
    matches = re.findall(r'\b[A-Z]\d{6}\b', text)
    return matches[0] if matches else None

# Clean name fields
def clean_name(text):
    return re.sub(r'[^A-Za-zÀ-ÿ\'\- ]', '', text).strip().title()

# Extract first and last name based on common CIN patterns
def extract_name(text):
    lines = [line.strip() for line in text.split('\n') if line.strip()]
    name = {"first_name": "", "last_name": ""}

    for line in lines:
        cleaned = line.strip()

        # Attempt to detect last name after '=' or 'Nom' or other patterns
        if re.search(r'(?i)\b(nom|me)\b\s*[:=]\s*(\w+)', cleaned):
            match = re.search(r'(?i)\b(nom|me)\b\s*[:=]\s*(\w+)', cleaned)
            name["last_name"] = match.group(2).title()

        # Attempt to find first name on a line with only a name (all caps or near it)
        elif re.match(r'^[;:\-]*\s*([A-Z][a-zA-Z]{2,})\s*$', cleaned):
            candidate = re.sub(r'^[;:\-]*\s*', '', cleaned)
            if not name["first_name"]:
                name["first_name"] = candidate.title()

    if name["first_name"] and name["last_name"]:
        return name
    else:
        return None



if __name__ == '__main__':
    app.run(debug=True)
