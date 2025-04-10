<?php
session_start();

// Redirecing if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin_menu.php');
    exit();
}

//code for  Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'dbconnect.php';
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // for Getting and sanitize input
        $username = sanitize_input($_POST['username']);
        $password = $_POST['password'];
        
        // for Validating input
        if (empty($username) || empty($password)) {
            $error_message = "Please fill in all fields";
        } else {
            // for Preparing statement to prevent SQL injection
            $stmt = $conn->prepare("SELECT * FROM user WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                // Verifing password directly
                if ($password === $user['password']) {
                    // Set session variables
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    
                    // Redirecing to admin menu
                    header('Location: admin_menu.php');
                    exit();
                } else {
                    $error_message = "Invalid username or password";
                }
            } else {
                $error_message = "Invalid username or password";
            }
        }
    } catch(PDOException $e) {
        $error_message = "Connection failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Login - Cit-E Cycling</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* css for the login container */
        .login-container {
            max-width: 400px;
            margin: 2rem auto;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .login-title {
            color: #2c3e50;
            margin-bottom: 2rem;
            text-align: center;
        }
        /* css for Responsive styles */
        @media (max-width: 576px) {
            .login-container {
                margin: 1rem auto;
                padding: 1.5rem;
                max-width: 95%;
            }
            .login-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="login-container bg-white">
            <h1 class="login-title">Admin Login</h1>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <a href="index.html" class="btn btn-secondary w-100 mt-2">Back to Home</a>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>