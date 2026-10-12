"""
Question 12:
Implement connection pooling in Python for efficient database interaction under high Apache 
server concurrency.
"""

def test_connection_pooling():
    print("=" * 65)
    print("Database Connection Pooling Implementation (High Concurrency)")
    print("=" * 65)

    try:
        from mysql.connector import pooling

        # Configure Connection Pool
        dbconfig = {
            "host": "localhost",
            "user": "root",
            "password": "",
            "database": "python_web_db"
        }

        # Initialize thread-safe connection pool with 5 reusable connections
        cnx_pool = pooling.MySQLConnectionPool(
            pool_name="web_app_pool",
            pool_size=5,
            pool_reset_session=True,
            **dbconfig
        )

        print(f"✓ Initialized connection pool: '{cnx_pool.pool_name}' with size: {cnx_pool.pool_size}")

        # Worker simulation borrowing connections concurrently
        for req_id in range(1, 4):
            # Borrow connection from pool
            connection = cnx_pool.get_connection()
            cursor = connection.cursor()
            cursor.execute("SELECT DATABASE(), NOW();")
            db_name, server_time = cursor.fetchone()
            print(f"  [Request {req_id}] Handled using pooled connection. DB: {db_name} | Time: {server_time}")
            cursor.close()
            # Closing the connection returns it back to the pool rather than terminating TCP socket
            connection.close()

        print("\n✓ All pooled connections returned successfully to the pool.")

    except ImportError:
        print("[DEMO / ARCHITECTURE PATTERN]: mysql.connector.pooling")
        print("To run with connection pool:")
        print("from mysql.connector import pooling")
        print("pool = pooling.MySQLConnectionPool(pool_name='app_pool', pool_size=10, ...)")
    except Exception as err:
        print("Connection Pool Notice:", err)

    print("=" * 65)

if __name__ == "__main__":
    test_connection_pooling()
