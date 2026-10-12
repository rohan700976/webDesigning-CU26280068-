"""
Question 19:
Write a Python script that verifies and sanitizes incoming web form inputs before executing SQL 
write operations.
"""

import re
import html

def sanitize_and_validate(raw_username, raw_email, raw_role):
    """
    Sanitizes and validates incoming web form inputs.
    Returns (is_valid, sanitized_data, error_messages)
    """
    errors = []

    # 1. Strip whitespace
    clean_username = raw_username.strip() if raw_username else ""
    clean_email = raw_email.strip() if raw_email else ""
    clean_role = raw_role.strip() if raw_role else "user"

    # 2. HTML escaping to prevent Cross-Site Scripting (XSS)
    safe_username = html.escape(clean_username)

    # 3. Validation: Username format (3 to 30 alphanumeric + underscores)
    if not re.match(r"^[a-zA-Z0-9_]{3,30}$", clean_username):
        errors.append("Username must be between 3 and 30 characters and contain only letters, numbers, and underscores.")

    # 4. Validation: RFC compliant Email regex
    email_regex = r"^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$"
    if not re.match(email_regex, clean_email):
        errors.append("Invalid email address format.")

    # 5. Validation: Whitelist valid roles
    valid_roles = ["user", "editor", "moderator", "admin"]
    if clean_role.lower() not in valid_roles:
        errors.append(f"Invalid role. Allowed values: {', '.join(valid_roles)}")

    if errors:
        return False, None, errors

    sanitized = {
        "username": clean_username,
        "email": clean_email.lower(),
        "role": clean_role.lower()
    }
    return True, sanitized, []

def execute_safe_sql_write(raw_input):
    print("=" * 65)
    print("Input Sanitization, Validation & Safe SQL Write Pipeline")
    print("=" * 65)
    print("Raw Input Received:", raw_input)

    is_valid, clean_data, errors = sanitize_and_validate(
        raw_input.get("username"),
        raw_input.get("email"),
        raw_input.get("role")
    )

    if not is_valid:
        print("\n[VALIDATION FAILED]: Input rejected before touching the database!")
        for err in errors:
            print(f"  ✗ {err}")
        print("=" * 65)
        return False

    print("\n[VALIDATION PASSED]: Sanitized data prepared:")
    print(" ", clean_data)

    # Safe execution with parameterized placeholder queries
    sql = "INSERT INTO users (username, email, role) VALUES (%s, %s, %s)"
    params = (clean_data["username"], clean_data["email"], clean_data["role"])
    print(f"\nReady to execute parameterized query:\n  {sql}\n  with parameters {params}")
    print("=" * 65)
    return True

if __name__ == "__main__":
    # Test with malicious / dirty input
    dirty_input = {
        "username": "<script>alert('xss')</script>",
        "email": "not-an-email",
        "role": "hacker"
    }
    execute_safe_sql_write(dirty_input)

    print()

    # Test with valid clean input
    clean_test_input = {
        "username": "rohit_sharma",
        "email": "rohit.sharma@example.com",
        "role": "editor"
    }
    execute_safe_sql_write(clean_test_input)
