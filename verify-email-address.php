<?php
require 'config.php';
include 'includes/connection.php';
include 'includes/functions.php';

$verification_code = $_GET['code'];

$sql = "SELECT * FROM users WHERE verification_code = '$verification_code'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $sql = "UPDATE users SET is_verified = 1 WHERE verification_code = '$verification_code'";
  if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Account verified successfully. You can now login!'); window.location.href = 'login" . ($fileName ? '.php' : '') . "';</script>";
  } else {
    echo "<script>alert('Error updating record: ' . $conn->error); window.location.href = 'register" . ($fileName ? '.php' : '') . "';</script>";
  }
} else {
  echo "<script>alert('Invalid verification code!'); window.location.href = 'register" . ($fileName ? '.php' : '') . "';</script>";
}
?>