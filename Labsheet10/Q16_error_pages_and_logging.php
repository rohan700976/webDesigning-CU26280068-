<?php
// Q16: Custom 404 / 500 pages + error logging, errors hidden from visitors

/* ---------- config/error_handling.php (include first in index.php) ---------- */
ini_set('display_errors', '0');            // never show to visitors
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/php_error.log');
error_reporting(E_ALL);

set_exception_handler(function (Throwable $e) {
    error_log(sprintf("[%s] %s in %s:%d\n%s", date('c'), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString()));
    http_response_code(500);
    require __DIR__ . '/../views/errors/500.php';
});
set_error_handler(function ($no, $str, $file, $line) {
    throw new ErrorException($str, 0, $no, $file, $line);   // route to exception handler
});
register_shutdown_function(function () {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        require __DIR__ . '/../views/errors/500.php';
    }
});

/* Trigger 404 from the router:
   http_response_code(404); require __DIR__.'/../views/errors/404.php'; exit;
*/

/* ---------- views/errors/404.php ----------
<!DOCTYPE html><html><head><title>404 Not Found</title></head>
<body style="font-family:sans-serif;text-align:center;margin-top:10%">
<h1>404</h1><p>Sorry, the page you are looking for does not exist.</p><a href="/">Go Home</a>
</body></html>
*/

/* ---------- views/errors/500.php ----------
<!DOCTYPE html><html><head><title>500 Server Error</title></head>
<body style="font-family:sans-serif;text-align:center;margin-top:10%">
<h1>500</h1><p>Something went wrong on our side. Please try again later.</p><a href="/">Go Home</a>
</body></html>
*/

/* ---------- Apache side (in .htaccess or virtual host) ----------
ErrorDocument 404 /errors/404.html
ErrorDocument 500 /errors/500.html
# Apache error log (virtual host):  ErrorLog ${APACHE_LOG_DIR}/myproject_error.log
#                                   CustomLog ${APACHE_LOG_DIR}/myproject_access.log combined
# php.ini production: display_errors=Off, log_errors=On, error_log=/var/log/php_errors.log
*/
