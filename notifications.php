<?php
require 'config.php';
include 'includes/connection.php';
include 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  $fileName = true;
  header("Location: login" . ($fileName ? ".php" : ""));
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Notifications - <?php echo $title; ?></title>

  <link rel="icon" type="image/png" href="https://cdn.danitechno.com/dtubein/img/favicon.png">

  <link rel="stylesheet" href="<?php echo $tailwindCSS; ?>">
  <link rel="stylesheet" href="<?php echo $fontAwesomeBeta; ?>">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/main.css">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/loading.css">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/notifications.css">
</head>

<body>
  <div class="loading-spinner">
    <div class="spinner"></div>
  </div>

  <div class="header">
    <h1 class="text-1xl font-bold text-white"><a href="<?php echo $_SERVER['HTTP_REFERER'] ?>"><i class="fas fa-arrow-left"></i></a> Notifications</h1>
  </div>

  <div class="notification">
    <img src="https://static.vecteezy.com/system/resources/previews/010/366/210/original/bell-icon-transparent-notification-free-png.png" alt="Notification Icon">
    <div class="content">
      <h4>No notifications found!</h4>
      <p>

      </p>
    </div>
  </div>

</div>
</main>

<script src="https://cdn.danitechno.com/dtubein/js/main.js" defer></script>
<script src="https://cdn.danitechno.com/dtubein/js/loading.js"></script>

</body>

</html>