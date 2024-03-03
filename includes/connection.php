
<?php
require 'config.php';


// Connect to database (MySql)
$conn = new mysqli($hostname, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Error, Connection to database failed: " . $conn->connect_error);
}
?>