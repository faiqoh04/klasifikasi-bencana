"""
ETL DATA BPBD KE MYSQL
Gunakan skrip ini untuk mengimpor ulang data Excel ke database.
Jalankan dari folder python/:  python etl.py
"""

import pandas as pd

# Gunakan koneksi DB dari config.py (tidak duplikat)
from config import engine

# ==========================
# FILE EXCEL
# ==========================

EXCEL_FILE = "../kejadian bencana_2020-2025.xlsx"

# ==========================
# LOAD DATA
# ==========================

frames = []

for year in range(2020, 2026):

    print(f"Membaca sheet {year}")

    df = pd.read_excel(
        EXCEL_FILE,
        sheet_name=str(year)
    )

    df["Year"] = year

    frames.append(df)

data = pd.concat(frames, ignore_index=True)

print("Total Data :", len(data))

# ==========================
# CLEANING
# ==========================

numeric_columns = [
    "Dead",
    "Missing",
    "Serious Wound",
    "Minor Injuries"
]

for col in numeric_columns:

    data[col] = pd.to_numeric(
        data[col],
        errors="coerce"
    ).fillna(0)

if "Losses" in data.columns:

    data["Losses"] = (
        data["Losses"]
        .astype(str)
        .str.replace(",", "", regex=False)
    )

    data["Losses"] = pd.to_numeric(
        data["Losses"],
        errors="coerce"
    )

data["Damage"] = data["Damage"].fillna("")

# ==========================
# RENAME
# ==========================

rename_columns = {
    "ID Logs":           "bpbd_log_id",
    "Disaster Type":     "disaster_type",
    "Event Date":        "event_date",
    "Regency":           "regency",
    "Area":              "area",
    "Latitude":          "latitude",
    "Longitude":         "longitude",
    "Weather":           "weather",
    "Chronology":        "chronology",
    "Dead":              "dead",
    "Missing":           "missing",
    "Serious Wound":     "serious_wound",
    "Minor Injuries":    "minor_injuries",
    "Damage":            "damage",
    "Losses":            "losses",
    "Response":          "response",
    "Photos":            "photos",
    "Source":            "source",
    "Status":            "status",
    "Level":             "level_bpbd"
}

data.rename(
    columns=rename_columns,
    inplace=True
)

# ==========================
# SIMPAN MYSQL
# ==========================

print("Import Database...")

data.to_sql(
    "historical_disasters",
    con=engine,
    if_exists="append",
    index=False
)

print()
print("="*50)
print("IMPORT BERHASIL")
print("="*50)
print("Jumlah Data :", len(data))