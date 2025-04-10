<?php
session_start();

// Checking  if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>View Participants - Cit-E Cycling</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* css for view container */
        .view-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .view-title {
            color: #2c3e50;
            margin-bottom: 2rem;
            text-align: center;
        }
        /* css for Responsive styles */
        @media (max-width: 767.98px) {
            .view-container {
                margin: 1rem auto;
                padding: 1rem;
                max-width: 95%;
            }
            .view-title {
                font-size: 1.8rem;
                margin-bottom: 1rem;
            }
            .table {
                font-size: 0.85rem;
            }
            .btn-sm {
                padding: 0.2rem 0.5rem;
                font-size: 0.75rem;
            }
        }
        /* css for Custom table responsive styles */
        @media (max-width: 575.98px) {
            .mobile-labels {
                display: inline-block;
                font-weight: bold;
                margin-right: 0.5rem;
            }
            .mobile-stack td {
                display: block;
                padding: 0.5rem;
                border-top: none;
            }
            .mobile-stack tr {
                border-bottom: 1px solid #dee2e6;
                margin-bottom: 0.5rem;
                display: block;
            }
            .mobile-stack thead {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="view-container bg-white">
            <h1 class="view-title">Manage Participants</h1>
            
            <?php
            //including connection variables
            include 'dbconnect.php';
            
            try {
                $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
                // set the PDO error mode to exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Get all participants with club information
                $stmt = $conn->prepare("
                    SELECT p.*, c.name as club_name 
                    FROM participant p 
                    LEFT JOIN club c ON p.club_id = c.id
                    ORDER BY p.id
                ");
                $stmt->execute();
                $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($participants) > 0) {
                    echo '<div class="table-responsive">';
                    
                    // Determine if we're on mobile
                    echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            if (window.innerWidth < 576) {
                                document.querySelectorAll("table").forEach(function(table) {
                                    table.classList.add("mobile-stack");
                                });
                            }
                        });
                    </script>';
                    
                    echo '<table class="table table-striped table-hover">';
                    echo '<thead class="table-dark">';
                    echo '<tr>';
                    echo '<th>ID</th>';
                    echo '<th>Name</th>';
                    echo '<th>Email</th>';
                    echo '<th>Club</th>';
                    echo '<th>Power Output (W)</th>';
                    echo '<th>Distance (km)</th>';
                    echo '<th>Actions</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    
                    foreach ($participants as $participant) {
                        echo '<tr>';
                        echo '<td><span class="d-none d-sm-inline"></span><span class="d-inline d-sm-none mobile-labels">ID:</span>' . htmlspecialchars($participant['id']) . '</td>';
                        echo '<td><span class="d-none d-sm-inline"></span><span class="d-inline d-sm-none mobile-labels">Name:</span>' . htmlspecialchars($participant['firstname'] . ' ' . $participant['surname']) . '</td>';
                        echo '<td><span class="d-none d-sm-inline"></span><span class="d-inline d-sm-none mobile-labels">Email:</span>' . htmlspecialchars($participant['email']) . '</td>';
                        echo '<td><span class="d-none d-sm-inline"></span><span class="d-inline d-sm-none mobile-labels">Club:</span>' . htmlspecialchars($participant['club_name'] ?? 'No Club') . '</td>';
                        echo '<td><span class="d-none d-sm-inline"></span><span class="d-inline d-sm-none mobile-labels">Power Output:</span>' . number_format($participant['power_output'], 2) . ' W</td>';
                        echo '<td><span class="d-none d-sm-inline"></span><span class="d-inline d-sm-none mobile-labels">Distance:</span>' . number_format($participant['distance'], 2) . ' km</td>';
                        echo '<td><span class="d-none d-sm-inline"></span><span class="d-inline d-sm-none mobile-labels">Actions:</span>';
                        echo '<div class="btn-group" role="group">';
                        echo '<a href="edit_participant_form.php?id=' . $participant['id'] . '" class="btn btn-sm btn-primary me-2">Edit</a>';
                        echo '<a href="delete.php?id=' . $participant['id'] . '" class="btn btn-sm btn-danger">Delete</a>';
                        echo '</div>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-info">No participants found.</div>';
                }
            }
            catch(PDOException $e) {
                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
            }
            ?>
            
            <div class="d-grid gap-2 mt-4">
                <a href="admin_menu.php" class="btn btn-secondary">Back to Admin Menu</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>