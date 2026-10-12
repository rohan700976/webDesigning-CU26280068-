"""
Question 2:
Install Python MySQL connector libraries (`mysql-connector-python` or `PyMySQL`) in the local 
python environment.

Installation Commands:
----------------------
pip install mysql-connector-python
pip install PyMySQL
pip install cryptography
"""

import sys

def verify_installations():
    print("=" * 60)
    print("Python MySQL Connector Libraries Verification")
    print("Python Interpreter:", sys.executable)
    print("Python Version    :", sys.version.split()[0])
    print("=" * 60)

    # 1. Checking mysql-connector-python
    try:
        import mysql.connector
        print("[INSTALLED] mysql-connector-python version:", mysql.connector.__version__)
    except ImportError:
        print("[MISSING] mysql-connector-python is not installed.")
        print("          Run: pip install mysql-connector-python")

    # 2. Checking PyMySQL
    try:
        import pymysql
        print("[INSTALLED] PyMySQL version:", pymysql.__version__)
    except ImportError:
        print("[MISSING] PyMySQL is not installed.")
        print("          Run: pip install pymysql")

    print("\nRecommended pip installation command:")
    print("pip install mysql-connector-python pymysql")
    print("=" * 60)

if __name__ == "__main__":
    verify_installations()
