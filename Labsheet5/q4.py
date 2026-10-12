"""
Question 4:
Create a Python script that connects to MySQL and creates a database named `python_web_db` 
with a table `users`.
"""

def create_database_and_table():
    conn = None
    try:
        try:
            import mysql.connector as db_driver
        except ImportError:
            import pymysql as db_driver

        # Step 1: Connect to MySQL server
        conn = db_driver.connect(
            host="localhost",
            user="root",
            password=""
        )
        cursor = conn.cursor()

        # Step 2: Create database `python_web_db`
        cursor.execute("CREATE DATABASE IF NOT EXISTS python_web_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;")
        print("✓ Database `python_web_db` verified/created successfully.")

        # Step 3: Switch to `python_web_db`
        cursor.execute("USE python_web_db;")

        # Step 4: Create table `users`
        create_table_sql = """
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            role VARCHAR(30) DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        """
        cursor.execute(create_table_sql)
        print("✓ Table `users` created successfully with columns (id, username, email, role, created_at).")

        cursor.close()

    except Exception as err:
        print("Database Operation Error:", err)

    finally:
        if conn:
            conn.close()

if __name__ == "__main__":
    create_database_and_table()
