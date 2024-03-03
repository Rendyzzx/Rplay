<?php
require 'config.php';
include 'includes/connection.php';
include 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  $fileName = true;
  header("Location: login" . ($fileName ? ".php" : ""));
  exit();
}

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $sql_user = "SELECT * FROM users WHERE id = $user_id";
  $result_user = $conn->query($sql_user);
  $row_user = $result_user->fetch_assoc();
}

ini_set('post_max_size', '500M');
ini_set('upload_max_filesize', '500M');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $user_id = $_SESSION['user_id'];
  $title = $_POST['title'];
  $description = $_POST['description'];

  $thumbnail = $_FILES['thumbnail']['name'];
  $thumbnail_temp = $_FILES['thumbnail']['tmp_name'];

  $thumbnail_type = $_FILES['thumbnail']['type'];
  if ($thumbnail_type !== 'image/jpeg' && $thumbnail_type !== 'image/jpg' && $thumbnail_type !== 'image/png') {
    echo "Error: Invalid file type. Only JPEG, JPG, and PNG images are allowed for thumbnails.";
    exit();
  }

  $thumbnail_name = generateUniqueFileName($thumbnail);
  $thumbnail_path = 'uploads/thumbnails/' . $thumbnail_name;
  move_uploaded_file($thumbnail_temp, $thumbnail_path);

  $video = $_FILES['video']['name'];
  $video_temp = $_FILES['video']['tmp_name'];

  $video_type = $_FILES['video']['type'];
  if ($video_type !== 'video/mp4') {
    echo "Error: Invalid file type. Only MP4 videos are allowed.";
    exit();
  }

  $video_size = $_FILES['video']['size'];
  $max_video_size = 500 * 1024 * 1024; // 500 MiB
  if ($video_size > $max_video_size) {
    echo "Error: The video file size exceeds the maximum limit of 500MiB.";
    exit();
  }

  $video_name = generateUniqueFileName($video);
  $video_path = 'uploads/videos/' . $video_name;
  move_uploaded_file($video_temp, $video_path);

  $sql = "INSERT INTO videos (user_id, title, description, thumbnail, video)
            VALUES ('$user_id', '$title', '$description', '$thumbnail_name', '$video_name')";

  if ($conn->query($sql) === TRUE) {
    header("Location: profile" . ($fileName ? ".php" : "") . "?username=" . $row_user['username']);
    exit();
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Upload - <?php echo $title; ?></title>

  <link rel="icon" type="image/png" href="https://cdn.danitechno.com/dtubein/img/favicon.png">

  <link rel="stylesheet" href="<?php echo $tailwindCSS; ?>">
  <link rel="stylesheet" href="<?php echo $fontAwesomeBeta; ?>">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/main.css">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/loading.css">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/upload.css">
</head>

<body>
  <div class="loading-spinner">
    <div class="spinner"></div>
  </div>

  <div class="header">
    <h1 class="text-1xl font-bold text-white"><a href="/"><i class="fas fa-arrow-left"></i></a> Upload Video</h1>
  </div>

  <div class="container mx-auto p-4">

    <form method="POST" action="" enctype="multipart/form-data">
      <div class="mb-4">
        <label class="block text-gray-200 text-sm font-bold mb-2" for="title">
          Title (Min length: 5 and Max length: 100)
        </label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-200 leading-tight focus:outline-none focus:shadow-outline" id="title" name="title" type="text" placeholder="Enter title" minlength="5" maxlength="100" required>
      </div>

      <div class="mb-4">
        <label class="block text-gray-200 text-sm font-bold mb-2" for="description">
          Description (Min length: 50 and Max length: 5000)
        </label>
        <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-200 leading-tight focus:outline-none focus:shadow-outline" id="description" name="description" placeholder="Enter description" rows="3" minlength="50" maxlength="5000" required></textarea>
      </div>

      <div class="mb-4">
        <label class="block text-gray-200 text-sm font-bold mb-2" for="thumbnails">
          Thumbnails (Only JPEG, JPG, and PNG images files are allowed)
        </label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-200 leading-tight focus:outline-none focus:shadow-outline" id="thumbnails" name="thumbnail" type="file" accept="image/jpeg, image/jpg, image/png" onchange="validateImageAspectRatio()" required>
      </div>

      <div class="mb-4">
        <label class="block text-gray-200 text-sm font-bold mb-2" for="video">
          Video (Only 16:9 aspect ratio and Max filesize: 500MiB are allowed)
        </label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-200 leading-tight focus:outline-none focus:shadow-outline" id="video" name="video" type="file" accept="video/mp4" onchange="validateVideoAspectRatio()" required>
      </div>

      <div class="flex items-center justify-center">
        <button class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
          Upload
        </button>
      </div>
    </form>
  </div>

  <script src="https://cdn.danitechno.com/dtubein/js/main.js" defer></script>
  <script src="https://cdn.danitechno.com/dtubein/js/loading.js"></script>
  <script src="https://cdn.danitechno.com/dtubein/js/upload.js"></script>

</body>

</html>