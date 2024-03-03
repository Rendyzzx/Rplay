<?php
require 'config.php';
include 'includes/connection.php';
include 'includes/functions.php';

require 'lib/PHPMailer/src/PHPMailer.php';
require 'lib/PHPMailer/src/SMTP.php';
require 'lib/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

if (isset($_SESSION['user_id'])) {
  header("Location: /?page=home");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $full_name = $_POST['full_name'];
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $profile_picture = $_FILES['profile_picture']['name'];
  $profile_picture_tmp = $_FILES['profile_picture']['tmp_name'];

  if (checkUsernameExists($conn, $username)) {
    echo "<script>alert('Username already exists!'); window.location.href = 'register' . ($fileName ? '.php' : '');</script>";
    exit();
  }

  if (checkEmailExists($conn, $email)) {
    echo "<script>alert('Email already exists!'); window.location.href = 'register' . ($fileName ? '.php' : '');</script>";
    exit();
  }

  if ($password !== $confirm_password) {
    echo "<script>alert('Password and confirm password do not match!'); window.location.href = 'register' . ($fileName ? '.php' : '');</script>";
    exit();
  }

  $verification_code = generateVerificationCode();

  $hashedPassword = hashPassword($password);
  $sql = "INSERT INTO users (full_name, username, email, phone_number, password, verification_code, profile_picture) VALUES ('$full_name', '$username', '$email', 'NULL', '$hashedPassword', '$verification_code', '$profile_picture')";

  if ($conn->query($sql) === TRUE) {
    move_uploaded_file($profile_picture_tmp, "uploads/profile-pictures/$profile_picture");

    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = $SMTPHostname;
      $mail->SMTPAuth = $SMTPAuth;
      $mail->Username = $SMTPUsername;
      $mail->Password = $SMTPPassword;
      $mail->SMTPSecure = $SMTPSecure;
      $mail->Port = $SMTPPort;
      $mail->SMTPOptions = $SMTPOptions;

      $mail->setFrom($SMTPUsername, $SMTPSetFrom);
      $mail->addAddress($email, $full_name);

      $mail->Subject = 'Verify your email address';
      $mail->Body = "Dear $full_name,\n\nPlease click the following link to verify your account:\n\nhttp://{$_SERVER['HTTP_HOST']}/verify-email-address" . ($fileName ? ".php" : "") . ($verification_code ? "?code=$verification_code" : "");

      $mail->send();

      echo "<script>alert('Registration successful. Please check your email for verification!')</script>";
    } catch (Exception $e) {
      echo "<script>alert('Error sending email: {$mail->ErrorInfo}')</script>";
    }
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
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
  <title>Register - <?php echo $title; ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($keywords); ?>">
  <meta property="og:title" content="Register - <?php echo htmlspecialchars($title); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($description); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($imageBannerURL); ?>">
  <meta property="og:image:alt" content="Register - <?php echo htmlspecialchars($title); ?>">

  <link rel="icon" type="image/png" href="https://cdn.danitechno.com/dtubein/img/favicon.png">

  <link rel="stylesheet" href="<?php echo $tailwindCSS; ?>">
  <link rel="stylesheet" href="<?php echo $fontAwesome; ?>">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/main.css">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/loading.css">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/auth.css">
</head>

<body class="flex justify-center items-center h-100 p-10">
  <div class="loading-spinner">
    <div class="spinner"></div>
  </div>

  <div class="w-1/1">
    <h2 class="text-3xl font-bold mt-6 mb-6">Register</h2>
    <form method="POST" action="" enctype="multipart/form-data">
      <div class="mb-4">
        <label class="block mb-2" for="fullname">Full name</label>
        <div class="relative">
          <span class="absolute left-4 top-3 text-gray-400">
            <i class="fas fa-user"></i>
          </span>
          <input class="w-full px-10 py-2 border rounded-lg" type="text" id="fullname" name="full_name" placeholder="Your name" required>
        </div>
      </div>
      <div class="mb-4">
        <label class="block mb-2" for="username">Username</label>
        <div class="relative">
          <span class="absolute left-4 top-3 text-gray-400">
            <i class="fas fa-at"></i>
          </span>
          <input class="w-full px-10 py-2 border rounded-lg" type="text" id="username" name="username" placeholder="yourname" required oninput="validateInput()" pattern="^\S+$" title="Tidak boleh berisi spasi atau karakter kosong">
        </div>
      </div>
      <div class="mb-4">
        <label class="block mb-2" for="email">Email</label>
        <div class="relative">
          <span class="absolute left-4 top-3 text-gray-400">
            <i class="fas fa-envelope"></i>
          </span>
          <input class="w-full px-10 py-2 border rounded-lg" type="email" id="email" name="email" placeholder="youremail@example.com" required>
        </div>
      </div>
      <div class="mb-4">
        <label class="block mb-2" for="password">Password</label>
        <div class="relative">
          <span class="absolute left-4 top-3 text-gray-400">
            <i class="fas fa-lock"></i>
          </span>
          <input class="w-full px-10 py-2 border rounded-lg" type="password" id="password" name="password" placeholder="Create your password" required>
        </div>
      </div>
      <div class="mb-4">
        <label class="block mb-2" for="confirm_password">Confirm password</label>
        <div class="relative">
          <span class="absolute left-4 top-3 text-gray-400">
            <i class="fas fa-lock"></i>
          </span>
          <input class="w-full px-10 py-2 border rounded-lg" type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
        </div>
      </div>
      <div class="mb-4">
        <label class="block mb-2" for="profilepicture">Profile picture (Only JPEG, JPG, and PNG images files are allowed)</label>
        <div class="relative">
          <span class="absolute left-4 top-3 text-gray-400">
            <i class="fas fa-images"></i>
          </span>
          <input class="w-full px-10 py-2 border rounded-lg" type="file" id="profilepicture" name="profile_picture" accept="image/jpeg, image/jpg, image/png" required>
        </div>
      </div>
      <button class="w-full bg-blue-500 dark-btn hover:bg-blue-600 dark-btn text-white font-bold py-2 rounded-lg" type="submit">Register</button>
    </form>
    <p class="text-center text-2xl m-4">
      or
    </p>
    <div class="mb-4">
      <button class="w-full bg-black dark-btn hover:bg-gray-800 dark-btn text-white font-bold py-2 rounded-lg" onclick="alert('Coming Soon!')"><i class="fab fa-google"></i> Sign Up with Google</button>
    </div>
    <div class="mb-4">
      <button class="w-full bg-black dark-btn hover:bg-gray-800 dark-btn text-white font-bold py-2 rounded-lg" onclick="alert('Coming Soon!')"><i class="fab fa-facebook"></i> Sign Up with Facebook</button>
    </div>
    <div class="mb-4">
      <button class="w-full bg-black dark-btn hover:bg-gray-800 dark-btn text-white font-bold py-2 rounded-lg" onclick="alert('Coming Soon!')"><i class="fab fa-github"></i> Sign Up with GitHub</button>
    </div>
    <p class="mt-4 mb-6 text-center text-sm">
      Already have an account? <a class="text-blue-500" href="login<?php echo $fileName ? ".php" : ""; ?>">Login</a>
    </p>
  </div>

  <script src="https://cdn.danitechno.com/dtubein/js/main.js" defer></script>
  <script src="https://cdn.danitechno.com/dtubein/js/loading.js"></script>
  <script src="https://cdn.danitechno.com/dtubein/js/auth.js"></script>

</body>

</html>