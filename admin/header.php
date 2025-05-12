<?php
// session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IHPCL Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            min-height: 100vh;
            background-color: var(--secondary-color);
            color: white;
            position: fixed;
            width: 250px;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.2);
        }
        
        .sidebar ul.components {
            padding: 20px 0;
        }
        
        .sidebar ul li a {
            padding: 10px 20px;
            display: block;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s;
        }
        
        .sidebar ul li a:hover {
            color: white;
            background-color: rgba(0, 0, 0, 0.2);
            text-decoration: none;
        }
        
        .sidebar ul li.active > a {
            color: white;
            background-color: var(--primary-color);
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: none;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            font-weight: 600;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .alert {
            border-radius: 5px;
        }
        
        .img-thumbnail {
            max-width: 150px;
            height: auto;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header text-center">
                <h4>IHPCL Admin Panel</h4>
            </div>
            <ul class="list-unstyled components">
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                    <a href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'about_us.php' ? 'active' : '' ?>">
                    <a href="about_us.php"><i class="fas fa-info-circle me-2"></i> About Us</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'manage_projects.php' ? 'active' : '' ?>">
                    <a href="manage_projects.php"><i class="fas fa-project-diagram me-2"></i> Projects</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'manage_blogs.php' ? 'active' : '' ?>">
                    <a href="manage_blogs.php"><i class="fas fa-blog me-2"></i> Blogs</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'manage_team.php' ? 'active' : '' ?>">
                    <a href="manage_team.php"><i class="fas fa-users me-2"></i> Team</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'manage_stats.php' ? 'active' : '' ?>">
                    <a href="manage_stats.php"><i class="fas fa-chart-bar me-2"></i> Statistics</a>
                </li>
                    <li class="<?= basename($_SERVER['PHP_SELF']) == 'manage_picture.php' ? 'active' : '' ?>">
                    <a href="manage_picture.php"><i class="fas fa-users me-2"></i> Pictures</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'manage_video.php' ? 'active' : '' ?>">
                    <a href="manage_video.php"><i class="fas fa-chart-bar me-2"></i> Videos</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'manage_user.php' ? 'active' : '' ?>">
                    <a href="manage_user.php"><i class="fas fa-user-cog me-2"></i> Users</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : '' ?>">
                    <a href="messages.php"><i class="fas fa-envelope me-2"></i> Messages</a>
                </li>
                <li>
                    <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div class="main-content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary d-md-none">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="mb-0"><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?></h4>
                    <div class="d-flex align-items-center">
                        <span class="me-3">Welcome, <?= $_SESSION['admin'] ?></span>
                    </div>
                </div>
            </nav>