<?php
require 'config.php';
include 'includes/connection.php';
include 'includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (isset($_GET['page']) && $_GET['page'] === 'shorts') {
  echo "<script>alert('Coming Soon (NanaXD)!'); window.location.href = '/?page=home';</script>";
} elseif (isset($_GET['page']) && $_GET['page'] === 'collection') {
  echo "<script>alert('Coming Soon! (NanaXD)'); window.location.href = '/?page=home';</script>";
} elseif (isset($_GET['page']) && $_GET['page'] === 'upload') {
  header("Location: upload" . ($fileName ? ".php" : ""));
  exit();
} elseif (isset($_GET['page']) && $_GET['page'] === 'subscription') {
  if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT videos.*, users.username
            FROM videos
            INNER JOIN users ON videos.user_id = users.id
            WHERE videos.user_id IN (
                SELECT subscribed_user_id
                FROM subscribers
                WHERE user_id = $user_id
            )
            ORDER BY uploaded_at DESC";
  } else {
    header("Location: login" . ($fileName ? ".php" : ""));
    exit();
  }
} else {
  $sql = "SELECT videos.*, users.username
            FROM videos
            INNER JOIN users ON videos.user_id = users.id
            ORDER BY uploaded_at DESC";
}

$result = $conn->query($sql);

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $sql_user = "SELECT * FROM users WHERE id = $user_id";
  $result_user = $conn->query($sql_user);
  $row_user = $result_user->fetch_assoc();
}

$videos = array();

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $videos[] = $row;
  }

  shuffle($videos);
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
  <title><?php echo htmlspecialchars($title); ?> - Platform Berbagi Video</title>
  <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($keywords); ?>">
  <meta property="og:title" content="<?php echo htmlspecialchars($title); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($description); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($imageBannerURL); ?>">
  <meta property="og:image:alt" content="<?php echo htmlspecialchars($title); ?>">

  <link rel="icon" type="image/png" href="https://cdn.danitechno.com/dtubein/img/favicon.png">

  <link rel="stylesheet" href="<?php echo $tailwindCSS; ?>">
  <link rel="stylesheet" href="<?php echo $fontAwesome; ?>">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/main.css">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/loading.css">
  
  <!-- Mondiad area -->
  <meta name="mnd-ver" content="m6fug5ginq4en1n5kencmq" />
  <script async src="https://ss.mndsrv.com/native.js"></script>
  <script async src="https://ss.mndsrv.com/banner.js"></script>
</head>

<body>
  <div class="loading-spinner">
    <div class="spinner"></div>
  </div>

  <header class="py-2">
    <nav class="container mx-auto px-4 flex justify-between items-center">
      <div class="brand">
        <img src="https://cdn.danitechno.com/dtubein/img/icon.png" class="logo-img" />
        <div class="logo-text">
          &nbsp;<?php echo $title; ?>
        </div>
      </div>
      <div class="flex items-center space-x-4">
        <a href="notifications<?php echo $fileName ? ".php" : ""; ?>" class="text-white">
          <i class="fas fa-bell"></i>
        </a>
        <a href="search<?php echo $fileName ? ".php" : ""; ?>" class="text-white">
          <i class="fas fa-search"></i>
        </a>
        <?php if (isset($_SESSION['user_id'])) : ?>
        <a href="profile<?php echo $fileName ? ".php" : ""; ?>?username=<?php echo $row_user['username']; ?>" class="text-white">
          <img src="./uploads/profile-pictures/<?php echo $row_user['profile_picture']; ?>" class="w-6 h-6 rounded-full">
        </a>
        <?php else : ?>
        <a href="login<?php echo $fileName ? ".php" : ""; ?>" class="text-white">
          <i class="fas fa-user"></i>
        </a>
        <?php endif; ?>
      </div>
    </nav>
  </header>
  
  <main class="container mx-auto py-0">
    
    <!-- Mondiad area -->
    <div style="display: <?php echo $displayForAds; ?>;" align="center">
      <div data-mndazid="3c0b9cbe-560e-4870-9d8d-de5715e22dc1"></div>
    </div>
    <!-- End Mondiad area -->
      
    <div class="container mx-auto mt-0 grid gap-0 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">

      <?php
      if (!empty($videos)) {
        foreach ($videos as $video) {
          $video_id = $video['id'];
          $thumbnail = $video['thumbnail'];
          $title = $video['title'];
          $user_id = $video['user_id'];
          $views = formatViews($video['views']);
          $uploaded_at = timeDifference(convertToWIB($video['uploaded_at']));

          $sql_user = "SELECT * FROM users WHERE id = $user_id";
          $result_user = $conn->query($sql_user);
          $row_user = $result_user->fetch_assoc();
          $profile_picture = $row_user['profile_picture'];
          $channel = $row_user['full_name'];
          $username = $row_user['username'];
          ?>

          <div class="p-4 pt-2 pb-2">
            <a href="video<?php echo $fileName ? ".php" : ""; ?>?id=<?php echo $video_id; ?>">
              <div class="bg-black-800 rounded">
                <img class="w-full h-auto mb-4" style="aspect-ratio: 16/9;" src="./uploads/thumbnails/<?php echo $thumbnail; ?>" alt="<?php echo htmlspecialchars($title); ?>" />
                <div class="flex items-center text-white text-sm mb-2">
                  <img class="w-8 h-8 rounded-full mr-2" src="./uploads/profile-pictures/<?php echo $profile_picture; ?>" alt="<?php echo htmlspecialchars($channel); ?>" />
                  <div>
                    <h4 class="font-bold"><?php echo htmlspecialchars($title); ?></h4>
                    <?php $isMobile = "<script>document.write(window.innerWidth < 700);</script>"; ?>
                    <style>
                    @media (max-width: 768px) {
                      .mobile-channel-info {
                        display: inline;
                      }

                      .desktop-channel-info {
                        display: none;
                      }
                    }

                    @media (min-width: 700px) {
                      .mobile-channel-info {
                        display: none;
                      }

                      .desktop-channel-info {
                        display: inline;
                      }
                    }
                    </style>
                    <p class="text-gray-400 text-xs">
                      <span class="mobile-channel-info"><?php echo htmlspecialchars($channel); ?> • <?php echo $views; ?> × watched • <?php echo $uploaded_at; ?></span>
                      <span class="desktop-channel-info"><?php echo htmlspecialchars($channel); ?><br><?php echo $views; ?> × watched • <?php echo $uploaded_at; ?></span>
                    </p>
                  </div>
                </div>
              </div>
            </a>
          </div>

          <?php
        }
      } else {
        echo '<p class="ml-5">No videos found.</p>';
      }
      ?>

    </div>
  </main>
  
  <!-- Mondiad area -->
  <div style="display: <?php echo $displayForAds; ?>;" align="center">
    <div data-mndazid="3c0b9cbe-560e-4870-9d8d-de5715e22dc1"></div>
    <br />
  </div>
  <!-- End Mondiad area -->

  <br /><br />

  <footer>
    <ul class="flex justify-center items-center space-x-10">
      <li><a class="home-btn text-white"><i class="fas fa-home" aria-label="Home"></i><br> Home</a></li>
      <li><a class="shorts-btn text-white"><i class="fas fa-play" aria-label="Shorts"></i><br> Shorts</a></li>
      <li><a class="upload-btn text-white"><i class="fas fa-upload" aria-label="Upload"></i><br> Upload</a></li>
      <li><a class="subscription-btn text-white"><i class="fas fa-compass" aria-label="Subscription"></i><br> Subscription</a></li>
      <li><a class="collection-btn text-white"><i class="fas fa-bookmark" aria-label="Collection"></i><br> Collection</a></li>
    </ul>
  </footer>

  <script src="<?php echo $jQuery; ?>"></script>
  <script src="https://cdn.danitechno.com/dtubein/js/main.js" defer></script>
  <script src="https://cdn.danitechno.com/dtubein/js/loading.js"></script>

</body>

</html>