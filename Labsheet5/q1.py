#!C:/Users/DELL-IN/AppData/Local/Programs/Python/Python311/python.exe
# -*- coding: utf-8 -*-
"""
Question 1:
Configure Apache web server to execute Python CGI scripts or WSGI applications (mod_wsgi) 
and verify with a 'Hello World' Python script.

Apache Configuration Instructions (httpd.conf):
------------------------------------------------
1. Enable CGI module in Apache:
   LoadModule cgi_module modules/mod_cgi.so

2. Add Handler for .py files in <Directory>:
   <Directory "C:/xampp/htdocs">
       Options +ExecCGI
       AddHandler cgi-script .py
   </Directory>
"""

# Required HTTP Header for CGI scripts
print("Content-Type: text/html; charset=utf-8\n")

# HTML Response output
html_content = """<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 1 - Python CGI Hello World</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 40px; color: #1e293b; }
        .card { max-width: 650px; margin: auto; background: white; padding: 35px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); text-align: center; }
        h1 { color: #2563eb; margin-bottom: 10px; }
        .badge { display: inline-block; background: #dcfce7; color: #166534; padding: 6px 14px; border-radius: 20px; font-weight: bold; margin-bottom: 20px; }
        pre { background: #1e293b; color: #38bdf8; padding: 15px; border-radius: 6px; text-align: left; font-size: 13px; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Hello World from Python CGI!</h1>
        <div class="badge">&#10003; Python CGI Execution Verified</div>
        <p>Apache web server successfully executed this script via Python CGI / WSGI gateway.</p>

        <h3>Shebang Line &amp; CGI Header:</h3>
        <pre>#!C:/Python311/python.exe
# Header required for web browsers:
print("Content-Type: text/html; charset=utf-8\\n")
print("&lt;h1&gt;Hello World&lt;/h1&gt;")</pre>
    </div>
</body>
</html>
"""

print(html_content)
