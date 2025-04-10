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

$error_message = '';
$success_message = '';
$participant = null;

if (isset($_GET['id'])) {
    include 'dbconnect.php';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Geting the  participant details
        $stmt = $conn->prepare("SELECT * FROM participant WHERE id = :id");
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$participant) {
            $error_message = "Participant not found";
        }
    } catch(PDOException $e) {
        $error_message = "Connection failed: " . $e->getMessage();
    }
} else {
    $error_message = "No participant ID provided";
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm']) && $_POST['confirm'] == 'yes') {
    try {
        $stmt = $conn->prepare("DELETE FROM participant WHERE id = :id");
        $stmt->bindParam(':id', $_GET['id']);
        
        if ($stmt->execute()) {
            $success_message = "Participant deleted successfully";
        } else {
            $error_message = "Failed to delete participant";
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
    <title>Delete Participant - Cit-E Cycling</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* styles for delete container */
        .delete-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .delete-title {
            color: #2c3e50;
            margin-bottom: 2rem;
            text-align: center;
        }
        .participant-details {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        /* for Responsive styles */
        @media (max-width: 767.98px) {
            .delete-container {
                margin: 1rem auto;
                padding: 1rem;
                max-width: 95%;
            }
            .delete-title {
                font-size: 1.8rem;
                margin-bottom: 1rem;
            }
            .participant-details {
                padding: 0.75rem;
            }
            .alert {
                padding: 0.75rem;
                font-size: 0.9rem;
            }
            .btn {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="delete-container bg-white">
            <h1 class="delete-title">Delete Participant</h1>
            
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

            <?php if ($participant && !$success_message): ?>
                <div class="participant-details">
                    <h5>Participant Details</h5>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($participant['firstname'] . ' ' . $participant['surname']); ?></p>
                    <p><strong>Power Output:</strong> <?php echo number_format($participant['power_output'], 2); ?> W</p>
                    <p><strong>Distance Travelled:</strong> <?php echo number_format($participant['distance'], 2); ?> km</p>
                </div>

                <div class="alert alert-warning">
                    <h5>Warning!</h5>
                    <p>Are you sure you want to delete this participant? This action cannot be undone.</p>
                </div>

                <form id="deleteForm" action="delete.php?id=<?php echo htmlspecialchars($_GET['id']); ?>" method="POST">
                    <div class="d-grid gap-2">
                        <button type="button" id="confirmDelete" class="btn btn-danger">Yes, Delete Participant</button>
                        <a href="view_participants_edit_delete.php" class="btn btn-secondary">Cancel</a>
                    </div>
                    <input type="hidden" name="confirm" value="yes">
                </form>
            <?php elseif (!$error_message && !$success_message): ?>
                <div class="alert alert-info">
                    No participant selected for deletion.
                </div>
            <?php endif; ?>

            <?php if ($success_message || $error_message): ?>
                <div class="d-grid gap-2 mt-4">
                    <a href="view_participants_edit_delete.php" class="btn btn-secondary">Back to Participants List</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Confirmation dialog
        $(document).ready(function() {
            $('#confirmDelete').click(function() {
                if (confirm('Are you absolutely sure you want to delete this participant?')) {
                    $('#deleteForm').submit();
                }
            });
        });
    </script>
</body>
</html>