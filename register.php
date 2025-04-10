<?php
session_start();

// copde for Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// code for Function to validate email
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// code for Function to validate phone number
function is_valid_phone($phone) {
    return preg_match('/^[0-9]{10,15}$/', $phone);
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'dbconnect.php';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // code for Getting and sanitize input
        $firstname = sanitize_input($_POST['firstname']);
        $surname = sanitize_input($_POST['surname']);
        $email = sanitize_input($_POST['email']);
        $phone = sanitize_input($_POST['phone']);
        $club = isset($_POST['club']) ? sanitize_input($_POST['club']) : null;
        $terms = isset($_POST['terms']) ? true : false;

        // Validating input
        $errors = [];

        if (empty($firstname) || !preg_match('/^[A-Za-z\s]{2,50}$/', $firstname)) {
            $errors[] = "Invalid first name";
        }

        if (empty($surname) || !preg_match('/^[A-Za-z\s]{2,50}$/', $surname)) {
            $errors[] = "Invalid surname";
        }

        if (empty($email) || !is_valid_email($email)) {
            $errors[] = "Invalid email address";
        }

        if (empty($phone) || !is_valid_phone($phone)) {
            $errors[] = "Invalid phone number";
        }

        if (!empty($club) && !preg_match('/^[A-Za-z0-9\s]{2,100}$/', $club)) {
            $errors[] = "Invalid club name";
        }

        if (!$terms) {
            $errors[] = "You must accept the terms and conditions";
        }

        if (empty($errors)) {
            // Checking if email already exists
            $stmt = $conn->prepare("SELECT id FROM interest WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error_message = "This email address is already registered";
            } else {
                //code for  Inserting new registration
                $stmt = $conn->prepare("INSERT INTO interest (firstname, surname, email, terms) 
                                     VALUES (:firstname, :surname, :email, :terms)");
                
                $stmt->bindParam(':firstname', $firstname);
                $stmt->bindParam(':surname', $surname);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':terms', $terms, PDO::PARAM_BOOL);
                
                if ($stmt->execute()) {
                    $success_message = "Registration successful! Thank you for your interest.";
                } else {
                    $error_message = "Registration failed. Please try again.";
                }
            }
        } else {
            $error_message = implode("<br>", $errors);
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
    <title>Registration Status - Cit-E Cycling</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* css for status container */
        .status-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .status-title {
            color: #2c3e50;
            margin-bottom: 2rem;
            text-align: center;
        }
        /*css for  Responsive styles */
        @media (max-width: 767.98px) {
            .status-container {
                margin: 1rem auto;
                padding: 1rem;
                max-width: 95%;
            }
            .status-title {
                font-size: 1.8rem;
                margin-bottom: 1rem;
            }
            .btn {
                font-size: 0.9rem;
            }
            .alert {
                padding: 0.75rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="status-container bg-white">
            <h1 class="status-title">Registration Status</h1>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="d-grid gap-2">
                <a href="register_form.html" class="btn btn-primary">Register Another</a>
                <a href="index.html" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>