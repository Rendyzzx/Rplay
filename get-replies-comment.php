<?php
require 'config.php';
include 'includes/connection.php';

if (isset($_POST['comment_id'])) {
  $comment_id = $_POST['comment_id'];

  $sql_get_replies = "SELECT comment_replies.*, users.username, users.profile_picture
                      FROM comment_replies
                      INNER JOIN users ON replies.user_id = users.id
                      WHERE comment_replies.comment_id = $comment_id
                      ORDER BY comment_replies.created_at DESC";
  $result_get_replies = $conn->query($sql_get_replies);

  $replies = array();
  while ($reply_row = $result_get_replies->fetch_assoc()) {
    $replies[] = $reply_row;
  }

  // Return the replies data as JSON
  echo json_encode($replies);
}
?>