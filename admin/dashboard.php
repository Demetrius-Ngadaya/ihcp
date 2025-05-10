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
