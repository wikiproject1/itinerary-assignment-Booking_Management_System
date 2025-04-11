<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

if(!isset($_SESSION)) {
    session_start();
}

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

// Get upcoming arrivals
require_once __DIR__ . "/../config/database.php";
$upcoming_arrivals = [];
$today = date('Y-m-d');
$three_days_later = date('Y-m-d', strtotime('+3 days'));

$sql = "SELECT i.*, g.first_name, g.last_name 
        FROM itineraries i 
        LEFT JOIN guide_assignments ga ON i.id = ga.itinerary_id 
        LEFT JOIN guides g ON ga.guide_id = g.id 
        WHERE i.arrival_time BETWEEN ? AND ? 
        AND i.completion_status = 'Pending'
        ORDER BY i.arrival_time ASC 
        LIMIT 3";

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "ss", $today, $three_days_later);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while($row = mysqli_fetch_assoc($result)){
        $upcoming_arrivals[] = $row;
    }
    mysqli_stmt_close($stmt);
}

// Get user data from session
$user_name = $_SESSION['user_name'] ?? 'Admin';
$notifications_count = count($upcoming_arrivals);
?>

<!-- Header -->
<header class="header">
    <div class="header-content">
        <div class="d-flex align-items-center">
            <!-- Mobile Menu Toggle Button -->
            <button class="navbar-toggler d-lg-none me-3" type="button" id="sidebarToggle">
                <i class="bi bi-list text-white fs-4"></i>
            </button>
            <?php include 'logo.php'; ?>
            <nav class="header-nav">
                <ul class="nav">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="itinerary.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'itinerary.php' ? 'active' : ''; ?>">
                            <i class="bi bi-calendar-event"></i>
                            Itineraries
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="guides.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'guides.php' ? 'active' : ''; ?>">
                            <i class="bi bi-people"></i>
                            Guides
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="reports.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                            <i class="bi bi-graph-up"></i>
                            Reports
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="header-actions d-flex align-items-center">
            <div class="notifications me-4 position-relative">
                <a href="#" class="text-white" data-bs-toggle="dropdown">
                    <i class="bi bi-bell-fill fs-5"></i>
                    <?php if($notifications_count > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo $notifications_count; ?>
                        <span class="visually-hidden">unread notifications</span>
                    </span>
                    <?php endif; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <h6 class="dropdown-header">Upcoming Arrivals</h6>
                    <?php if(count($upcoming_arrivals) > 0): ?>
                        <?php foreach($upcoming_arrivals as $arrival): ?>
                            <a class="dropdown-item" href="itinerary.php">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-check text-primary me-2"></i>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($arrival['group_name']); ?></div>
                                        <small class="text-muted">
                                            Arriving: <?php echo date('M d, Y', strtotime($arrival['arrival_time'])); ?><br>
                                            <?php if($arrival['first_name']): ?>
                                            Guide: <?php echo htmlspecialchars($arrival['first_name'] . ' ' . $arrival['last_name']); ?>
                                            <?php else: ?>
                                            <span class="text-warning">No guide assigned</span>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a class="dropdown-item text-muted">
                            <i class="bi bi-info-circle me-2"></i>
                            No upcoming arrivals in the next 3 days
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="settings me-4">
                <a href="#" class="text-white">
                    <i class="bi bi-gear-fill fs-5"></i>
                </a>
            </div>
            <div class="user-profile d-flex align-items-center">
                <div class="user-info me-3 text-end">
                    <h6 class="user-name mb-0"><?php echo htmlspecialchars($_SESSION["username"]); ?></h6>
                    <small class="user-role text-light">Administrator</small>
                </div>
                <div class="user-avatar">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION["username"]); ?>&background=random" 
                         alt="Profile" 
                         class="rounded-circle"
                         width="40"
                         height="40">
                </div>
                <div class="ms-2">
                    <a href="logout.php" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Sidebar -->
<nav class="sidebar">
    <div class="nav flex-column">
        <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        <a href="itinerary.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'itinerary.php' ? 'active' : ''; ?>">
            <i class="bi bi-calendar-event"></i>
            <span>Itineraries</span>
        </a>
        <a href="guides.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'guides.php' ? 'active' : ''; ?>">
            <i class="bi bi-people"></i>
            <span>Guide Assignment</span>
        </a>
        <a href="reports.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
            <i class="bi bi-graph-up"></i>
            <span>Reports</span>
        </a>
        <a href="logout.php" class="nav-link">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </div>
</nav>

<!-- Mobile Menu Overlay -->
<div class="overlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle functionality
    const sidebarToggle = document.querySelector('.navbar-toggler');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.overlay');

    function toggleSidebar() {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }

    sidebarToggle.addEventListener('click', toggleSidebar);
    
    // Close sidebar when clicking overlay
    overlay.addEventListener('click', toggleSidebar);

    // Close sidebar when clicking a nav link on mobile
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                toggleSidebar();
            }
        });
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        }
    });
});
</script> 