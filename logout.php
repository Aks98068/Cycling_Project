<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroing the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroing the session
session_destroy();

// Redirecting to login page
header('Location: login.php');
exit();
?> 