<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the current page name for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " - Itinerary System" : "Itinerary System"; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Custom Styles -->
    <?php include 'includes/styles.php'; ?>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="nav flex-column">
            <a href="dashboard.php" class="nav-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="itinerary.php" class="nav-link <?php echo $current_page === 'itinerary' ? 'active' : ''; ?>">
                <i class="bi bi-calendar-event"></i> Itineraries
            </a>
            <a href="guides.php" class="nav-link <?php echo $current_page === 'guides' ? 'active' : ''; ?>">
                <i class="bi bi-people"></i> Guides
            </a>
            <a href="reports.php" class="nav-link <?php echo $current_page === 'reports' ? 'active' : ''; ?>">
                <i class="bi bi-graph-up"></i> Reports
            </a>
        </nav>
    </div>

    <!-- Mobile Overlay -->
    <div class="overlay"></div>

    <!-- Main Content -->
    <main class="main-content">
        <?php if (isset($content)) echo $content; ?>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Mobile menu toggle
        document.querySelector('.navbar-toggler').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
            document.querySelector('.overlay').classList.toggle('show');
        });

        // Close sidebar when clicking overlay
        document.querySelector('.overlay').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.remove('show');
            document.querySelector('.overlay').classList.remove('show');
        });
    </script>
</body>
</html> 