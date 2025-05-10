<?php
require_once 'config.php';

// Start secure session
function sec_session_start() {
    $session_name = SESSION_NAME;
    $secure = true; // Set to true if using HTTPS
    $httponly = true; // Prevent JavaScript access to session ID

    ini_set('session.use_only_cookies', 1);
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params(
        $cookieParams["lifetime"],
        $cookieParams["path"],
        $cookieParams["domain"],
        $secure,
        $httponly
    );
    session_name($session_name);
    session_start();
    session_regenerate_id(true);
}

// CSRF token generation
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// File upload handling
function handle_file_upload($file, $type = 'image') {
    $errors = [];
    $uploadPath = UPLOAD_DIR . basename($file['name']);
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "File upload error: " . $file['error'];
        return [false, $errors];
    }
    
    // Check file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $errors[] = "File is too large. Maximum size is " . (MAX_UPLOAD_SIZE / 1024 / 1024) . "MB.";
    }
    
    // Check file type
    $fileExt = strtolower(pathinfo($uploadPath, PATHINFO_EXTENSION));
    if ($type === 'image' && !in_array($fileExt, ALLOWED_IMAGE_TYPES)) {
        $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed.";
    }
    
    if (!empty($errors)) {
        return [false, $errors];
    }
    
    // Generate unique filename
    $newFilename = uniqid() . '.' . $fileExt;
    $destination = UPLOAD_DIR . $newFilename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return [true, $newFilename];
    } else {
        $errors[] = "Failed to move uploaded file.";
        return [false, $errors];
    }
}

// Input sanitization
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Check if user is logged in
function is_logged_in() {
    if (!isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
        return false;
    }
    
    $user_id = $_SESSION['user_id'];
    $login_string = $_SESSION['login_string'];
    $username = $_SESSION['username'];
    
    // Check timeout
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        return false;
    }
    $_SESSION['last_activity'] = time();
    
    // Verify login string
    $login_check = hash('sha512', $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
    return hash_equals($login_check, $login_string);
}

// Redirect function
function redirect($url) {
    header("Location: $url");
    exit();
}
?>