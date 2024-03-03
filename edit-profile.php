<?php
require 'config.php';
include 'includes/connection.php';
include 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  $fileName = true;
  header("Location: login" . ($fileName ? ".php" : ""));
  exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update_public_profile'])) {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $bio = $_POST['bio'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    $sql_check_username = "SELECT id FROM users WHERE username = '$username' AND id != $user_id";
    $result_check_username = $conn->query($sql_check_username);

    if ($result_check_username->num_rows > 0) {
      echo "<script>alert('Username already exists. Please choose a different username!'); window.location.href = 'edit-profile';</script>";
      exit();
    }

    $sql_update = "UPDATE users SET full_name = '$full_name', username = '$username', bio = '$bio', email = '$email', phone_number = '$phone_number' WHERE id = $user_id";
    if ($conn->query($sql_update) === TRUE) {
      header("Location: edit-profile" . ($fileName ? ".php" : ""));
      exit();
    } else {
      echo "Error updating profile: " . $conn->error;
    }
  } elseif (isset($_POST['update_profile_picture'])) {
    $profile_picture = $_FILES['profile_picture'];
    $profile_picture_name = $profile_picture['name'];
    $profile_picture_temp = $profile_picture['tmp_name'];

    $profile_picture_type = $profile_picture['type'];
    if ($profile_picture_type !== 'image/jpeg' && $profile_picture_type !== 'image/jpg' && $profile_picture_type !== 'image/png') {
      echo "Error: Invalid file type. Only JPEG, JPG, and PNG images are allowed.";
      exit();
    }

    $profile_picture_path = 'uploads/profile-pictures/' . $profile_picture_name;
    move_uploaded_file($profile_picture_temp, $profile_picture_path);

    $sql_update_picture = "UPDATE users SET profile_picture = '$profile_picture_name' WHERE id = $user_id";
    if ($conn->query($sql_update_picture) === TRUE) {
      header("Location: edit-profile" . ($fileName ? ".php" : ""));
      exit();
    } else {
      echo "Error updating profile picture: " . $conn->error;
    }
  } elseif (isset($_POST['update_password'])) {
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql_update_password = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
    if ($conn->query($sql_update_password) === TRUE) {
      header("Location: edit-profile" . ($fileName ? ".php" : ""));
      exit();
    } else {
      echo "Error updating password: " . $conn->error;
    }
  }
}

$sql_user = "SELECT * FROM users WHERE id = $user_id";
$result_user = $conn->query($sql_user);

if ($result_user->num_rows > 0) {
  $user_row = $result_user->fetch_assoc();
} else {
  echo "User not found.";
  exit();
}

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $sql_user = "SELECT * FROM users WHERE id = $user_id";
  $result_user = $conn->query($sql_user);
  $row_user = $result_user->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Edit Profile - <?php echo htmlspecialchars($title); ?></title>

  <link rel="icon" type="image/png" href="https://cdn.danitechno.com/dtubein/img/favicon.png">

  <link rel="stylesheet" href="<?php echo $tailwindCSS; ?>">
  <link rel="stylesheet" href="<?php echo $fontAwesomeBeta; ?>">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/main.css">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/loading.css">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/edit-profile.css">
</head>

<body>
  <div class="loading-spinner">
    <div class="spinner"></div>
  </div>

  <div class="header">
    <h1 class="text-1xl font-bold text-white"><a href="profile<?php echo $fileName ? ".php" : "" ?>?username=<?php echo $row_user['username']; ?>"><i class="fas fa-arrow-left"></i></a> Edit Profile</h1>
  </div>

  <div class="container mx-auto p-4">
    <h2 class="block text-white text-lg font-bold mb-2">Public profile</h2>
    <form method="POST" action="">
      <div class="mb-4">
        <label class="block text-gray-200 text-sm font-bold mb-2" for="fullname">
          Full name
        </label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-200 leading-tight focus:outline-none focus:shadow-outline" type="text" id="fullname" name="full_name" value="<?php echo $user_row['full_name']; ?>" required>
      </div>

      <div class="mb-4">
        <label class="block text-gray-200 text-sm font-bold mb-2" for="username">
          Username
        </label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-200 leading-tight focus:outline-none focus:shadow-outline" type="text" id="username" name="username" value="<?php echo $user_row['username']; ?>" required>
      </div>

      <div class="mb-4">
        <label class="block text-gray-200 text-sm font-bold mb-2" for="bio">
          Bio
        </label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-200 leading-tight focus:outline-none focus:shadow-outline" type="text" id="bio" name="bio" value="<?php echo $user_row['bio']; ?>" required>
      </div>

      <div class="mb-4">
        <label class="block text-gray-200 text-sm font-bold mb-2" for="email">
          Email
        </label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-200 leading-tight focus:outline-none focus:shadow-outline" type="email" id="email" name="email" value="<?php echo $user_row['email']; ?>" required>
      </div>

      <div class="mb-4">
        <label class="block text-gray-200 text-sm font-bold mb-2" for="phonenumber">
          Phone number (Example: 628xxx)
        </label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-200 leading-tight focus:outline-none focus:shadow-outline" type="number" id="phonenumber" name="phone_number" value="<?php echo $user_row['phone_number']; ?>" required>
      </div>

      <div class="flex items-left justify-left">
        <input class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" name="update_public_profile" value="Save">
      </div>
    </form>

    <br />

    <h2 class="block text-white text-lg font-bold mb-2">Profile picture</h2>
    <form method="POST" action="" enctype="multipart/form-data">
      <div class="mb-4">
        <label class="block text-gray-200 text-sm font-bold mb-2" for="profilepicture">
          Profile picture (Only JPEG, JPG, and PNG images files are allowed)
        </label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-200 leading-tight focus:outline-none focus:shadow-outline" id="profilepicture" name="profile_picture" type="file" accept="image/jpeg, image/jpg, image/png" required>
      </div>

      <div class="flex items-left justify-left">
        <input class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" name="update_profile_picture" value="Save">
      </div>
    </form>

    <br />

    <h2 class="block text-white text-lg font-bold mb-2">Security</h2>
    <form method="POST" action="">
      <div class="mb-4">
        <label class="block text-gray-200 text-sm font-bold mb-2" for="password">
          Password
        </label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-200 leading-tight focus:outline-none focus:shadow-outline" type="text" id="password" name="password" value="" required>
      </div>

      <div class="flex items-left justify-left">
        <input class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" name="update_password" value="Save">
      </div>
    </form>
  </div>

  <script src="https://cdn.danitechno.com/dtubein/js/main.js" defer></script>
  <script src="https://cdn.danitechno.com/dtubein/js/loading.js"></script>
</body>

</html>