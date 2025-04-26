<?php
require_once 'functions.php';

sec_session_start();

// Check if user is trying to login
if (isset($_POST['username'], $_POST['password'])) {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password']; // Don't sanitize passwords
    
    if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
        // Authentication successful
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = $username;
        $_SESSION['login_string'] = hash('sha512', $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
        $_SESSION['last_activity'] = time();
        
        redirect('dashboard.php');
    } else {
        $_SESSION['error_message'] = "Invalid username or password.";
        redirect('login.php');
    }
}

// If not logged in and not on login page, redirect to login
if (!is_logged_in() && basename($_SERVER['PHP_SELF']) != 'login.php') {
    redirect('login.php');
}
?>