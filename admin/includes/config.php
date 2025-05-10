<?php
// Admin configuration
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD_HASH', password_hash('your_secure_password', PASSWORD_DEFAULT));
define('SESSION_NAME', 'gpi_admin');
define('SESSION_TIMEOUT', 3600); // 1 hour

// CSRF protection
define('CSRF_TOKEN_SECRET', 'your_csrf_secret_here');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'gpi_website');
define('DB_USER', 'root');
define('DB_PASS', '');

// File upload settings
define('UPLOAD_DIR', '../uploads/');
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // 2MB

// Set timezone
date_default_timezone_set('Africa/Dar_es_Salaam');
?>