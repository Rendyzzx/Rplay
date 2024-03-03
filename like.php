<?php
require 'config.php';
include 'includes/connection.php';
include 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['video_id']) && is_numeric($_POST['video_id'])) {
    $video_id = $_POST['video_id'];

    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }

    if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
      $user_id = $_SESSION['user_id'];

      $sql_check_like = "SELECT * FROM likes WHERE video_id = $video_id AND user_id = $user_id";
      $result_check_like = $conn->query($sql_check_like);

      if ($result_check_like && $result_check_like->num_rows > 0) {
        $sql_delete_like = "DELETE FROM likes WHERE video_id = $video_id AND user_id = $user_id";
        $conn->query($sql_delete_like);
      } else {
        $sql_add_like = "INSERT INTO likes (video_id, user_id, action) VALUES ($video_id, $user_id, 'like')";
        $conn->query($sql_add_like);
      }

      $sql_likes = "SELECT COUNT(*) AS like_count FROM likes WHERE video_id = $video_id AND action = 'like'";
      $result_likes = $conn->query($sql_likes);

      if ($result_likes) {
        $row_likes = $result_likes->fetch_assoc();
        $like_count = $row_likes['like_count'];
        echo json_encode(['status' => 'success', 'like_count' => $like_count]);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch like count']);
      }
    } else {
      echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid video ID']);
  }
} else {
  echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>