<?php
session_start();
include '../db.php';
if (!isset($_SESSION['admin'])) header('Location: login.php');

if (isset($_POST['add'])) {
    $label = $_POST['label'];
    $value = $_POST['value'];
    $conn->query("INSERT INTO stats (label, value) VALUES ('$label', $value)");
}
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM stats WHERE id=" . $_GET['delete']);
}
$stats = $conn->query("SELECT * FROM stats");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Stats</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h3>Manage Stats</h3>
  <form method="post" class="mb-3">
    <input name="label" class="form-control mb-2" placeholder="Label">
    <input name="value" type="number" class="form-control mb-2" placeholder="Value">
    <button name="add" class="btn btn-primary">Add Stat</button>
  </form>
  <ul class="list-group">
  <?php while($s = $stats->fetch_assoc()): ?>
    <li class="list-group-item">
      <strong><?= $s['label'] ?>:</strong> <?= $s['value'] ?>
      <a href="?delete=<?= $s['id'] ?>" class="btn btn-sm btn-danger float-end">Delete</a>
    </li>
  <?php endwhile; ?>
  </ul>
</div>
</body>
</html>

