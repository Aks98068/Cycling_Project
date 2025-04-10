<?php
session_start();

// Checking if user is logged in
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
    <title>Search - Cit-E Cycling</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* css for search container */
        .search-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .search-title {
            color: #2c3e50;
            margin-bottom: 2rem;
            text-align: center;
        }
        .search-section {
            margin-bottom: 3rem;
            padding: 1.5rem;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        .search-section:last-child {
            margin-bottom: 0;
        }
        .search-section h2 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        .form-label {
            font-weight: 500;
        }
        /*css for  Responsive styles */
        @media (max-width: 767.98px) {
            .search-container {
                margin: 1rem auto;
                padding: 1rem;
                max-width: 95%;
            }
            .search-title {
                font-size: 1.8rem;
                margin-bottom: 1rem;
            }
            .search-section {
                padding: 1rem;
                margin-bottom: 1.5rem;
            }
            .search-section h2 {
                font-size: 1.25rem;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="search-container bg-white">
            <h1 class="search-title">Search Participants and Clubs</h1>
            
            <div class="search-section">
                <h2>Search for Individual Participants</h2>
                <form action="search_result.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" 
                               pattern="[A-Za-z\s]{2,50}"
                               title="Please enter a valid first name (2-50 characters, letters and spaces only)">
                    </div>
                    <div class="mb-3">
                        <label for="surname" class="form-label">Surname</label>
                        <input type="text" class="form-control" id="surname" name="surname"
                               pattern="[A-Za-z\s]{2,50}"
                               title="Please enter a valid surname (2-50 characters, letters and spaces only)">
                    </div>
                    <input type="hidden" name="participant" value="1">
                    <button type="submit" class="btn btn-primary">Search Participants</button>
                </form>
            </div>

            <div class="search-section">
                <h2>Search for Clubs/Teams</h2>
                <form action="search_result.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="club" class="form-label">Club Name</label>
                        <input type="text" class="form-control" id="club" name="club"
                               pattern="[A-Za-z0-9\s]{2,100}"
                               title="Please enter a valid club name (2-100 characters)">
                    </div>
                    <button type="submit" class="btn btn-primary">Search Clubs</button>
                </form>
            </div>

            <div class="d-grid gap-2 mt-4">
                <a href="admin_menu.php" class="btn btn-secondary">Back to Admin Menu</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!--code for  Form Validation Script -->
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