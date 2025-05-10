<?php
<<<<<<< HEAD
session_start();
session_destroy();
header('Location: login.php');
=======
require_once 'includes/functions.php';

sec_session_start();

// Unset all session values
$_SESSION = array();

// Delete session cookie
$params = session_get_cookie_params();
setcookie(
    session_name(),
    '',
    time() - 42000,
    $params["path"],
    $params["domain"],
    $params["secure"],
    $params["httponly"]
);

// Destroy session
session_destroy();

redirect('login.php');
>>>>>>> 40d2ea7f59aee763d902fac8d078a4356200de5b
?>