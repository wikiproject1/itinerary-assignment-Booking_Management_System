<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
?>
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