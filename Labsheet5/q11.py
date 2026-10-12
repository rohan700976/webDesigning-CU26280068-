"""
Question 11:
Write a Python script that connects to MySQL, performs a parameterized query to prevent SQL 
injection, and returns filtered results.
"""

def query_users_filtered(role_filter, search_keyword):
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
        cursor = conn.cursor(dictionary=True) if hasattr(conn, 'cursor') else conn.cursor()

        # SECURE: Using parameterized placeholders (%s) to neutralize SQL injection
        sql = """
        SELECT id, username, email, role, created_at 
        FROM users 
        WHERE role = %s AND username LIKE %s
        ORDER BY id ASC;
        """

        # Form formatted parameters safely
        like_pattern = f"%{search_keyword}%"
        params = (role_filter, like_pattern)

        print("=" * 65)
        print(f"Executing Parameterized Query with filters: role='{role_filter}', keyword='{search_keyword}'")
        cursor.execute(sql, params)

        results = cursor.fetchall()

        print(f"Total matching records found: {len(results)}")
        print("-" * 65)
        for user in results:
            print(f"User #{user.get('id', user[0])}: {user.get('username', user[1])} | Email: {user.get('email', user[2])} | Role: {user.get('role', user[3])}")
        print("=" * 65)

        cursor.close()

    except Exception as e:
        print("Query Execution Notice:", e)
        print("Parameterized queries ensure that even inputs containing \"' OR '1'='1\" are treated safely as raw literal strings.")

    finally:
        if conn:
            conn.close()

if __name__ == "__main__":
    query_users_filtered(role_filter="user", search_keyword="sh")
