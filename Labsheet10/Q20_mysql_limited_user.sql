-- Q20: Limited-privilege MySQL user for the application (never use root in code)
-- Run as root:  mysql -u root -p < Q20_mysql_limited_user.sql

CREATE DATABASE IF NOT EXISTS myproject CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 1. Create user (host restricted to localhost)
CREATE USER IF NOT EXISTS 'app_user'@'localhost' IDENTIFIED BY 'Str0ng!Passw0rd#2025';

-- 2. Grant ONLY the privileges the app needs, on ONLY this database
GRANT SELECT, INSERT, UPDATE, DELETE ON myproject.* TO 'app_user'@'localhost';

-- (Optional: even tighter, per-table)
-- GRANT SELECT, INSERT, UPDATE, DELETE ON myproject.products TO 'app_user'@'localhost';
-- GRANT SELECT, INSERT, UPDATE         ON myproject.users    TO 'app_user'@'localhost';

-- NOT granted: DROP, CREATE, ALTER, GRANT OPTION, FILE, SUPER, and global privileges

FLUSH PRIVILEGES;

-- 3. Verify
SHOW GRANTS FOR 'app_user'@'localhost';

-- 4. Test from shell:
--    mysql -u app_user -p myproject -e "SELECT 1;"        -> works
--    mysql -u app_user -p -e "DROP DATABASE myproject;"   -> ERROR 1044 access denied

-- 5. Update config/config.php:  'user' => 'app_user', 'pass' => 'Str0ng!Passw0rd#2025'
-- Revoke / remove if needed:
-- REVOKE DELETE ON myproject.* FROM 'app_user'@'localhost';
-- DROP USER 'app_user'@'localhost';
