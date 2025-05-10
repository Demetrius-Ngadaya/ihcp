<?php
session_start();
include '../db.php';
if (!isset($_SESSION['admin'])) header('Location: login.php');

if (isset($_POST['add'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $conn->query("INSERT INTO admin (username, password) VALUES ('$username', '$password')");
}
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM admin WHERE id=" . $_GET['delete']);
}
$admins = $conn->query("SELECT * FROM admin");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h3>Manage Users</h3>
  <form method="post" class="mb-3">
    <input name="username" class="form-control mb-2" placeholder="Username" required>
    <input name="password" type="password" class="form-control mb-2" placeholder="Password" required>
    <button name="add" class="btn btn-primary">Add User</button>
  </form>
  <ul class="list-group">
  <?php while($a = $admins->fetch_assoc()): ?>
    <li class="list-group-item">
      <?= $a['username'] ?>
      <a href="?delete=<?= $a['id'] ?>" class="btn btn-sm btn-danger float-end">Delete</a>
    </li>
  <?php endwhile; ?>
  </ul>
</div>
</body>
</html>