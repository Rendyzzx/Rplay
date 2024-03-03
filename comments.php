<?php
require 'config.php';
include 'includes/connection.php';
include 'includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (isset($_GET['video-id'])) {
  $video_id = $_GET['video-id'];

  $sql_views = "UPDATE videos SET views = views + 1 WHERE id = $video_id";
  $conn->query($sql_views);

  $sql_video = "SELECT videos.*, users.username
                  FROM videos
                  INNER JOIN users ON videos.user_id = users.id
                  WHERE videos.id = $video_id";
  $result_video = $conn->query($sql_video);
  $video_row = $result_video->fetch_assoc();

  $sql_comments = "SELECT COUNT(*) AS comment_count FROM comments WHERE video_id = $video_id";
  $result_comments = $conn->query($sql_comments);
  $row_comments = $result_comments->fetch_assoc();
  $comment_count = $row_comments['comment_count'];
} else {
  header("Location: /?page=home");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Comments: <?php echo htmlspecialchars($video_row['title']); ?> - <?php echo htmlspecialchars($title); ?></title>

  <link rel="icon" type="image/png" href="https://cdn.danitechno.com/dtubein/img/favicon.png">

  <link rel="stylesheet" href="<?php echo $fontAwesomeBeta; ?>">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/loading.css">
  <link rel="stylesheet" href="https://cdn.danitechno.com/dtubein/css/comment.css">
</head>

<body>
  <div class="loading-spinner">
    <div class="spinner"></div>
  </div>

  <div class="container">
    <div class="video-wrapper">
      <video poster="./uploads/thumbnails/<?php echo $video_row['thumbnail']; ?>" controls autoplay>
        <source src="uploads/videos/<?php echo $video_row['video']; ?>" type="video/mp4">
      </video>
    </div>

    <div class="comment">
      <div class="comment-nav">
        <h4><i class="comment-icon far fa-comments"></i><?php echo $comment_count; ?> Comments <a class="close" href="video<?php echo $fileName ? ".php" : ""; ?>?id=<?php echo $video_id; ?>"><i class="fas fa-times"></i></a></h4>

        <form class="comment-form" method="POST" action="add-comment<?php echo $fileName ? ".php" : ""; ?>">
          <input type="hidden" name="video_id" value="<?php echo $video_id; ?>">
          <textarea id="comment-input" name="comment" placeholder="Write a comment" required></textarea>
          <button type="submit"><i class="fas fa-paper-plane"></i></button>
        </form>
      </div>

      <div class="comment-content">
        <?php
        $sql_get_comments = "SELECT comments.*, users.username, users.full_name, users.bio, users.profile_picture
                        FROM comments
                        INNER JOIN users ON comments.user_id = users.id
                        WHERE comments.video_id = $video_id
                        ORDER BY comments.created_at DESC";
        $result_get_comments = $conn->query($sql_get_comments);

        if ($result_get_comments->num_rows > 0) {
          while ($comment_row = $result_get_comments->fetch_assoc()) {
            ?>
            <div class="comment-wrapper">
              <a href="profile<?php echo $fileName ? ".php" : ""; ?>?username=<?php echo $comment_row['username']; ?>" class="comment-header">
                <img src="./uploads/profile-pictures/<?php echo $comment_row['profile_picture']; ?>" alt="<?php echo htmlspecialchars($comment_row['full_name']); ?>">
                <h3>@<?php echo $comment_row['username']; ?>
                  <?php if (in_array($comment_row['username'], $verifiedAccount)) {
                    ?>
                    <i class="fas fa-check-circle"></i>
                    <?php
                  } ?>
                </h3>
                <p>
                  <?php if ($comment_row['username'] === $ceoAccount) {
                    ?>
                    • CEO of <?php echo $title; ?>
                    <?php
                  } ?>
                  • <?php echo timeDifference(convertToWIB($comment_row['created_at'])); ?>
                </p>
              </a>
              <div class="comment-body">
                <?php echo htmlspecialchars($comment_row['comment']); ?>
              </div>
              <div class="comment-footer">
                <button><i class="fas fa-thumbs-up"></i> Like</button>
                <button><i class="fas fa-thumbs-down"></i> Dislike</button>
                <button><i class="fas fa-reply"></i> Reply</button>
              </div>
            </div>
            <?php
          }
        } else {
          echo '<div class="comment-wrapper">
          <div class="comment-body">
            No comments yet!
          </div>
        </div>';
        }
        ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.danitechno.com/dtubein/js/main.js" defer></script>
  <script src="https://cdn.danitechno.com/dtubein/js/loading.js"></script>
  <script src="https://cdn.danitechno.com/dtubein/js/comment.js"></script>
</body>

</html>