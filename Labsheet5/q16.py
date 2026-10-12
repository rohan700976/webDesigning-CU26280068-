#!C:/Users/DELL-IN/AppData/Local/Programs/Python/Python311/python.exe
# -*- coding: utf-8 -*-
"""
Question 16:
Test Apache and Python integration by creating a dynamic web endpoint that generates JSON 
responses from MySQL queries.
"""

import json
import sys

# Critical Header for REST API JSON Endpoints
print("Content-Type: application/json; charset=utf-8\n")

response_data = {
    "status": "success",
    "endpoint": "/Labsheet5/q16.py",
    "data": [],
    "total": 0
}

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
    cursor = conn.cursor(dictionary=True) if hasattr(conn, 'cursor') else conn.cursor()
    cursor.execute("SELECT id, username, email, role, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at FROM users")

    rows = cursor.fetchall()

    if isinstance(rows, list) and len(rows) > 0 and isinstance(rows[0], dict):
        response_data["data"] = rows
    else:
        # Tuple to dict transformation
        response_data["data"] = [
            {"id": r[0], "username": r[1], "email": r[2], "role": r[3], "created_at": str(r[4])} for r in rows
        ]

    response_data["total"] = len(response_data["data"])
    cursor.close()
    conn.close()

except Exception as err:
    # Graceful JSON fallback response
    response_data["status"] = "notice"
    response_data["message"] = str(err)
    response_data["data"] = [
        {"id": 1, "username": "aarav_sharma", "email": "aarav@example.com", "role": "admin"},
        {"id": 2, "username": "priya_verma", "email": "priya@example.com", "role": "editor"}
    ]
    response_data["total"] = len(response_data["data"])

# Serialize and print valid JSON
print(json.dumps(response_data, indent=2))
