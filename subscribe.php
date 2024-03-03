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
  if (isset($_POST['profile_user_id'])) {
    $profile_user_id = $_POST['profile_user_id'];
    $user_id = $_SESSION['user_id'];

    $sql_check_subscription = "SELECT * FROM subscribers WHERE user_id = $user_id AND subscribed_user_id = $profile_user_id";
    $result_check_subscription = $conn->query($sql_check_subscription);

    if ($result_check_subscription->num_rows > 0) {
      $sql_unsubscribe = "DELETE FROM subscribers WHERE user_id = $user_id AND subscribed_user_id = $profile_user_id";
      $conn->query($sql_unsubscribe);
      header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
      $sql_subscribe = "INSERT INTO subscribers (user_id, subscribed_user_id) VALUES ($user_id, $profile_user_id)";
      $conn->query($sql_subscribe);
      header("Location: " . $_SERVER['HTTP_REFERER']);
      exit();
    }
  }
}
?>