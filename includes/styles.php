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
    margin: 0;
    color: white;
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

/* Notifications Styles */
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

/* User Profile Styles */
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

/* Responsive Styles */
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

@media (max-width: 576px) {
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
</style> 