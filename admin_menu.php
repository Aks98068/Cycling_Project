<?php
session_start();

// Checking the if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Geting username for greeting
$username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Menu - Cit-E Cycling</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* css for admin container */
        .admin-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .admin-title {
            color: #2c3e50;
            margin-bottom: 2rem;
            text-align: center;
        }
        .admin-menu-list {
            list-style: none;
            padding: 0;
        }
        .admin-menu-item {
            margin-bottom: 1rem;
        }
        .admin-menu-link {
            display: block;
            padding: 1rem;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            color: #2c3e50;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .admin-menu-link:hover {
            background-color: #e9ecef;
            transform: translateX(5px);
            text-decoration: none;
            color: #2c3e50;
        }
        /* Responsive styles for admin */
        @media (max-width: 767.98px) {
            .admin-container {
                margin: 1rem auto;
                padding: 1.5rem;
                max-width: 95%;
            }
            .admin-title {
                font-size: 1.8rem;
            }
            .admin-menu-link {
                padding: 0.75rem;
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
        <div class="admin-container bg-white">
            <h1 class="admin-title">Cit-E Cycling Admin Portal</h1>
            <div class="alert alert-info">
                Welcome, <?php echo htmlspecialchars($username); ?>! 
                <span class="float-end">
                    <a href="logout.php" class="text-decoration-none">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </span>
            </div>
            <ul class="admin-menu-list">
                <li class="admin-menu-item">
                    <a href="search_form.php" class="admin-menu-link">
                        <i class="bi bi-search"></i> Search for Clubs or Participants
                    </a>
                </li>
                <li class="admin-menu-item">
                    <a href="view_participants_edit_delete.php" class="admin-menu-link">
                        <i class="bi bi-people"></i> View All Participants (Edit/Delete)
                    </a>
                </li>
                <li class="admin-menu-item">
                    <a href="logout.php" class="admin-menu-link text-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</body>
</html>