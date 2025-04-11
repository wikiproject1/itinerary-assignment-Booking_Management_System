<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

// Get upcoming arrivals
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Management System</title>
    
    <?php 
    define('BASEPATH', true);
    include 'includes/site_icons.php'; 
    ?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* Header Styles */
        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 1rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1030;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1rem;
            max-width: 1800px;
            margin: 0 auto;
        }

        .header-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-left: 0.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #e6e9f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-nav .nav {
            margin: 0;
            padding: 0;
            display: flex;
            gap: 0.5rem;
        }

        .header-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.5rem 1rem;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .header-nav .nav-link:hover,
        .header-nav .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .header-nav .nav-link i {
            font-size: 1.1rem;
        }

        .header-actions {
            gap: 1rem;
        }

        .header-actions a {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .header-actions a:hover {
            color: white;
            transform: translateY(-2px);
        }

        .notifications .dropdown-menu {
            min-width: 280px;
            padding: 0.5rem;
            margin-top: 0.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: none;
            border-radius: 10px;
        }

        .notifications .dropdown-header {
            color: #2c3e50;
            font-weight: 600;
            padding: 0.5rem 1rem;
        }

        .notifications .dropdown-item {
            padding: 0.75rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .notifications .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .user-name {
            color: white;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .user-role {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .user-avatar img {
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .user-avatar img:hover {
            border-color: white;
            transform: scale(1.1);
        }

        @media (max-width: 992px) {
            .header-nav {
                display: none;
            }
            
            .header-title {
                font-size: 1.2rem;
            }
            
            .user-info {
                display: none;
            }
        }

        /* Sidebar Styles */
        .sidebar {
            background-color: var(--primary-color);
            width: 250px;
            position: fixed;
            top: 60px;
            left: 0;
            bottom: 0;
            padding: 1rem;
            color: white;
            z-index: 1020;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            overflow-y: auto;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.8rem 1rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--accent-color);
            color: white;
            transform: translateX(5px);
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 250px;
            margin-top: 60px;
            padding: 2rem;
            background-color: #f5f6fa;
            min-height: calc(100vh - 60px);
        }

        /* Card and Table Styles */
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .table td {
            vertical-align: middle;
        }

        /* Button Styles */
        .btn-primary {
            background-color: var(--accent-color);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        /* Enhanced Status Badge Styles */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            min-width: 120px;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .status-pending {
            background-color: #ffd43b;
            color: #000;
            border: 2px solid #fab005;
        }

        .status-completed {
            background-color: #51cf66;
            color: white;
            border: 2px solid #37b24d;
        }

        /* Table Row Status Indicators */
        .table tbody tr {
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .table tbody tr.status-row-completed {
            border-left-color: #37b24d;
            background-color: rgba(81, 207, 102, 0.05);
        }

        .table tbody tr.status-row-pending {
            border-left-color: #fab005;
            background-color: rgba(255, 212, 59, 0.05);
        }

        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .header-title {
                font-size: 1.2rem;
            }
        }

        /* Modal Styles */
        .modal-content {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 1rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid #eee;
            padding: 1rem;
        }

        .delete-modal-icon {
            font-size: 3rem;
            color: var(--danger-color);
            margin-bottom: 1rem;
        }

        .delete-modal-title {
            color: var(--danger-color);
            font-weight: 600;
        }

        /* Dashboard Summary Cards */
        .summary-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            overflow: hidden;
        }

        .summary-card:hover {
            transform: translateY(-5px);
        }

        .summary-card .card-body {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .summary-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .total-groups .summary-icon {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .total-earnings .summary-icon {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .total-deposits .summary-icon {
            background-color: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }

        .remaining-amount .summary-icon {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .summary-info {
            flex-grow: 1;
        }

        .summary-value {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            line-height: 1.2;
        }

        .summary-label {
            color: #6c757d;
            margin: 0;
            font-size: 0.875rem;
        }

        .summary-card .progress {
            height: 4px;
            margin: 0;
            border-radius: 0;
        }

        /* Status Cards */
        .status-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .status-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
        }

        .status-circle.pending {
            background-color: #ffd43b;
            color: #000;
        }

        .status-circle.completed {
            background-color: #51cf66;
        }

        /* Card Headers */
        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.125);
        }

        .card-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 0;
        }

        /* Mobile Menu Styles */
        .navbar-toggler {
            padding: 0.25rem 0.75rem;
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .navbar-toggler:focus {
            outline: none;
            box-shadow: none;
        }

        /* Sidebar Mobile Styles */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1015;
            }

            .overlay.show {
                display: block;
            }
        }

        /* Adjust header for mobile */
        @media (max-width: 576px) {
            .logo-circle {
                width: 35px;
                height: 35px;
            }
            
            .logo-text {
                font-size: 1.2rem;
            }
            
            .header-title {
                font-size: 1.2rem;
            }

            .header-actions {
                gap: 0.5rem;
            }

            .notifications,
            .settings {
                margin-right: 0.5rem !important;
            }

            .user-info {
                display: none;
            }
        }

        .card.status-card {
            perspective: 1000px;
            background: transparent;
        }

        .status-card .card-body {
            background: white;
            transform-style: preserve-3d;
            border-radius: 10px;
        }

        #monthlyChart {
            transition: transform 0.5s ease;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .chart-container {
            position: relative;
            padding: 20px;
            background: linear-gradient(145deg, #ffffff, #f5f6fa);
            border-radius: 15px;
            box-shadow: 
                0 5px 15px rgba(0,0,0,0.1),
                inset 0 -5px 10px rgba(0,0,0,0.05);
        }

        /* Logo Styles */
        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-circle {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #00c6fb 0%, #005bea 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .logo-circle::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent 0%,
                rgba(255, 255, 255, 0.1) 50%,
                transparent 100%
            );
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        .logo-text {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            font-family: 'Segoe UI', sans-serif;
        }

        .logo-circle:hover {
            transform: scale(1.05) rotate(5deg);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        @keyframes shine {
            0% {
                transform: translateX(-100%) rotate(45deg);
            }
            100% {
                transform: translateX(100%) rotate(45deg);
            }
        }

        /* 3D Table Styles */
        .monthly-distribution-table {
            transform: perspective(1000px) rotateX(5deg);
            transform-origin: top;
            transition: all 0.3s ease;
        }

        .monthly-distribution-table:hover {
            transform: perspective(1000px) rotateX(0deg);
        }

        .monthly-distribution-table thead {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
        }

        .monthly-distribution-table th {
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
        }

        .monthly-distribution-table td {
            padding: 12px 15px;
            border: none;
            background: white;
            transition: all 0.3s ease;
        }

        .monthly-distribution-table tbody tr {
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .monthly-distribution-table tbody tr:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            background: rgba(52, 152, 219, 0.05);
            cursor: pointer;
        }

        .distribution-bar {
            height: 20px;
            background: linear-gradient(90deg, var(--accent-color), var(--primary-color));
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        .distribution-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: shine 2s infinite;
        }

        @keyframes shine {
            to {
                left: 100%;
            }
        }

        /* Month Groups Modal Styles */
        #monthGroupsModal .modal-content {
            background: linear-gradient(145deg, #ffffff, #f5f6fa);
            border: none;
            border-radius: 15px;
        }

        #monthGroupsModal .modal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border-radius: 15px 15px 0 0;
        }

        #monthGroupsModal .table {
            margin: 0;
        }

        #monthGroupsModal .table th {
            background: rgba(52, 152, 219, 0.1);
            border: none;
        }

        #monthGroupsModal .table td {
            border: none;
            padding: 12px;
        }

        #monthGroupsModal .table tr {
            transition: all 0.3s ease;
        }

        #monthGroupsModal .table tr:hover {
            background: rgba(52, 152, 219, 0.05);
            transform: translateX(5px);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="d-flex align-items-center">
                <!-- Mobile Menu Toggle Button -->
                <button class="navbar-toggler d-lg-none me-3" type="button" id="sidebarToggle">
                    <i class="bi bi-list text-white fs-4"></i>
                </button>
                <?php include 'includes/logo.php'; ?>
                <nav class="header-nav">
                    <ul class="nav">
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link active">
                                <i class="bi bi-speedometer2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="itinerary.php" class="nav-link">
                                <i class="bi bi-calendar-event"></i>
                                Itineraries
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="guides.php" class="nav-link">
                                <i class="bi bi-people"></i>
                                Guides
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="reports.php" class="nav-link">
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
                        <?php if(count($upcoming_arrivals) > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo count($upcoming_arrivals); ?>
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Upcoming Arrivals</h6>
                        <?php if(count($upcoming_arrivals) > 0): ?>
                            <?php foreach($upcoming_arrivals as $arrival): ?>
                                <a class="dropdown-item" href="itinerary.php?id=<?php echo $arrival['id']; ?>">
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
            <a href="dashboard.php" class="nav-link">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="itinerary.php" class="nav-link active">
                <i class="bi bi-calendar-event"></i>
                <span>Itineraries</span>
            </a>
            <a href="guides.php" class="nav-link">
                <i class="bi bi-people"></i>
                <span>Guide Assignment</span>
            </a>
            <a href="reports.php" class="nav-link">
                <i class="bi bi-graph-up"></i>
                <span>Reports</span>
            </a>
            <a href="logout.php" class="nav-link">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <div class="row">
                <!-- Upcoming Arrivals Card -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-calendar-check me-2"></i>
                                Upcoming Arrivals (Next 3 Days)
                            </h5>
                            <span class="badge bg-light text-primary">
                                <?php echo count($upcoming_arrivals); ?> Groups
                            </span>
                        </div>
                        <div class="card-body">
                            <?php if(count($upcoming_arrivals) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Group Name</th>
                                                <th>Arrival Date</th>
                                                <th>Starting Location</th>
                                                <th>Final Destination</th>
                                                <th>Assigned Guide</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($upcoming_arrivals as $arrival): ?>
                                                <tr>
                                                    <td>
                                                        <a href="itinerary.php?id=<?php echo $arrival['id']; ?>" class="text-decoration-none">
                                                            <?php echo htmlspecialchars($arrival['group_name']); ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo date('M d, Y', strtotime($arrival['arrival_time'])); ?></td>
                                                    <td><?php echo htmlspecialchars($arrival['starting_location']); ?></td>
                                                    <td><?php echo htmlspecialchars($arrival['final_destination']); ?></td>
                                                    <td>
                                                        <?php if($arrival['first_name']): ?>
                                                            <?php echo htmlspecialchars($arrival['first_name'] . ' ' . $arrival['last_name']); ?>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning text-dark">Not Assigned</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">Pending</span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-calendar-x text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">No upcoming arrivals in the next 3 days</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- End of Upcoming Arrivals Card -->

                <!-- Dashboard Summary Cards -->
                <div class="col-md-3">
                    <div class="card summary-card total-groups">
                        <div class="card-body">
                            <div class="summary-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="summary-info">
                                <h3 class="summary-value" id="totalGroups">0</h3>
                                <p class="summary-label">Total Groups</p>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card total-earnings">
                        <div class="card-body">
                            <div class="summary-icon">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <div class="summary-info">
                                <h3 class="summary-value" id="totalAmount">$0</h3>
                                <p class="summary-label">Total Amount</p>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card total-deposits">
                        <div class="card-body">
                            <div class="summary-icon">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div class="summary-info">
                                <h3 class="summary-value" id="totalDeposits">$0</h3>
                                <p class="summary-label">Total Deposits</p>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 0%" id="depositsProgress"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card remaining-amount">
                        <div class="card-body">
                            <div class="summary-icon">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="summary-info">
                                <h3 class="summary-value" id="totalRemaining">$0</h3>
                                <p class="summary-label">Remaining Amount</p>
                            </div>

                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" id="remainingProgress"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="card status-card">
                        <div class="card-body">
                            <h5 class="card-title">Completion Status</h5>
                            <div class="d-flex justify-content-around align-items-center mt-3">
                                <div class="text-center">
                                    <div class="status-circle pending" id="pendingCount">0</div>
                                    <p class="mt-2">Pending</p>
                                </div>
                                <div class="text-center">
                                    <div class="status-circle completed" id="completedCount">0</div>
                                    <p class="mt-2">Completed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Groups Distribution Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Monthly Groups Distribution</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover monthly-distribution-table">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Number of Groups</th>
                                            <th>Distribution</th>
                                        </tr>
                                    </thead>
                                    <tbody id="monthlyDistributionBody">
                                        <!-- Table content will be loaded dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Itinerary Modal -->
    <div class="modal fade" id="addItineraryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Itinerary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addItineraryForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Month</label>
                                <select class="form-select" name="month" required>
                                    <option value="">Select Month</option>
                                    <option value="JANUARY">JANUARY</option>
                                    <option value="FEBRUARY">FEBRUARY</option>
                                    <option value="MARCH">MARCH</option>
                                    <option value="APRIL">APRIL</option>
                                    <option value="MAY">MAY</option>
                                    <option value="JUNE">JUNE</option>
                                    <option value="JULY">JULY</option>
                                    <option value="AUGUST">AUGUST</option>
                                    <option value="SEPTEMBER">SEPTEMBER</option>
                                    <option value="OCTOBER">OCTOBER</option>
                                    <option value="NOVEMBER">NOVEMBER</option>
                                    <option value="DECEMBER">DECEMBER</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Group Name</label>
                                <input type="text" class="form-control" name="group_name" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Starting Location</label>
                                <input type="text" class="form-control" name="starting_location" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Final Destination</label>
                                <input type="text" class="form-control" name="final_destination" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Arrival Time</label>
                                <input type="datetime-local" class="form-control" name="arrival_time" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Departure Time</label>
                                <input type="datetime-local" class="form-control" name="departure_time" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Status</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status[]" value="Safari" id="statusSafari">
                                    <label class="form-check-label" for="statusSafari">Safari</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status[]" value="Kilimanjaro Climbing" id="statusKilimanjaro">
                                    <label class="form-check-label" for="statusKilimanjaro">Kilimanjaro Climbing</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status[]" value="Day Trip" id="statusDayTrip">
                                    <label class="form-check-label" for="statusDayTrip">Day Trip</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status[]" value="Zanzibar" id="statusZanzibar">
                                    <label class="form-check-label" for="statusZanzibar">Zanzibar</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Total Amount (USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="total_amount" step="0.01" required>
                                    <span class="input-group-text">USD</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Deposit Amount (USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="deposit_amount" step="0.01" required>
                                    <span class="input-group-text">USD</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Remaining Amount (USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="remaining_amount" step="0.01" readonly>
                                    <span class="input-group-text">USD</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Safari Days</label>
                                <input type="text" class="form-control" name="safari_days" placeholder="e.g., 5,6,7,8 Safari & 8 Drop OFF">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Completion Status</label>
                                <select class="form-select" name="completion_status" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveItinerary()">Save Itinerary</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Itinerary Modal -->
    <div class="modal fade" id="editItineraryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Itinerary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editItineraryForm">
                        <input type="hidden" name="id">
                        <!-- Same form fields as add itinerary -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateItinerary()">Update Itinerary</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-trash-fill delete-modal-icon"></i>
                    <h4 class="delete-modal-title mb-3">Delete Itinerary</h4>
                    <p>Are you sure you want to delete this itinerary? This action cannot be undone.</p>
                    <p class="text-danger"><strong>This will permanently remove the itinerary from the system.</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="bi bi-trash-fill me-2"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Month Groups Modal -->
    <div class="modal fade" id="monthGroupsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Groups in <span id="selectedMonth">Month</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Group Name</th>
                                    <th>Starting Location</th>
                                    <th>Final Destination</th>
                                    <th>Status</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody id="monthGroupsBody">
                                <!-- Group details will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentDeleteId = null;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const monthGroupsModal = new bootstrap.Modal(document.getElementById('monthGroupsModal'));
        let itineraryData = []; // Store the data globally for easy access

        document.addEventListener('DOMContentLoaded', function() {
            // Update dashboard summary on page load
            fetch('api/itineraries.php')
                .then(response => response.json())
                .then(data => {
                    itineraryData = data; // Store the data
                    updateDashboardSummary(data);
                    updateMonthlyDistribution(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        function updateDashboardSummary(data) {
            let totalAmount = 0;
            let totalDeposits = 0;
            let totalRemaining = 0;
            let pendingCount = 0;
            let completedCount = 0;

            data.forEach(item => {
                totalAmount += parseFloat(item.total_amount) || 0;
                totalDeposits += parseFloat(item.deposit_amount) || 0;
                totalRemaining += parseFloat(item.remaining_amount) || 0;

                if (item.completion_status.toLowerCase() === 'pending') {
                    pendingCount++;
                } else if (item.completion_status.toLowerCase() === 'completed') {
                    completedCount++;
                }
            });

            // Update summary cards
            document.getElementById('totalGroups').textContent = data.length;
            document.getElementById('totalAmount').textContent = '$' + totalAmount.toFixed(2);
            document.getElementById('totalDeposits').textContent = '$' + totalDeposits.toFixed(2);
            document.getElementById('totalRemaining').textContent = '$' + totalRemaining.toFixed(2);

            // Update progress bars
            const depositsPercentage = (totalDeposits / totalAmount) * 100;
            const remainingPercentage = (totalRemaining / totalAmount) * 100;
            document.getElementById('depositsProgress').style.width = depositsPercentage + '%';
            document.getElementById('remainingProgress').style.width = remainingPercentage + '%';

            // Update status counts
            document.getElementById('pendingCount').textContent = pendingCount;
            document.getElementById('completedCount').textContent = completedCount;
        }

        function updateMonthlyDistribution(data) {
            const monthlyData = {};
            const months = ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 
                          'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'];
            
            // Initialize all months with 0
            months.forEach(month => monthlyData[month] = 0);

            // Count groups per month
            data.forEach(item => {
                if (monthlyData.hasOwnProperty(item.month)) {
                    monthlyData[item.month]++;
                }
            });

            // Find the maximum number of groups for percentage calculation
            const maxGroups = Math.max(...Object.values(monthlyData));

            // Update the table
            const tbody = document.getElementById('monthlyDistributionBody');
            tbody.innerHTML = '';

            months.forEach(month => {
                const count = monthlyData[month];
                const percentage = maxGroups > 0 ? (count / maxGroups * 100) : 0;
                
                const row = document.createElement('tr');
                row.style.cursor = 'pointer';
                row.onclick = () => showMonthGroups(month);
                row.innerHTML = `
                    <td>${month}</td>
                    <td>${count} Groups</td>
                    <td style="width: 50%">
                        <div class="distribution-bar" style="width: ${percentage}%"></div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function showMonthGroups(month) {
            // Filter groups for the selected month
            const monthGroups = itineraryData.filter(item => item.month === month);
            
            // Update modal title
            document.getElementById('selectedMonth').textContent = month;
            
            // Update modal content
            const tbody = document.getElementById('monthGroupsBody');
            tbody.innerHTML = '';
            
            if (monthGroups.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center">No groups found for ${month}</td>
                    </tr>
                `;
            } else {
                monthGroups.forEach(group => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${group.group_name}</td>
                        <td>${group.starting_location}</td>
                        <td>${group.final_destination}</td>
                        <td>
                            <span class="badge ${group.completion_status.toLowerCase() === 'completed' ? 'bg-success' : 'bg-warning'}">
                                ${group.completion_status}
                            </span>
                        </td>
                        <td>$${parseFloat(group.total_amount).toFixed(2)}</td>
                    `;
                    tbody.appendChild(row);
                });
            }
            
            // Show the modal
            monthGroupsModal.show();
        }

        function showAlert(title, message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
            
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="bi ${icon} me-2"></i>
                    <strong>${title}</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            const alertContainer = document.createElement('div');
            alertContainer.style.position = 'fixed';
            alertContainer.style.top = '20px';
            alertContainer.style.right = '20px';
            alertContainer.style.zIndex = '9999';
            alertContainer.style.minWidth = '300px';
            alertContainer.style.maxWidth = '500px';
            alertContainer.innerHTML = alertHtml;
            
            // Remove any existing alerts
            const existingAlerts = document.querySelectorAll('.alert-container');
            existingAlerts.forEach(alert => alert.remove());
            
            // Add alert-container class for easy removal
            alertContainer.classList.add('alert-container');
            document.body.appendChild(alertContainer);
            
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) {
                    alert.classList.remove('show');
                    setTimeout(() => alertContainer.remove(), 150);
                }
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle functionality
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }

            // Close sidebar when clicking outside
            document.addEventListener('click', function(event) {
                if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html> 