"""
Question 8:
Implement a Python script to update user profile information in MySQL based on a given user ID.
"""

def update_user_profile(user_id, new_email, new_role):
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

        # Parameterized UPDATE query based on user ID
        update_query = """
        UPDATE users 
        SET email = %s, role = %s 
        WHERE id = %s
        """

        cursor.execute(update_query, (new_email, new_role, user_id))
        conn.commit()

        print("=" * 60)
        if cursor.rowcount > 0:
            print(f"✓ Success: Profile for User ID #{user_id} updated!")
            print(f"  New Email: {new_email}")
            print(f"  New Role : {new_role}")
        else:
            print(f"Notice: No record updated. User ID #{user_id} was not found or data was identical.")
        print("=" * 60)

        cursor.close()

    except Exception as err:
        print("Update Error:", err)
        if conn:
            conn.rollback()

    finally:
        if conn:
            conn.close()

if __name__ == "__main__":
    # Example execution: update user ID 1
    update_user_profile(user_id=1, new_email="aarav.updated@example.com", new_role="superadmin")
