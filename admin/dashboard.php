<<<<<<< HEAD
<?php session_start(); if (!isset($_SESSION['admin'])) header('Location: login.php'); ?>
<!DOCTYPE html>
<html><head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light">
<div class="container py-5">
  <h2 class="mb-4">Admin Dashboard</h2>
  <div class="list-group">
    <a href="manage_projects.php" class="list-group-item">Manage Projects</a>
    <a href="manage_blogs.php" class="list-group-item">Manage Blogs</a>
    <a href="manage_team.php" class="list-group-item">Manage Team</a>
    <a href="manage_stats.php" class="list-group-item">Manage Stats</a>
    <a href="manage_user.php" class="list-group-item">Manage Users</a>
    <a href="messages.php" class="list-group-item">View Messages</a>
    <a href="logout.php" class="list-group-item text-danger">Logout</a>
  </div>
</div></body></html>
=======
<?php
require_once 'includes/header.php';
?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Projects</h5>
                <?php
                $stmt = $conn->query("SELECT COUNT(*) FROM projects");
                $count = $stmt->fetchColumn();
                ?>
                <h2 class="card-text"><?= $count; ?></h2>
                <a href="manage-projects.php" class="text-white">View all projects</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Team Members</h5>
                <?php
                $stmt = $conn->query("SELECT COUNT(*) FROM team_members");
                $count = $stmt->fetchColumn();
                ?>
                <h2 class="card-text"><?= $count; ?></h2>
                <a href="manage-team.php" class="text-white">View all team members</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Blog Posts</h5>
                <?php
                $stmt = $conn->query("SELECT COUNT(*) FROM blog_posts");
                $count = $stmt->fetchColumn();
                ?>
                <h2 class="card-text"><?= $count; ?></h2>
                <a href="manage-blog.php" class="text-white">View all posts</a>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5>Recent Activity</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Activity</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= date('Y-m-d H:i'); ?></td>
                        <td>Admin logged in</td>
                    </tr>
                    <!-- You can add more activity logs here or fetch from a log table -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
>>>>>>> 40d2ea7f59aee763d902fac8d078a4356200de5b
