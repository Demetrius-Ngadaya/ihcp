<?php
session_start(); include '../db.php';
if (!isset($_SESSION['admin'])) header('Location: login.php');

if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $status = $_POST['status'];
    $conn->query("INSERT INTO projects (title, description, status) VALUES ('$title', '$desc', '$status')");
}
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM projects WHERE id=" . $_GET['delete']);
}
$projects = $conn->query("SELECT * FROM projects");
?>
<!DOCTYPE html><html><head>
<title>Manage Projects</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light">
<div class="container py-5">
<h3>Manage Projects</h3>
<form method="post" class="mb-3">
  <input name="title" class="form-control mb-2" placeholder="Project Title" required>
  <textarea name="description" class="form-control mb-2" placeholder="Project Description"></textarea>
  <select name="status" class="form-control mb-2">
    <option value="current">Current</option>
    <option value="upcoming">Upcoming</option>
    <option value="completed">Completed</option>
  </select>
  <button name="add" class="btn btn-primary">Add Project</button>
</form>
<ul class="list-group">
<?php while($p = $projects->fetch_assoc()): ?>
  <li class="list-group-item">
    <strong><?= $p['title'] ?></strong> (<?= $p['status'] ?>)
    <p><?= $p['description'] ?></p>
    <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
  </li>
<?php endwhile; ?>
</ul></div></body></html>
