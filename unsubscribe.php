<?php
require 'config.php';
include 'includes/connection.php';
include 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  $fileName = true;
  header("Location: login" . ($fileName ? ".php" : ""));
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $profile_user_id = $_POST['profile_user_id'];

  if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql_user = "SELECT * FROM users WHERE id = $user_id";
    $result_user = $conn->query($sql_user);
    $row_user = $result_user->fetch_assoc();

    $sql_unsubscribe = "DELETE FROM subscribers WHERE user_id = $user_id AND subscribed_user_id = $profile_user_id";
    $conn->query($sql_unsubscribe);
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
  }
}
?>