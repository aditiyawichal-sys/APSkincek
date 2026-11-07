<?php
// DB Params
define('DB_HOST', 'localhost');
define('DB_USER', 'postgres');
define('DB_PASS', 'your_password'); // Change to actual password
define('DB_NAME', 'skincheck_db');
define('DB_PORT', '5432');

// App Root
define('APPROOT', dirname(dirname(__FILE__)));
// URL Root (adjust if needed depending on vhost setup)
define('URLROOT', 'http://localhost/APSkincek');
// Site Name
define('SITENAME', 'SkinCheck');
// Python AI Service URL
define('PYTHON_API_URL', 'http://localhost:5000');
