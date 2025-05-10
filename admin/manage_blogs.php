<?php
session_start();
include '../db.php';
if (!isset($_SESSION['admin'])) header('Location: login.php');

if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $conn->query("INSERT INTO blogs (title, content, category) VALUES ('$title', '$content', '$category')");
}
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM blogs WHERE id=" . $_GET['delete']);
}
$blogs = $conn->query("SELECT * FROM blogs");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Blogs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h3>Manage Blogs</h3>
  <form method="post" class="mb-3">
    <input name="title" class="form-control mb-2" placeholder="Blog Title" required>
    <textarea name="content" class="form-control mb-2" placeholder="Blog Content"></textarea>
    <select name="category" class="form-control mb-2">
      <option value="primary">Primary</option>
      <option value="secondary">Secondary</option>
    </select>
    <button name="add" class="btn btn-primary">Add Blog</button>
  </form>
  <ul class="list-group">
  <?php while($b = $blogs->fetch_assoc()): ?>
    <li class="list-group-item">
      <strong><?= $b['title'] ?></strong> (<?= $b['category'] ?>)
      <p><?= $b['content'] ?></p>
      <a href="?delete=<?= $b['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
    </li>
  <?php endwhile; ?>
  </ul>
</div>
</body>
</html>

