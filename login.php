<?php
require 'config.php';
include 'includes/connection.php';
include 'includes/functions.php';

session_start();

if (isset($_SESSION['user_id'])) {
  header("Location: /?page=home");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $sql = "SELECT * FROM users WHERE username = '$username'";
  $result = $conn->query($sql);

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $user_id = $row['id'];
    $hashed_password = $row['password'];
    $is_verified = $row['is_verified'];

    if ($is_verified == 1 && verifyPassword($password, $hashed_password)) {
      $_SESSION['user_id'] = $user_id;
      header("Location: /?page=home");
      exit();
    } elseif ($is_verified == 0) {
      echo "<script>alert('Your account is not verified yet. Please check your email for verification instructions.')</script>";
    } else {
      echo "<script>alert('Invalid username or password!')</script>";
    }
  } else {
    echo "<script>alert('Invalid username or password!')</script>";
  }
}
?>

<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$baseURL = $protocol . '://' . $_SERVER['HTTP_HOST'];
$imageBannerURL = $baseURL . '/src/img/banner.jpg';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Login - <?php echo $title; ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($keywords); ?>">
  <meta property="og:title" content="Login - <?php echo htmlspecialchars($title); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($description); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($imageBannerURL); ?>">
  <meta property="og:image:alt" content="Login - <?php echo htmlspecialchars($title); ?>">

  <link rel="icon" type="image/png" href="https://cdn.danitechno.com/dtubein/img/favicon.png">

  <link rel="stylesheet" href="<?php echo $tailwindCSS; ?>">
  <link rel="stylesheet" href="<?php echo $fontAwesome; ?>">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/main.css">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/loading.css">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/auth.css">
</head>

<body class="flex justify-center items-center h-screen">
  <div class="loading-spinner">
    <div class="spinner"></div>
  </div>

  <div class="w-1/1">
    <h2 class="text-3xl font-bold mb-6">Login</h2>
    <form method="POST" action="">
      <div class="mb-4">
        <label class="block mb-2" for="username">Username</label>
        <div class="relative">
          <span class="absolute left-4 top-3 text-gray-400">
            <i class="fas fa-user"></i>
          </span>
          <input class="w-full px-10 py-2 border rounded-lg" type="username" id="username" name="username" placeholder="Enter your username" required>
        </div>
      </div>
      <div class="mb-4">
        <label class="block mb-2" for="password">Password</label>
        <div class="relative">
          <span class="absolute left-4 top-3 text-gray-400">
            <i class="fas fa-lock"></i>
          </span>
          <input class="w-full px-10 py-2 border rounded-lg" type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>
      </div>
      <div class="flex items-center mb-2">
        <a class="text-blue-500 text-sm mb-0 mr-auto" href="javascript:alert('Coming Soon!')">Forgot password?</a>
      </div>
      <div class="flex items-center mb-4">
        <input class="mr-2" type="checkbox" id="remember" name="remember">
        <label for="remember">Remember me</label>
      </div>
      <button class="w-full bg-blue-500 dark-btn hover:bg-blue-600 dark-btn text-white font-bold py-2 rounded-lg" type="submit">Login</button>
    </form>
    <p class="text-center text-2xl m-4">
      or
    </p>
    <div class="mb-4">
      <button class="w-full bg-black dark-btn hover:bg-gray-800 dark-btn text-white font-bold py-2 rounded-lg" onclick="alert('Coming Soon!')"><i class="fab fa-google"></i> Sign In with Google</button>
    </div>
    <div class="mb-4">
      <button class="w-full bg-black dark-btn hover:bg-gray-800 dark-btn text-white font-bold py-2 rounded-lg" onclick="alert('Coming Soon!')"><i class="fab fa-facebook"></i> Sign In with Facebook</button>
    </div>
    <div class="mb-4">
      <button class="w-full bg-black dark-btn hover:bg-gray-800 dark-btn text-white font-bold py-2 rounded-lg" onclick="alert('Coming Soon!')"><i class="fab fa-github"></i> Sign In with GitHub</button>
    </div>
    <p class="mt-4 text-center text-sm">
      Don't have an account? <a class="text-blue-500" href="register<?php echo $fileName ? ".php" : ""; ?>">Create now</a>
    </p>
  </div>

  <script src="https://cdn.danitechno.com/dtubein/js/main.js" defer></script>
  <script src="https://cdn.danitechno.com/dtubein/js/loading.js"></script>
  <script src="https://cdn.danitechno.com/dtubein/js/auth.js"></script>

</body>

</html>