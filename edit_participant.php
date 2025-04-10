<?php
session_start();

// Checking if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Function for the to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function for validating numeric input
function is_valid_number($value, $min = 0) {
    return is_numeric($value) && $value >= $min;
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'dbconnect.php';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Geting and validating input
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $power_output = isset($_POST['power_output']) ? sanitize_input($_POST['power_output']) : '';
        $distance_travelled = isset($_POST['distance_travelled']) ? sanitize_input($_POST['distance_travelled']) : '';

        // Validating input
        if ($id <= 0) {
            $error_message = "Invalid participant ID";
        } elseif (!is_valid_number($power_output)) {
            $error_message = "Invalid power output value";
        } elseif (!is_valid_number($distance_travelled)) {
            $error_message = "Invalid distance value";
        } else {
            // Checking if participant exists
            $stmt = $conn->prepare("SELECT id FROM participant WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                $error_message = "Participant not found";
            } else {
                // Updating participant
                $stmt = $conn->prepare("UPDATE participant SET 
                                      power_output = :power_output,
                                      distance = :distance
                                      WHERE id = :id");
                
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':power_output', $power_output);
                $stmt->bindParam(':distance', $distance_travelled);
                
                if ($stmt->execute()) {
                    $success_message = "Participant details updated successfully";
                } else {
                    $error_message = "Failed to update participant details";
                }
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
    <title>Edit Participant - Cit-E Cycling</title>
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
        /* css forResponsive styles */
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
            <h1 class="status-title">Update Status</h1>
            
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
                <a href="edit_participant_form.php?id=<?php echo isset($_POST['id']) ? htmlspecialchars($_POST['id']) : ''; ?>" 
                   class="btn btn-primary">Edit Again</a>
                <a href="view_participants_edit_delete.php" class="btn btn-secondary">Back to Participants List</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>