"""
Question 14:
Develop a Python script that reads CSV file data and bulk-inserts rows into a MySQL database 
table.
"""

import os
import csv

def bulk_insert_from_csv(csv_filepath):
    print("=" * 65)
    print("Bulk Insert CSV Data into MySQL Table")
    print("=" * 65)

    if not os.path.exists(csv_filepath):
        print(f"Error: CSV file not found at {csv_filepath}")
        return

    # Read records from CSV file
    records_to_insert = []
    with open(csv_filepath, mode='r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            records_to_insert.append((
                row['username'].strip(),
                row['email'].strip(),
                row['role'].strip()
            ))

    print(f"Read {len(records_to_insert)} records from CSV: {csv_filepath}")

    conn = None
    try:
        try:
            import mysql.connector as db_driver
        except ImportError:
            import pymysql as db_driver

        conn = db_driver.connect(
            host="localhost",
            user="root",
            password="",
            database="python_web_db"
        )
        cursor = conn.cursor()

        # Parameterized INSERT query using INSERT IGNORE or ON DUPLICATE KEY
        insert_sql = "INSERT INTO users (username, email, role) VALUES (%s, %s, %s)"

        # Perform bulk insertion using executemany()
        cursor.executemany(insert_sql, records_to_insert)
        conn.commit()

        print(f"✓ Successfully bulk-inserted {cursor.rowcount} rows into table `users`!")
        cursor.close()

    except Exception as err:
        print("Bulk Insert Notice / Error:", err)
        if conn:
            conn.rollback()

    finally:
        if conn:
            conn.close()
    print("=" * 65)

if __name__ == "__main__":
    csv_path = os.path.join(os.path.dirname(__file__), "users_data.csv")
    bulk_insert_from_csv(csv_path)
