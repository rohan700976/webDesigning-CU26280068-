"""
Question 13:
Write a Python script to log database error messages and access timestamps into a dedicated 
server log file.
"""

import os
import datetime
import traceback

# Dedicated server log file
LOG_FILE_PATH = os.path.join(os.path.dirname(__file__), "server_db.log")

def write_log(level, message, details=None):
    """Writes formatted timestamped log entries to dedicated log file."""
    timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    log_entry = f"[{timestamp}] [{level.upper()}] {message}"
    if details:
        log_entry += f"\n  Details: {details}"
    log_entry += "\n"

    with open(LOG_FILE_PATH, "a", encoding="utf-8") as f:
        f.write(log_entry)
    
    print(log_entry.strip())

def simulate_database_access():
    print(f"Logging actions into: {LOG_FILE_PATH}\n" + "=" * 60)

    # 1. Log valid access timestamp
    write_log("INFO", "Database query executed: SELECT * FROM users")

    # 2. Simulate error to test error logging
    try:
        try:
            import mysql.connector as db_driver
        except ImportError:
            import pymysql as db_driver

        # Deliberately attempt invalid database to capture error
        conn = db_driver.connect(
            host="localhost",
            user="root",
            password="",
            database="non_existent_db_xyz"
        )
    except Exception as exc:
        # Logging error message with stack context
        error_msg = f"Database operation failed: {exc}"
        write_log("ERROR", error_msg, details=traceback.format_exc().strip().splitlines()[-1])

    # 3. Log successful cleanup
    write_log("INFO", "Server access routine completed.")
    print("=" * 60)
    print(f"Log entries written to file: {os.path.abspath(LOG_FILE_PATH)}")

if __name__ == "__main__":
    simulate_database_access()
