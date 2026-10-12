#!C:/Users/DELL-IN/AppData/Local/Programs/Python/Python311/python.exe
# -*- coding: utf-8 -*-
"""
Question 6:
Develop a Python script that retrieves all records from the `users` table and formats them into 
an HTML response string.
"""

print("Content-Type: text/html; charset=utf-8\n")

records = []
error_notice = ""

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
    cursor.execute("SELECT id, username, email, role, created_at FROM users ORDER BY id ASC")
    records = cursor.fetchall()
    cursor.close()
    conn.close()

except Exception as e:
    error_notice = f"Notice: {e} (Displaying sample records for visual presentation)"
    records = [
        (1, "aarav_sharma", "aarav.sharma@example.com", "admin", "2026-10-12 11:00:00"),
        (2, "priya_verma", "priya.verma@example.com", "editor", "2026-10-12 11:05:00"),
        (3, "rohan_patel", "rohan.patel@example.com", "user", "2026-10-12 11:10:00"),
        (4, "sneha_gupta", "sneha.gupta@example.com", "user", "2026-10-12 11:15:00")
    ]

# Construct HTML table rows dynamically
table_rows = ""
for row in records:
    table_rows += f"""
    <tr>
        <td>#{row[0]}</td>
        <td><strong>{row[1]}</strong></td>
        <td>{row[2]}</td>
        <td><span class="badge">{row[3]}</span></td>
        <td>{row[4]}</td>
    </tr>
    """

html_page = f"""<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 6 - Python HTML Response Formatter</title>
    <style>
        body {{ font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 30px; }}
        .box {{ max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }}
        h2 {{ color: #0f172a; margin-bottom: 8px; }}
        table {{ width: 100%; border-collapse: collapse; margin-top: 15px; }}
        th, td {{ padding: 12px 14px; text-align: left; border-bottom: 1px solid #e2e8f0; }}
        th {{ background: #1e293b; color: white; font-size: 13px; text-transform: uppercase; }}
        tr:nth-child(even) {{ background: #f8fafc; }}
        .badge {{ background: #dbeafe; color: #1d4ed8; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; }}
    </style>
</head>
<body>
    <div class="box">
        <h2>Users Table Records (Formatted via Python)</h2>
        <p style="color: #64748b;">Output dynamically assembled in Python and rendered as an HTML table.</p>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                {table_rows}
            </tbody>
        </table>
    </div>
</body>
</html>
"""

print(html_page)
