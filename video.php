<?php
require 'config.php';
include 'includes/connection.php';
include 'includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (isset($_GET['id'])) {
  $video_id = $_GET['id'];

  $sql_views = "UPDATE videos SET views = views + 1 WHERE id = $video_id";
  $conn->query($sql_views);

  $sql_video = "SELECT videos.*, users.username, users.full_name, users.profile_picture
                  FROM videos
                  INNER JOIN users ON videos.user_id = users.id
                  WHERE videos.id = $video_id";
  $result_video = $conn->query($sql_video);
  $video_row = $result_video->fetch_assoc();

  $sql_likes = "SELECT COUNT(*) AS like_count FROM likes WHERE video_id = $video_id AND action = 'like'";
  $result_likes = $conn->query($sql_likes);
  $row_likes = $result_likes->fetch_assoc();
  $like_count = $row_likes['like_count'];

  $sql_dislikes = "SELECT COUNT(*) AS dislike_count FROM likes WHERE video_id = $video_id AND action = 'dislike'";
  $result_dislikes = $conn->query($sql_dislikes);
  $row_dislikes = $result_dislikes->fetch_assoc();
  $dislike_count = $row_dislikes['dislike_count'];

  $sql_comments = "SELECT COUNT(*) AS comment_count FROM comments WHERE video_id = $video_id";
  $result_comments = $conn->query($sql_comments);
  $row_comments = $result_comments->fetch_assoc();
  $comment_count = $row_comments['comment_count'];

  $profile_user_id = $video_row['user_id'];
  $sql_subscribers = "SELECT COUNT(*) AS subscriber_count FROM subscribers WHERE subscribed_user_id = $profile_user_id";
  $result_subscribers = $conn->query($sql_subscribers);
  $row_subscribers = $result_subscribers->fetch_assoc();
  $subscriber_count = $row_subscribers['subscriber_count'];

  $user_id = $_SESSION['user_id'];
  $sql_check_subscription = "SELECT * FROM subscribers WHERE user_id = $user_id AND subscribed_user_id = $profile_user_id";
  $result_check_subscription = $conn->query($sql_check_subscription);
  $is_subscribed = $result_check_subscription->num_rows > 0;
} else {
  header("Location: /?page=home");
  exit();
}
?>

<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$baseURL = $protocol . '://' . $_SERVER['HTTP_HOST'];
$imageThumbnailURL = $baseURL . '/uploads/thumbnails/' . $video_row['thumbnail'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title><?php echo htmlspecialchars($video_row['title']); ?> - <?php echo $title; ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($video_row['description']); ?>">
  <meta property="og:title" content="<?php echo htmlspecialchars($video_row['title']); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($video_row['description']); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($imageThumbnailURL); ?>">
  <meta property="og:image:alt" content="<?php echo htmlspecialchars($video_row['title']); ?>">

  <link rel="icon" type="image/png" href="https://cdn.danitechno.com/dtubein/img/favicon.png">

  <link rel="stylesheet" href="<?php echo $fontAwesomeBeta; ?>">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/loading.css">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/video.css">

  <meta name="mnd-ver" content="m6fug5ginq4en1n5kencmq" />
  <script async src="https://ss.mndsrv.com/native.js"></script>
  <script async src="https://ss.mndsrv.com/banner.js"></script>
  <script async src="https://ss.mndsrv.com/ctatic/b2c0f0b0-3e06-42e9-a4a1-b30975bc1c49.js"></script>
</head>

<body>
  <div class="loading-spinner">
    <div class="spinner"></div>
  </div>

  <div class="container">
    <div class="video-wrapper">
      <video poster="./uploads/thumbnails/<?php echo $video_row['thumbnail']; ?>" controls autoplay>
        <source src="./uploads/videos/<?php echo $video_row['video']; ?>" type="video/mp4">
      </video>
    </div>
    <div class="video-info">
      <h1><a href="/?page=home" style="color: white;"><i class="fas fa-arrow-left"></i></a> <?php echo htmlspecialchars($video_row['title']); ?></h1>
      <p class="description">
        <?php echo formatViews($video_row['views']); ?> × watched • <?php echo timeDifference(convertToWIB($video_row['uploaded_at'])); ?> • <?php echo makeLinksClickable(htmlspecialchars($video_row['description'])); ?>
      </p>
      <p class="read-more">
        ...Read more
      </p>

      <div class="channel-info">
        <a style="text-decoration: none;" href="profile<?php echo $fileName ? ".php" : ""; ?>?username=<?php echo $video_row['username']; ?>">
          <img src="./uploads/profile-pictures/<?php echo $video_row['profile_picture']; ?>" alt="<?php echo $video_row['full_name']; ?>" alt="name">
          <div>
            <h4><?php echo htmlspecialchars($video_row['full_name']); ?>
              <?php
              if (in_array($video_row['username'], $verifiedAccount)) {
                echo '<i class="fas fa-check-circle"></i>';
              }
              ?>
            </h4>
            <p>
              <?php echo $subscriber_count; ?> subscribers
            </p>
          </a>
        </div>
        <?php if (isset($_SESSION['user_id'])) : ?>
        <?php
        $is_owner = ($video_row['user_id'] == $_SESSION['user_id']);

        if (!$is_owner) {
          $profile_user_id = $video_row['user_id'];

          $user_id = $_SESSION['user_id'];
          $sql_check_subscription = "SELECT * FROM subscribers WHERE user_id = $user_id AND subscribed_user_id = $profile_user_id";
          $result_check_subscription = $conn->query($sql_check_subscription);
          $is_subscribed = ($result_check_subscription->num_rows > 0);
          ?>
          <?php if ($is_subscribed) : ?>
          <form class="subscribe-form" method="POST" action="unsubscribe<?php echo $fileName ? ".php" : ""; ?>">
            <input type="hidden" name="profile_user_id" value="<?php echo $profile_user_id; ?>">
            <input type="submit" value="Unsubscribe" class="subscribe-btn">
          </form>
          <?php else : ?>
          <form class="subscribe-form" method="POST" action="subscribe<?php echo $fileName ? ".php" : ""; ?>">
            <input type="hidden" name="profile_user_id" value="<?php echo $profile_user_id; ?>">
            <input type="submit" value="Subscribe" class="subscribe-btn">
          </form>
          <?php endif; ?>
          <?php
        }
        ?>
        <?php else : ?>
        <form class="subscribe-form" method="GET" action="login<?php echo $fileName ? ".php" : ""; ?>">
          <input type="submit" value="Subscribe" class="subscribe-btn">
        </form>
        <?php endif; ?>
      </div>
      <div class="action action-container">
        <button><span id="like-btn" data-video-id="<?php echo $video_id; ?>"><i class="fas fa-thumbs-up" aria-label="Like"></i> <span id="like-count"><?php echo $like_count; ?></span> Like</span> | <i class="fas fa-thumbs-down" aria-label="Dislike"></i></button>
        <button class="comment-btn" data-video-id="<?php echo $video_id; ?>"><i class="fas fa-comments" aria-label="Comments"></i> <?php echo $comment_count ?> Comments</button>
        <button class="share-btn" data-video-id="<?php echo $video_id; ?>" data-video-url="<?php echo $video_row['video']; ?>"><i class="fas fa-share" aria-label="Share"></i> Share</button>
        <button class="download-btn" data-video-url="<?php echo $video_row['video']; ?>"><i class="fas fa-download" aria-label="Download"></i> Download</button>
        <button class="support-btn" data-video-id="<?php echo $video_id; ?>"><i class="fas fa-heart" aria-label="Support"></i> Support</button>
        <button class="report-btn" data-video-id="<?php echo $video_id; ?>"><i class="fas fa-flag" aria-label="Report"></i> Report</button>
      </div>
    </div>
    
    <br />
    
    <!-- Mondiad area -->
    <div style="display: <?php echo $displayForAds; ?>;" align="center">
      <div data-mndbanid="86ded412-3d7b-478a-9de2-4f1261c2daa8"></div>
      <div style="margin: 15px;"></div>
    </div>
    <!-- End Mondiad area -->
    
    <?php
    $sql_other_videos = "SELECT * FROM videos WHERE id <> $video_id ORDER BY RAND()";
    $result_other_videos = $conn->query($sql_other_videos);

    if ($result_other_videos->num_rows > 0) {
      while ($row = $result_other_videos->fetch_assoc()) {
        $other_video_id = $row['id'];
        $other_thumbnail = $row['thumbnail'];
        $other_title = $row['title'];
        $other_user_id = $row['user_id'];
        $other_views = formatViews($row['views']);
        $other_uploaded_at = timeDifference(convertToWIB($row['uploaded_at']));

        $sql_user = "SELECT * FROM users WHERE id = $other_user_id";
        $result_user = $conn->query($sql_user);
        $row_user = $result_user->fetch_assoc();
        $other_channel = $row_user['full_name'];
        $other_profile_picture = $row_user['profile_picture'];
        ?>
        <div class="video-other">
          <a href="video<?php echo $fileName ? ".php" : ""; ?>?id=<?php echo $other_video_id; ?>" style="text-decoration: none;">
            <div>
              <img src="./uploads/thumbnails/<?php echo $other_thumbnail; ?>" alt="<?php echo htmlspecialchars($other_title); ?>" class="bg-gray-700 w-full h-100 mb-4" style="aspect-ratio: 16/9;">
              <h1><?php echo htmlspecialchars($other_title); ?></h1>
              <div class="video-other-info">
                <img class="profile" src="./uploads/profile-pictures/<?php echo $other_profile_picture; ?>" alt="<?php echo htmlspecialchars($other_channel); ?>">
                <div>
                  <h4><?php echo htmlspecialchars($other_channel); ?></h4>
                  <p>
                    <?php echo $other_views; ?> × watched • <?php echo $other_uploaded_at; ?>
                  </p>
                </div>
              </div>
            </div>
          </a>
        </div>

        <?php
      }
    } else {
      echo '<p style="color: white; margin: 10px;">No videos found.</p>';
    }
    ?>

  </div>
  
  <!-- Mondiad area -->
  <div style="display: <?php echo $displayForAds; ?>;" align="center">
    <br />
    <div data-mndazid="3c0b9cbe-560e-4870-9d8d-de5715e22dc1"></div>
    <br />
  </div>
  <!-- End Mondiad area -->
  
  <script src="<?php echo $jQuery; ?>"></script>
  <script>
    $(document).ready(function() {
      $(document).ready(function() {
        $('#like-btn').on('click', function() {
          var videoId = $(this).data('video-id');
          $.ajax({
            type: 'POST',
            url: 'like.php',
            data: {
              video_id: videoId
            },
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success') {
                $('#like-count').text(response.like_count);
              } else {
                alert('An error occurred: ' + response.message);
                window.location.href = 'login<?php echo $fileName ? ".php" : ""; ?>';
              }
            },
            error: function(xhr, status, error) {
              alert('An error occurred: ' + error);
            }
          });
        });
      });

      $('.comment-btn').on('click', function() {
        var videoId = $(this).data('video-id');
        window.location.href = 'comments<?php echo $fileName ? ".php" : ""; ?>?video-id=' + videoId;
      });

      $('.share-btn').on('click', function() {
        var videoId = $(this).data('video-id');
        var shareUrl = window.location.protocol + '//' + window.location.host + '/video<?php echo $fileName ? ".php" : ""; ?>?id=' + videoId;

        if (navigator.share) {
          navigator.share({
            title: 'Check out this video!',
            text: 'Watch this amazing video',
            url: shareUrl
          }).then(() => {
            alert('Shared successfully');
          }).catch((error) => {
            alert('Sharing canceled');
            console.log('Error: ', error)
          });
        } else {
          var tempInput = $('<input>');
          $('body').append(tempInput);
          tempInput.val(shareUrl).select();
          document.execCommand('copy');
          tempInput.remove();

          alert('Video link copied to clipboard: ' + shareUrl);
        }
      });

      $('.download-btn').click(function() {
        var videoUrl = $(this).data('video-url');
        var downloadUrl = './uploads/videos/' + videoUrl;

        var isConfirmed = confirm("Are you sure you want to download this video?");

        if (isConfirmed) {
          var link = document.createElement('a');
          link.href = downloadUrl;
          link.download = videoUrl;
          link.target = '_blank';
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        } else {
          alert('Download canceled.');
        }
      });

      $('.support-btn').on('click',
        function() {
          var donateUrl = '<?php echo $donateURL; ?>';
          window.location.href = donateUrl;
        });

      $('.report-btn').on('click',
        function() {
          var videoId = $(this).data('video-id');
          showPrompt();

          function showPrompt() {
            var problem = prompt('Problem?');
            if (problem === null) {
              return;
            } else if (problem === '') {
              alert('Please provide a problem description before submitting the report.');
              showPrompt();
            } else {
              var reportUrl = 'https://api.whatsapp.com/send/?phone=<?php echo $customerServiceWhatsAppPhoneNumber; ?>&text=Hello admin, I want to report a video that violates the <?php echo $title; ?> platform, here\'s the *link:* ```http://' + window.location.host + '/video<?php echo $fileName ? ".php" : ""; ?>?id=' + videoId + '```, *problem:* ' + encodeURIComponent(problem) + '&type=phone_number&app_absent=0';
              window.location.href = reportUrl;
            }
          }
        });
    });
  </script>

  <script src="https://cdn.danitechno.com/dtubein/js/main.js" defer></script>
  <script src="https://cdn.danitechno.com/dtubein/js/loading.js"></script>
  <script src="https://cdn.danitechno.com/dtubein/js/video.js"></script>
</body>

</html>