"""
Question 3:
Write a Python script to connect to a MySQL database server and execute a query returning the 
database server version.
"""

def get_mysql_version():
    connection = None
    try:
        # Try importing mysql.connector, fallback to pymysql
        try:
            import mysql.connector
            connection = mysql.connector.connect(
                host="localhost",
                user="root",
                password=""
            )
            connector_name = "mysql.connector"
        except ImportError:
            import pymysql
            connection = pymysql.connect(
                host="localhost",
                user="root",
                password=""
            )
            connector_name = "pymysql"

        # Create database cursor
        cursor = connection.cursor()

        # Execute query returning database server version
        cursor.execute("SELECT VERSION();")

        # Fetch the scalar result
        db_version = cursor.fetchone()

        print("=" * 60)
        print("✓ Successfully connected using:", connector_name)
        print("MySQL Database Server Version:", db_version[0])
        print("=" * 60)

        cursor.close()

    except Exception as error:
        print("=" * 60)
        print("Notice / Connection Exception:", error)
        print("Ensure MySQL service is started (e.g., via XAMPP Control Panel).")
        print("=" * 60)

    finally:
        if connection:
            connection.close()

if __name__ == "__main__":
    get_mysql_version()
