"""
Question 15:
Write a Python script utilizing SQLAlchemy ORM to map Python classes to MySQL database 
tables.

Prerequisites:
--------------
pip install sqlalchemy pymysql
"""

try:
    from sqlalchemy import create_engine, Column, Integer, String, DateTime
    from sqlalchemy.orm import declarative_base, sessionmaker
    import datetime

    # Step 1: Base declaration
    Base = declarative_base()

    # Step 2: Define Python ORM class mapped to `users` table
    class User(Base):
        __tablename__ = 'users'

        id = Column(Integer, primary_key=True, autoincrement=True)
        username = Column(String(50), nullable=False, unique=True)
        email = Column(String(100), nullable=False, unique=True)
        role = Column(String(30), default='user')
        created_at = Column(DateTime, default=datetime.datetime.utcnow)

        def __repr__(self):
            return f"<User(id={self.id}, username='{self.username}', role='{self.role}')>"

    # Step 3: Database Engine Connection String
    # Format: mysql+pymysql://<user>:<password>@<host>/<dbname>
    DATABASE_URL = "mysql+pymysql://root:@localhost/python_web_db"

    def run_orm_demo():
        print("=" * 65)
        print("SQLAlchemy ORM Mapping Demonstration")
        print("=" * 65)

        engine = create_engine(DATABASE_URL, echo=False)

        # Create table schema if not already present
        Base.metadata.create_all(engine)

        # Create session
        Session = sessionmaker(bind=engine)
        session = Session()

        try:
            # Querying using ORM
            print("Querying users via SQLAlchemy ORM:")
            users = session.query(User).limit(5).all()
            for u in users:
                print(f"  {u}")

        except Exception as e:
            print("ORM Query Notice:", e)
        finally:
            session.close()
        print("=" * 65)

    if __name__ == "__main__":
        run_orm_demo()

except ImportError:
    print("=" * 65)
    print("SQLAlchemy ORM Model Architecture Snippet")
    print("=" * 65)
    print("""
from sqlalchemy import create_engine, Column, Integer, String
from sqlalchemy.orm import declarative_base, sessionmaker

Base = declarative_base()

class User(Base):
    __tablename__ = 'users'
    id = Column(Integer, primary_key=True)
    username = Column(String(50), nullable=False)
    email = Column(String(100), nullable=False)
    role = Column(String(30), default='user')

# Engine & Session:
engine = create_engine("mysql+pymysql://root:@localhost/python_web_db")
Session = sessionmaker(bind=engine)
session = Session()
users = session.query(User).all()
""")
    print("Install SQLAlchemy via: pip install sqlalchemy pymysql")
    print("=" * 65)
