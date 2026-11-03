#!/bin/bash
# Q18: Self-signed SSL/TLS certificate on Apache + enable HTTPS (Ubuntu/Debian)

# 1. Generate key + certificate (valid 1 year)
sudo mkdir -p /etc/ssl/myproject
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/myproject/myproject.key \
  -out    /etc/ssl/myproject/myproject.crt \
  -subj "/C=IN/ST=Rajasthan/L=Roorkee/O=MyProject/CN=myproject.local" \
  -addext "subjectAltName=DNS:myproject.local"
sudo chmod 600 /etc/ssl/myproject/myproject.key

# 2. Enable SSL module
sudo a2enmod ssl rewrite headers

# 3. Create HTTPS virtual host: /etc/apache2/sites-available/myproject-ssl.conf
sudo tee /etc/apache2/sites-available/myproject-ssl.conf > /dev/null <<'CONF'
<VirtualHost *:443>
    ServerName myproject.local
    DocumentRoot /var/www/myproject/public

    SSLEngine on
    SSLCertificateFile    /etc/ssl/myproject/myproject.crt
    SSLCertificateKeyFile /etc/ssl/myproject/myproject.key

    <Directory /var/www/myproject/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog  ${APACHE_LOG_DIR}/myproject_ssl_error.log
    CustomLog ${APACHE_LOG_DIR}/myproject_ssl_access.log combined
</VirtualHost>
CONF

# 4. Enable site, test config, restart
sudo a2ensite myproject-ssl.conf
sudo apache2ctl configtest
sudo systemctl restart apache2

# 5. Verify
curl -k -I https://myproject.local
# Browser will warn "not trusted" (self-signed) -> click Advanced -> Proceed.

# --- XAMPP/Windows equivalent ---
# Edit C:\xampp\apache\conf\extra\httpd-ssl.conf, set SSLCertificateFile/KeyFile paths,
# make sure "LoadModule ssl_module" is enabled in httpd.conf and "Include conf/extra/httpd-ssl.conf" is uncommented.
