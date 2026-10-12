#!C:/Users/DELL-IN/AppData/Local/Programs/Python/Python311/python.exe
# -*- coding: utf-8 -*-
"""
Question 18:
Implement user session handling in a Python web script interacting with MySQL backend 
storage.
"""

import os
import uuid
import datetime
from http import cookies

# Generate or retrieve session ID from client cookies
cookie = cookies.SimpleCookie(os.environ.get("HTTP_COOKIE", ""))
session_id = cookie.get("app_session_id")

is_new_session = False
if not session_id:
    # Issue new unique session token
    session_token = str(uuid.uuid4())
    is_new_session = True
else:
    session_token = session_id.value

# Output Cookie Header if new session created
if is_new_session:
    print(f"Set-Cookie: app_session_id={session_token}; Path=/; HttpOnly")

print("Content-Type: text/html; charset=utf-8\n")

session_data = {"user": "Guest", "last_active": "Just now"}

# Interact with MySQL backend session store
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

    # Create sessions table if not exists
    cursor.execute("""
    CREATE TABLE IF NOT EXISTS user_sessions (
        session_id VARCHAR(100) PRIMARY KEY,
        user_data TEXT,
        last_accessed TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );
    """)

    # Upsert session state into MySQL backend
    cursor.execute("""
    INSERT INTO user_sessions (session_id, user_data) 
    VALUES (%s, %s)
    ON DUPLICATE KEY UPDATE last_accessed = CURRENT_TIMESTAMP;
    """, (session_token, "username=aarav;role=admin"))

    conn.commit()
    cursor.close()
    conn.close()

except Exception as e:
    pass

print(f"""<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 18 - Python MySQL Backend Session Handler</title>
    <style>
        body {{ font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 30px; }}
        .card {{ max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }}
        .badge {{ background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 13px; }}
        code {{ background: #e2e8f0; padding: 4px 8px; border-radius: 4px; font-size: 14px; word-break: break-all; }}
        .info-row {{ margin: 15px 0; }}
    </style>
</head>
<body>
    <div class="card">
        <h2>Python Session Handling with MySQL Backend</h2>
        <span class="badge">Session Active</span>

        <div class="info-row">
            <strong>Session Token (Cookie):</strong><br>
            <code>{session_token}</code>
        </div>

        <div class="info-row">
            <strong>Session Status:</strong> {'Newly Issued Session' if is_new_session else 'Existing Resumed Session'}
        </div>

        <p style="color: #64748b; font-size: 13px;">
            This architecture stores session keys on the client via HTTP cookies, while user state and expiry metadata are stored securely inside the <code>user_sessions</code> MySQL table.
        </p>
    </div>
</body>
</html>
""")
