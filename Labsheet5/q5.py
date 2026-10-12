"""
Question 5:
Write a Python script to insert multiple user records into the MySQL database using 
executemany() method.
"""

def insert_multiple_users():
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

        # Parameterized query with placeholders
        insert_sql = "INSERT INTO users (username, email, role) VALUES (%s, %s, %s)"

        # List of user tuples to insert in batch
        user_records = [
            ("aarav_sharma", "aarav.sharma@example.com", "admin"),
            ("priya_verma", "priya.verma@example.com", "editor"),
            ("rohan_patel", "rohan.patel@example.com", "user"),
            ("sneha_gupta", "sneha.gupta@example.com", "user"),
            ("vikram_aditya", "vikram.aditya@example.com", "moderator")
        ]

        # Use executemany() for efficient batch insertion
        cursor.executemany(insert_sql, user_records)

        # Commit transaction to persist changes
        conn.commit()

        print("=" * 60)
        print(f"✓ Successfully inserted {cursor.rowcount} records using cursor.executemany()!")
        print("=" * 60)

        cursor.close()

    except Exception as err:
        print("Insert Exception:", err)
        if conn:
            conn.rollback()

    finally:
        if conn:
            conn.close()

if __name__ == "__main__":
    insert_multiple_users()
