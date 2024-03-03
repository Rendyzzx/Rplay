<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

/*function checkAuth() {
  if (!isset($_SESSION['user_id'])) {
    $fileName = true;
    header("Location: login" . ($fileName ? ".php" : ""));
    exit();
  }
}*/

function checkUsernameExists($conn, $username) {
  $sql = "SELECT * FROM users WHERE username = '$username'";
  $result = $conn->query($sql);
  return $result->num_rows > 0;
}

function checkEmailExists($conn, $email) {
  $sql = "SELECT * FROM users WHERE email = '$email'";
  $result = $conn->query($sql);
  return $result->num_rows > 0;
}

function checkPhoneNumberExists($conn, $phoneNumber) {
  $sql = "SELECT * FROM users WHERE phone_number = '$phoneNumber'";
  $result = $conn->query($sql);
  return $result->num_rows > 0;
}

function generateVerificationCode($length = 8) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $code = '';
  $character_count = strlen($characters);

  for ($i = 0; $i < $length; $i++) {
    $code .= $characters[rand(0, $character_count - 1)];
  }

  return $code;
}

function hashPassword($password) {
  return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
  return password_verify($password, $hash);
}

function getCurrentUserId() {
  if (isset($_SESSION['user_id'])) {
    return $_SESSION['user_id'];
  } else {
    return null;
  }
}

function checkSubscription($current_user_id, $profile_user_id) {
  global $conn;

  if ($current_user_id) {
    $sql_check_subscription = "SELECT COUNT(*) AS count FROM subscribers WHERE subscriber_user_id = $current_user_id AND subscribed_user_id = $profile_user_id";
    $result_check_subscription = $conn->query($sql_check_subscription);

    if ($result_check_subscription !== false) {
      $row_check_subscription = $result_check_subscription->fetch_assoc();
      $count = $row_check_subscription['count'];

      return $count > 0;
    } else {
      return false;
    }
  } else {
    return false;
  }
}

function subscribeUser($subscriber_user_id, $subscribed_user_id) {
  global $conn;

  $sql_subscribe = "INSERT INTO subscribers (subscriber_user_id, subscribed_user_id) VALUES ($subscriber_user_id, $subscribed_user_id)";
  $conn->query($sql_subscribe);
}

function unsubscribeUser($subscriber_user_id, $subscribed_user_id) {
  global $conn;

  $sql_unsubscribe = "DELETE FROM subscribers WHERE subscriber_user_id = $subscriber_user_id AND subscribed_user_id = $subscribed_user_id";
  $conn->query($sql_unsubscribe);
}

function getCurrentPageURL() {
  $pageURL = 'http';

  if ($_SERVER["HTTPS"] == "on") {
    $pageURL .= "s";
  }

  $pageURL .= "://";

  if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
  } else {
    $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
  }

  return $pageURL;
}

function generateUniqueFileName($filename) {
  $path_parts = pathinfo($filename);
  $extension = $path_parts['extension'];
  $basename = $path_parts['filename'];

  $unique_name = $basename . '_' . uniqid() . '.' . $extension;
  return $unique_name;
}

function convertToWIB($time) {
  $date = new DateTime($time, new DateTimeZone('UTC'));
  $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
  return $date->format('Y-m-d H:i:s');
}

function formatViews($views) {
  if ($views >= 1000000000) {
    return round($views / 1000000000, 1) . 'B';
  } elseif ($views >= 1000000) {
    return round($views / 1000000, 1) . 'M';
  } elseif ($views >= 1000) {
    return round($views / 1000, 1) . 'K';
  } else {
    return $views;
  }
}

function timeDifference($uploaded_at) {
  $now = time();
  date_default_timezone_set('Asia/Jakarta');
  $uploaded_time = strtotime($uploaded_at);
  $diff = $now - $uploaded_time;

  if ($diff < 60) {
    return "Just now";
  } elseif ($diff < 3600) {
    $minutes = floor($diff / 60);
    return "$minutes minutes ago";
  } elseif ($diff < 86400) {
    $hours = floor($diff / 3600);
    return "$hours hours ago";
  } elseif ($diff < 604800) {
    $days = floor($diff / 86400);
    return "$days days ago";
  } elseif ($diff < 2592000) {
    $weeks = floor($diff / 604800);
    return "$weeks weeks ago";
  } elseif ($diff < 31536000) {
    $months = floor($diff / 2592000);
    return "$months months ago";
  } else {
    $years = floor($diff / 31536000);
    return "$years years ago";
  }
}

function makeLinksClickable($text) {
  $text = nl2br($text);

  $pattern = '/#(\w+)/';
  $replacement = '<a style="color: #007bff; text-decoration: none;" href="search' . ($fileName ? '.php' : '') . '?q=%23$1">$0</a>';
  $text = preg_replace($pattern, $replacement, $text);

  $urlPattern = '/((?:http|https):\/\/[^\s<>,]+)/';
  $urlReplacement = '<a style="color: #007bff; text-decoration: none;" href="$1" target="_blank">$1</a>';
  $text = preg_replace($urlPattern, $urlReplacement, $text);

  return $text;
}
?>