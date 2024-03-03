<?php
require 'config.php';
include 'includes/connection.php';

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (isset($_POST['comment_id'])) {
  $comment_id = $_POST['comment_id'];
  $user_id = $_SESSION['user_id'];

  $sql_check_like = "SELECT id FROM comment_likes WHERE user_id = $user_id AND comment_id = $comment_id";
  $result_check_like = $conn->query($sql_check_like);

  if ($result_check_like->num_rows > 0) {
    $sql_delete_like = "DELETE FROM comment_likes WHERE user_id = $user_id AND comment_id = $comment_id";
    $conn->query($sql_delete_like);
  } else {
    $sql_add_like = "INSERT INTO comment_likes (user_id, comment_id) VALUES ($user_id, $comment_id)";
    $conn->query($sql_add_like);
  }

  $sql_updated_like_count = "SELECT COUNT(*) AS like_count FROM comment_likes WHERE comment_id = $comment_id";
  $result_updated_like_count = $conn->query($sql_updated_like_count);
  $row_updated_like_count = $result_updated_like_count->fetch_assoc();
  $like_count = $row_updated_like_count['like_count'];

  echo $like_count;
}
?>