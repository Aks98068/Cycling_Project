<?php
// Check if a session has already been started
if (session_status() == PHP_SESSION_NONE) {
    // Session security settings - only apply if session hasn't started
    ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session cookie
    ini_set('session.use_only_cookies', 1); // Force sessions to only use cookies
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    ini_set('session.gc_maxlifetime', 1800); // Session timeout after 30 minutes of inactivity
    session_set_cookie_params(1800); // Cookie lifetime
}

$servername = "localhost";// localhost 
$username = "root"; // Default MySQL user
$password = ""; // Default MySQL password is empty
$dbname = "cycling";// database name cycling

// Creating mysqli connection for database
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// PDO connection string for files using PDO
$database = $dbname;
?>
