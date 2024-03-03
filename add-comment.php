<?php
require 'config.php';
include 'includes/connection.php';
include 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  $fileName = true;
  header("Location: login" . ($fileName ? ".php" : ""));
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['video_id']) && isset($_POST['comment'])) {
  $video_id = $_POST['video_id'];
  $comment = $_POST['comment'];
  $user_id = $_SESSION['user_id'];

  $sql_add_comment = "INSERT INTO comments (user_id, video_id, comment) VALUES ($user_id, $video_id, '$comment')";
  $conn->query($sql_add_comment);

  header("Location: " . $_SERVER['HTTP_REFERER']);
  exit();
} else {
  header("Location: /?page=home");
  exit();
}
?>