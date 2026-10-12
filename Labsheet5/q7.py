#!C:/Users/DELL-IN/AppData/Local/Programs/Python/Python311/python.exe
# -*- coding: utf-8 -*-
"""
Question 7:
Write a Python CGI script that reads form input data via `cgi` module and inserts the submitted 
data into MySQL.
"""

import cgi
import cgitb

# Enable CGI error debugging
cgitb.enable()

print("Content-Type: text/html; charset=utf-8\n")

# Reading form fields using cgi.FieldStorage()
form = cgi.FieldStorage()

username = form.getvalue("username")
email = form.getvalue("email")
role = form.getvalue("role", "user")

message = ""
status_color = "#2563eb"

if username and email:
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

        sql = "INSERT INTO users (username, email, role) VALUES (%s, %s, %s)"
        cursor.execute(sql, (username, email, role))
        conn.commit()

        message = f"✓ User '{username}' successfully registered and inserted into MySQL via Python CGI!"
        status_color = "#16a34a"

        cursor.close()
        conn.close()

    except Exception as e:
        message = f"Notice / Database Error: {e}"
        status_color = "#dc2626"
else:
    message = "Please fill in the form fields to register a user."

print(f"""<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 7 - Python CGI Form Input &amp; MySQL Insert</title>
    <style>
        body {{ font-family: Arial, sans-serif; background: #f8fafc; padding: 30px; }}
        .card {{ max-width: 500px; margin: auto; background: white; padding: 25px 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }}
        .msg {{ padding: 12px; border-radius: 6px; font-weight: bold; margin-bottom: 20px; color: {status_color}; background: #f1f5f9; }}
        .field {{ margin-bottom: 14px; }}
        label {{ display: block; margin-bottom: 5px; font-weight: bold; }}
        input, select {{ width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; }}
        button {{ background: #2563eb; color: white; border: none; padding: 10px 18px; border-radius: 4px; cursor: pointer; font-weight: bold; }}
    </style>
</head>
<body>
    <div class="card">
        <h2>Python CGI User Registration</h2>
        <div class="msg">{message}</div>

        <form method="POST" action="q7.py">
            <div class="field">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="field">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="field">
                <label for="role">User Role:</label>
                <select id="role" name="role">
                    <option value="user">Standard User</option>
                    <option value="editor">Content Editor</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
            <button type="submit">Submit via Python CGI</button>
        </form>
    </div>
</body>
</html>
""")
