<?php
require 'config.php';
include 'includes/connection.php';
include 'includes/functions.php';

if (isset($_GET['username'])) {
  $username = $_GET['username'];

  $sql_user = "SELECT * FROM users WHERE username = '$username'";
  $result_user = $conn->query($sql_user);

  if ($result_user->num_rows > 0) {
    $user_row = $result_user->fetch_assoc();

    $user_id = $user_row['id'];
    $sql_videos = "SELECT * FROM videos WHERE user_id = $user_id";
    $result_videos = $conn->query($sql_videos);

    $total_videos = $result_videos->num_rows;
    $total_views = 0;
    while ($row = $result_videos->fetch_assoc()) {
      $total_views += $row['views'];
    }
  } else {
    echo "User not found.";
    exit();
  }
} else {
  header("Location: /?page=home");
  exit();
}

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $sql_user = "SELECT * FROM users WHERE id = $user_id";
  $result_user = $conn->query($sql_user);
  $row_user = $result_user->fetch_assoc();
}
?>

<?php
$profile_user_id = $user_row['id'];
$sql_subscribers = "SELECT COUNT(*) AS subscriber_count FROM subscribers WHERE subscribed_user_id = $profile_user_id";
$result_subscribers = $conn->query($sql_subscribers);
$row_subscribers = $result_subscribers->fetch_assoc();
$subscriber_count = $row_subscribers['subscriber_count'];
?>

<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$baseURL = $protocol . '://' . $_SERVER['HTTP_HOST'];
$imageProfileURL = $baseURL . '/uploads/profile-pictures/' . $user_row['profile_picture'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Profile: <?php echo htmlspecialchars($user_row['full_name']); ?></title>
  <meta name="description" content="@<?php echo htmlspecialchars($user_row['username']); ?> • <?php echo $subscriber_count; ?> subscribers • <?php echo $total_videos; ?> videos • <?php echo htmlspecialchars(formatViews($total_views)); ?> total views • join <?php echo htmlspecialchars(timeDifference(convertToWIB($row_user['created_at']))); ?> • <?php echo htmlspecialchars($user_row['bio'] ? $user_row['bio'] : 'No bio yet'); ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($keywords); ?>">
  <meta property="og:title" content="Profile: <?php echo htmlspecialchars($user_row['full_name'] . ' - ' . $title); ?>">
  <meta property="og:description" content="@<?php echo htmlspecialchars($user_row['username']); ?> • <?php echo $subscriber_count; ?> subscribers • <?php echo $total_videos; ?> videos • <?php echo htmlspecialchars(formatViews($total_views)); ?> total views • join <?php echo htmlspecialchars(timeDifference(convertToWIB($row_user['created_at']))); ?> • <?php echo htmlspecialchars($user_row['bio'] ? $user_row['bio'] : 'No bio yet'); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($imageProfileURL); ?>">
  <meta property="og:image:alt" content="Profile: <?php echo htmlspecialchars($user_row['full_name']); ?>">

  <link rel="icon" type="image/png" href="https://cdn.danitechno.com/dtubein/img/favicon.png">

  <link rel="stylesheet" href="<?php echo $tailwindCSS; ?>">
  <link rel="stylesheet" href="<?php echo $fontAwesomeBeta; ?>">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/main.css">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/loading.css">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/profile.css">
</head>

<body>
  <div class="loading-spinner">
    <div class="spinner"></div>
  </div>

  <div class="header">
    <a href="/?page=home"><i class="fas fa-arrow-left"></i></a>
    <h1 class="text-1xl font-bold text-white ml-2"><?php echo htmlspecialchars($user_row['full_name']); ?><?php if ($user_row['username'] === $ceoAccount) { echo " • CEO of $title"; } ?></h1>
    <i class="ml-auto fas fa-share" onclick="shareProfile()"></i>
  </div>

  <div class="bg-black-900 text-white pt-4 py-0">
    <div class="container mx-auto flex flex-col items-center">
      <img src="./uploads/profile-pictures/<?php echo $user_row['profile_picture']; ?>" alt="Foto Profil" class="rounded-full w-24 h-24 mb-4">
      <h1 class="text-name2 font-bold mb-2">
        <?php echo htmlspecialchars($user_row['full_name']); ?>
        <?php
        if (in_array($user_row['username'], $verifiedAccount)) {
          echo '<i class="fas fa-check-circle"></i>';
        }
        ?>
      </h1>
      <p class="text-gray-400 mb-4" style="font-size: .7rem;">
        <b>@<?php echo htmlspecialchars($user_row['username']); ?></b> • <?php echo $subscriber_count; ?> subscribers • <?php echo $total_videos; ?> videos • <?php echo formatViews($total_views); ?> total views • join <?php echo timeDifference(convertToWIB($row_user['created_at'])); ?>
      </p>
      <p class="text-gray-400 text-sm mb-4">
        <?php echo htmlspecialchars($user_row['bio'] ? $user_row['bio'] : 'No bio yet'); ?>
      </p>

      <?php
      $profile_user_id = $user_row['id'];

      if ($profile_user_id == $_SESSION['user_id']) : ?>
      <div style="display: flex;">
        <a href="edit-profile<?php echo $fileName ? ".php" : ""; ?>"><i class="fas fa-edit"></i> Edit Profile</a>&nbsp;</>&nbsp;
      <a id="logoutButton" href="logout<?php echo $fileName ? ".php" : ""; ?>"><i class="fas fa-sign-out"></i> Log Out</a>
    </div>
    <?php else : ?>
    <?php
    $profile_user_id = $user_row['id'];

    $user_id = $_SESSION['user_id'];
    $sql_check_subscription = "SELECT * FROM subscribers WHERE user_id = $user_id AND subscribed_user_id = $profile_user_id";
    $result_check_subscription = $conn->query($sql_check_subscription);
    $is_subscribed = ($result_check_subscription->num_rows > 0);

    if ($is_subscribed) : ?>
    <form class="subscribe-form" method="POST" action="unsubscribe<?php echo $fileName ? ".php" : ""; ?>">
      <input type="hidden" name="profile_user_id" value="<?php echo $profile_user_id; ?>">
      <input type="submit" value="Unsubscribe" class="bg-white text-black font-bold px-20 py-2 rounded-full hover:bg-gray-300">
    </form>
    <?php else : ?>
    <form class="subscribe-form" method="POST" action="subscribe<?php echo $fileName ? ".php" : ""; ?>">
      <input type="hidden" name="profile_user_id" value="<?php echo $profile_user_id; ?>">
      <input type="submit" value="Subscribe" class="bg-white text-black font-bold px-20 py-2 rounded-full hover:bg-gray-300">
    </form>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<main class="container mx-auto py-0">
  <div class="ml-5 text-2xl font-bold mt-8">
    Videos
  </div>
  <div class="container mx-auto mt-0 grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
    <?php
    $result = $conn->query($sql_videos . " ORDER BY uploaded_at DESC");

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $video_id = $row['id'];
        $thumbnail = $row['thumbnail'];
        $title = $row['title'];
        $user_id = $row['user_id'];
        $views = formatViews($row['views']);
        $uploaded_at = timeDifference(convertToWIB($row['uploaded_at']));
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

<script>
  function shareProfile() {
    if (navigator.share) {
      const shareData = {
        title: 'Check out this profile',
        text: 'Check out ' + '<?php echo $user_row['full_name']; ?>' + "'s profile",
        url: window.location.href
      };

      navigator.share(shareData)
      .then(() => alert('Shared successfully'))
      .catch((error) => alert('Sharing canceled'));
    } else {
      const tempInput = document.createElement('input');
      tempInput.value = window.location.href;
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand('copy');
      document.body.removeChild(tempInput);

      alert('URL copied to clipboard.');
    }
  }

  document.getElementById("logoutButton").addEventListener("click", function(event) {
    event.preventDefault();
    var confirmed = confirm("Are you sure you want to exit?");
    if (confirmed) {
      window.location.href = event.target.getAttribute("href");
    }
  });
</script>

<script src="https://cdn.danitechno.com/dtubein/js/main.js" defer></script>
<script src="https://cdn.danitechno.com/dtubein/js/loading.js"></script>
</body>

</html>