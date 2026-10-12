"""
Question 9:
Write a Python script to delete a record from the MySQL database and handle database 
exceptions using `try-except` blocks.
"""

def delete_user_record(user_id):
    conn = None
    try:
        try:
            import mysql.connector as db_driver
            from mysql.connector import Error as DBError
        except ImportError:
            import pymysql as db_driver
            from pymysql import Error as DBError

        print(f"[TRY]: Connecting to MySQL to delete record with ID #{user_id}...")
        conn = db_driver.connect(
            host="localhost",
            user="root",
            password="",
            database="python_web_db"
        )
        cursor = conn.cursor()

        # Parameterized delete statement
        delete_sql = "DELETE FROM users WHERE id = %s"
        cursor.execute(delete_sql, (user_id,))

        conn.commit()

        if cursor.rowcount > 0:
            print(f"[SUCCESS]: Record #{user_id} deleted successfully from table `users`.")
        else:
            print(f"[NOTICE]: No record found with ID #{user_id} to delete.")

        cursor.close()

    # Handling database exceptions specifically
    except DBError as db_err:
        print("[DATABASE EXCEPTION CAUGHT]:", db_err)
        if conn:
            conn.rollback()

    # Catching general application errors
    except Exception as general_err:
        print("[GENERAL EXCEPTION CAUGHT]:", general_err)

    finally:
        # Closing resource safely
        if conn and conn.is_connected() if hasattr(conn, 'is_connected') else conn:
            conn.close()
            print("[FINALLY]: Database connection closed gracefully.")

if __name__ == "__main__":
    delete_user_record(user_id=5)
