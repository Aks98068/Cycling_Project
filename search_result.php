<?php
session_start();

// Checking  if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// code for Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$error_message = '';
$search_results = [];
$club_stats = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'dbconnect.php';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Checking which form was submitted
        if (isset($_POST['participant']) && $_POST['participant'] == "1") {
            // Searching  for participants
            $firstname = isset($_POST['firstname']) ? sanitize_input($_POST['firstname']) : '';
            $surname = isset($_POST['surname']) ? sanitize_input($_POST['surname']) : '';

            $sql = "SELECT p.*, c.name as club_name 
                    FROM participant p 
                    LEFT JOIN club c ON p.club_id = c.id 
                    WHERE 1=1";
            $params = [];

            if (!empty($firstname)) {
                $sql .= " AND p.firstname LIKE :firstname";
                $params[':firstname'] = "%$firstname%";
            }
            if (!empty($surname)) {
                $sql .= " AND p.surname LIKE :surname";
                $params[':surname'] = "%$surname%";
            }

            $stmt = $conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } else {
            // Searching  for clubs
            $club = isset($_POST['club']) ? sanitize_input($_POST['club']) : '';

            if (!empty($club)) {
                //code for  Getting club details
                $stmt = $conn->prepare("SELECT * FROM club WHERE name LIKE :club");
                $stmt->bindValue(':club', "%$club%");
                $stmt->execute();
                $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // code for Getting club statistics
                foreach ($search_results as $club) {
                    $stmt = $conn->prepare("
                        SELECT 
                            COUNT(*) as total_participants,
                            AVG(power_output) as avg_power,
                            AVG(distance) as avg_distance,
                            SUM(power_output) as total_power,
                            SUM(distance) as total_distance
                        FROM participant 
                        WHERE club_id = :club_id
                    ");
                    $stmt->bindValue(':club_id', $club['id']);
                    $stmt->execute();
                    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
                    $club_stats[$club['id']] = $stats;
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
    <title>Search Results - Cit-E Cycling</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* css for results container */
        .results-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .results-title {
            color: #2c3e50;
            margin-bottom: 2rem;
            text-align: center;
        }
        .stats-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .table-responsive {
            margin-top: 2rem;
        }
        /*css for  Responsive styles */
        @media (max-width: 767.98px) {
            .results-container {
                margin: 1rem auto;
                padding: 1rem;
                max-width: 95%;
            }
            .results-title {
                font-size: 1.8rem;
                margin-bottom: 1rem;
            }
            .stats-card {
                padding: 0.75rem;
            }
            .table {
                font-size: 0.85rem;
            }
        }
        /*  css for Custom table responsive styles */
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
        <div class="results-container bg-white">
            <h1 class="results-title">Search Results</h1>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($search_results)): ?>
                <div class="alert alert-info">
                    No results found. Please try different search criteria.
                </div>
            <?php else: ?>
                <?php if (isset($_POST['participant']) && $_POST['participant'] == "1"): ?>
                    <!-- Participant Results -->
                    <div class="table-responsive">
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                if (window.innerWidth < 576) {
                                    document.querySelectorAll("table").forEach(function(table) {
                                        table.classList.add("mobile-stack");
                                    });
                                }
                            });
                        </script>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Club</th>
                                    <th>Power Output</th>
                                    <th>Distance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($search_results as $participant): ?>
                                    <tr>
                                        <td><span class="d-none d-sm-inline"></span><span class="d-inline d-sm-none mobile-labels">Name:</span><?php echo htmlspecialchars($participant['firstname'] . ' ' . $participant['surname']); ?></td>
                                        <td><span class="d-none d-sm-inline"></span><span class="d-inline d-sm-none mobile-labels">Club:</span><?php echo htmlspecialchars($participant['club_name'] ?? 'No Club'); ?></td>
                                        <td><span class="d-none d-sm-inline"></span><span class="d-inline d-sm-none mobile-labels">Power Output:</span><?php echo number_format($participant['power_output'], 2); ?> W</td>
                                        <td><span class="d-none d-sm-inline"></span><span class="d-inline d-sm-none mobile-labels">Distance:</span><?php echo number_format($participant['distance'], 2); ?> km</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <!-- Club Results -->
                    <?php foreach ($search_results as $club): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="mb-0"><?php echo htmlspecialchars($club['name']); ?></h3>
                            </div>
                            <div class="card-body">
                                <?php if (isset($club_stats[$club['id']])): ?>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="stats-card">
                                                <h5>Club Statistics</h5>
                                                <p>Total Participants: <?php echo $club_stats[$club['id']]['total_participants']; ?></p>
                                                <p>Average Power Output: <?php echo number_format($club_stats[$club['id']]['avg_power'], 2); ?> W</p>
                                                <p>Average Distance: <?php echo number_format($club_stats[$club['id']]['avg_distance'], 2); ?> km</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="stats-card">
                                                <h5>Total Performance</h5>
                                                <p>Total Power Output: <?php echo number_format($club_stats[$club['id']]['total_power'], 2); ?> W</p>
                                                <p>Total Distance: <?php echo number_format($club_stats[$club['id']]['total_distance'], 2); ?> km</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>

            <div class="d-grid gap-2 mt-4">
                <a href="search_form.php" class="btn btn-primary">New Search</a>
                <a href="admin_menu.php" class="btn btn-secondary">Back to Admin Menu</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>