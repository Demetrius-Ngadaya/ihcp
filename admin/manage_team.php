<?php
session_start();
include '../db.php';
if (!isset($_SESSION['admin'])) header('Location: login.php');

if (isset($_POST['add']) && isset($_FILES['image'])) {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $image = $_FILES['image']['name'];
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
    $conn->query("INSERT INTO team (name, role, image_path) VALUES ('$name', '$role', '$image')");
}
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM team WHERE id=" . $_GET['delete']);
}
$team = $conn->query("SELECT * FROM team");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Team</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h3>Manage Team</h3>
  <form method="post" enctype="multipart/form-data" class="mb-3">
    <input name="name" class="form-control mb-2" placeholder="Name" required>
    <input name="role" class="form-control mb-2" placeholder="Role">
    <input type="file" name="image" class="form-control mb-2" required>
    <button name="add" class="btn btn-primary">Add Member</button>
  </form>
  <div class="row">
  <?php while($t = $team->fetch_assoc()): ?>
    <div class="col-md-4 mb-3">
      <div class="card">
        <img src="../uploads/<?= $t['image_path'] ?>" class="card-img-top" height="200">
        <div class="card-body">
          <h5><?= $t['name'] ?></h5>
          <p><?= $t['role'] ?></p>
          <a href="?delete=<?= $t['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
  </div>
</div>
</body>
</html>
