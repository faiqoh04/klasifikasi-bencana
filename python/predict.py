"""
PREDICTION MODULE
Digunakan oleh Flask API

Pipeline disesuaikan dengan MODEL_DecisionTree_RandomForest.ipynb terbaru:
- Dataset difilter berdasarkan jenis kejadian sesuai Juklak BNPB
- Train: 2020-2023, Test: 2024-2025 (bukan random split)
- SMOTE hanya pada data training
- Feature: Dead, Missing, Serious Wound, Minor Injuries + damage features (38 total)
- Label: RENDAH, SEDANG, TINGGI
"""

import joblib
import pandas as pd
import numpy as np

from damage_feature_engineering import (
    clean_damage,
    extract_damage_features
)

# ==========================================================
# LOAD MODEL & ARTIFACTS
# ==========================================================

rf_model = joblib.load("models/random_forest.joblib")

label_encoder = joblib.load("models/label_encoder.joblib")

feature_columns = joblib.load("models/feature_columns.joblib")

print("=" * 60)
print("MODEL BERHASIL DIMUAT")
print("  Random Forest   : models/random_forest.joblib")
print(f"  Label Encoder   : {label_encoder.classes_}")
print(f"  Feature Columns : {len(feature_columns)} fitur")
print("=" * 60)


# ==========================================================
# FUNCTION PREDICT
# ==========================================================

def predict_disaster(data):

    # -------------------------------------------------------
    # 1. INPUT: Ambil nilai dari request
    # -------------------------------------------------------
    dead           = int(data.get("dead", 0))
    missing        = int(data.get("missing", 0))
    serious_wound  = int(data.get("serious_wound", 0))
    minor_injuries = int(data.get("minor_injuries", 0))
    damage_raw     = str(data.get("damage", ""))

    # -------------------------------------------------------
    # 2. PREPROCESSING: Sama persis dengan pipeline training
    # -------------------------------------------------------

    # Clip nilai negatif (sesuai training: df[col].clip(lower=0))
    dead           = max(0, dead)
    missing        = max(0, missing)
    serious_wound  = max(0, serious_wound)
    minor_injuries = max(0, minor_injuries)

    # Clean text damage (sesuai training: df["Damage"].fillna("").apply(clean_damage))
    damage_clean = clean_damage(damage_raw)

    # Feature engineering damage (sesuai training: extract_damage_features)
    damage_features = extract_damage_features(damage_clean)

    # -------------------------------------------------------
    # 3. GABUNG SEMUA FEATURE
    # -------------------------------------------------------
    feature = {
        "Dead":           dead,
        "Missing":        missing,
        "Serious Wound":  serious_wound,
        "Minor Injuries": minor_injuries,
        **damage_features
    }

    # -------------------------------------------------------
    # 4. BUAT DATAFRAME DAN SUSUN KOLOM
    # Harus sama dengan X_train_main.columns (feature_columns.joblib)
    # -------------------------------------------------------
    df = pd.DataFrame([feature])

    # Pastikan semua kolom yang dibutuhkan ada (isi 0 jika tidak ada)
    for col in feature_columns:
        if col not in df.columns:
            df[col] = 0

    # Pilih dan urutkan kolom sesuai feature_columns.joblib
    df = df[feature_columns]

    # -------------------------------------------------------
    # LOGGING/DEBUGGING (Sebelum melakukan prediksi)
    # -------------------------------------------------------
    print("=" * 60)
    print("DEBUG LOG PREDIKSI:")
    print("1. Data input yang diterima dari form:")
    print(f"   - dead: {dead}")
    print(f"   - missing: {missing}")
    print(f"   - serious_wound: {serious_wound}")
    print(f"   - minor_injuries: {minor_injuries}")
    print(f"   - damage: '{damage_raw}'")
    print("2. Hasil feature engineering Damage:")
    print(f"   - {damage_features}")
    print("3. Seluruh feature yang dikirim ke model (input_df):")
    print(df.to_dict(orient="records")[0])
    print("4. Urutan kolom (input_df.columns):")
    print(list(df.columns))
    print("5. Isi feature_columns:")
    print(feature_columns)
    
    # Validasi kesamaan
    columns_match = list(df.columns) == list(feature_columns)
    print(f"6. Pastikan input_df.columns sama persis dengan feature_columns: {columns_match}")
    print("=" * 60)

    # -------------------------------------------------------
    # 5. PREDIKSI dengan Random Forest
    # -------------------------------------------------------
    pred_encoded = rf_model.predict(df)[0]
    prob_array   = rf_model.predict_proba(df)[0]

    # Decode label
    prediction = label_encoder.inverse_transform([pred_encoded])[0]

    # Buat dict probabilitas per kelas dengan menyelaraskan rf_model.classes_ ke label asli
    probability_result = {}
    for idx, class_code in enumerate(rf_model.classes_):
        class_label = label_encoder.inverse_transform([class_code])[0]
        probability_result[class_label] = round(float(prob_array[idx]), 4)

    # -------------------------------------------------------
    # 6. KEMBALIKAN HASIL
    # -------------------------------------------------------
    return {
        "prediction":  prediction,
        "probability": probability_result,
        "feature":     damage_features,
        "input": {
            "dead":           dead,
            "missing":        missing,
            "serious_wound":  serious_wound,
            "minor_injuries": minor_injuries,
            "damage":         damage_clean
        }
    }