"""
FLASK API
Klasifikasi Keparahan Bencana
"""

from flask import Flask, request, jsonify
from flask_cors import CORS

from predict import predict_disaster

app = Flask(__name__)

CORS(app)


# ==========================================================
# HOME
# ==========================================================

@app.route("/", methods=["GET"])
def home():

    return jsonify({

        "status": "success",

        "message": "API Klasifikasi Keparahan Bencana Aktif"

    })


# ==========================================================
# PREDICT
# ==========================================================

@app.route("/predict", methods=["POST"])
def predict():

    try:

        data = request.get_json()

        result = predict_disaster(data)

        return jsonify({

            "status": "success",

            "data": result

        })

    except Exception as e:

        return jsonify({

            "status": "error",

            "message": str(e)

        }), 500


# ==========================================================
# MAIN
# ==========================================================

if __name__ == "__main__":

    app.run(

        host="127.0.0.1",

        port=5000,

        debug=True

    )