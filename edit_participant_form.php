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
$participant = null;

if (isset($_GET['id'])) {
    include 'dbconnect.php';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
        /* css for edit container */
        .edit-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .edit-title {
            color: #2c3e50;
            margin-bottom: 2rem;
            text-align: center;
        }
        .form-label {
            font-weight: 500;
        }
        /*css for Responsive styles */
        @media (max-width: 767.98px) {
            .edit-container {
                margin: 1rem auto;
                padding: 1rem;
                max-width: 95%;
            }
            .edit-title {
                font-size: 1.8rem;
                margin-bottom: 1rem;
            }
            .form-label {
                font-size: 0.9rem;
            }
            .form-control {
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
        <div class="edit-container bg-white">
            <h1 class="edit-title">Edit Participant Details</h1>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($participant): ?>
                <form action="edit_participant.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" 
                               value="<?php echo htmlspecialchars($participant['firstname']); ?>" 
                               disabled>
                    </div>

                    <div class="mb-3">
                        <label for="surname" class="form-label">Surname</label>
                        <input type="text" class="form-control" id="surname" name="surname" 
                               value="<?php echo htmlspecialchars($participant['surname']); ?>" 
                               disabled>
                    </div>

                    <div class="mb-3">
                        <label for="power_output" class="form-label">Power Output (Watts)</label>
                        <input type="number" class="form-control" id="power_output" name="power_output" 
                               value="<?php echo htmlspecialchars($participant['power_output']); ?>" 
                               required min="0" step="0.01">
                        <div class="invalid-feedback">
                            Please enter a valid power output value
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="distance_travelled" class="form-label">Distance Travelled (KM)</label>
                        <input type="number" class="form-control" id="distance_travelled" name="distance_travelled" 
                               value="<?php echo htmlspecialchars($participant['distance']); ?>" 
                               required min="0" step="0.01">
                        <div class="invalid-feedback">
                            Please enter a valid distance value
                        </div>
                    </div>

                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($participant['id']); ?>">

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Update Participant</button>
                        <a href="view_participants_edit_delete.php" class="btn btn-secondary">Back to Participants List</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- script for form validation -->
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>