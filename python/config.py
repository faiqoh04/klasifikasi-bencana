"""
Konfigurasi Project
"""

import warnings
warnings.filterwarnings("ignore")

import numpy as np
import pandas as pd

import matplotlib.pyplot as plt

plt.style.use("ggplot")
plt.rcParams["figure.figsize"] = (8,5)
plt.rcParams["figure.dpi"] = 120

from sqlalchemy import create_engine

# ==========================================
# DATABASE
# ==========================================

DB_USER = "root"
DB_PASSWORD = ""
DB_HOST = "localhost"
DB_PORT = "3306"
DB_NAME = "db_klasifikasi_keparahan_bencana"

DATABASE_URL = (
    f"mysql+pymysql://{DB_USER}:{DB_PASSWORD}"
    f"@{DB_HOST}:{DB_PORT}/{DB_NAME}"
)

engine = create_engine(DATABASE_URL)

# ==========================================
# MODEL PATH
# ==========================================

DT_MODEL = "models/decision_tree.joblib"

RF_MODEL = "models/random_forest.joblib"

LABEL_ENCODER = "models/label_encoder.joblib"