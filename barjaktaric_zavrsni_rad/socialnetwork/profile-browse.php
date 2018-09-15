<?php
session_start();
require_once('db.php');

// Check if this is my profile, when yes, redirect to profile.php
if ($_GET['id'] == $_SESSION['id']) header('Location: profile.php');

// Load the profile details
function profile_browse_load() {
  global $con;
  if (isset($_GET['id'])) {
    if (!empty($_GET['id'])) {
      $query = sprintf("SELECT firstName, lastName, profilePicture
                        FROM Users WHERE id = '%s';",
                        $con->real_escape_string($_GET['id']));
      if ($result = $con->query($query)) {
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            if ($row['profilePicture'] == NULL){
              echo '<img src="img/profile.jpg" class="profilePicture">';
            } else {
              echo '<img src="' . $row['profilePicture'] . '" class="profilePicture">';
            }
            ?>
              <h2><?=$row['firstName']?> <?=$row['lastName']?></h2>
            <?php
          }
        }
      }
    }
  }
}

// Load the profile posts
function profile_browse_load_posts() {
  global $con;
  if (isset($_GET['id'])) {
    if (!empty($_GET['id'])) {
      $query = sprintf("SELECT title, postText, dateCreated
                        FROM Posts WHERE FK_Users_id = '%s';",
                        $con->real_escape_string($_GET['id']));
      if ($result = $con->query($query)) {
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            ?>
            <div class="post">
              <h1>
                <?=$row['title'];?>
              </h1>
              <i>Posted: <?=$row['dateCreated']?></i>
              <p>
                <?=$row['postText'];?>
              </p>
            </div>
            <?php
          }
        }
      }
    }
  }
}

function load_friends_btn() {
  global $con;
  $query = sprintf("SELECT * FROM Friendships
                    WHERE FK_Users_user1 = '%s' AND FK_Users_user2 = '%s'",
                    $con->real_escape_string($_SESSION['id']),
                    $con->real_escape_string($_GET['id']));
  if ($result = $con->query($query)) {
    if ($result->num_rows > 0) {
      ?>
      <a href="friends.php?id=<?=$_GET['id']?>&action=unfollow" class="remove-friend-btn">
        <h2>Unfollow</h2>
      </a>
      <?php
    }
    else {
      ?>
      <a href="friends.php?id=<?=$_GET['id']?>&action=follow" class="add-friend-btn">
        <h2>Follow</h2>
      </a>
      <?php
    }
  }
}
 ?>



<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Profile browse</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  </head>
  <body>
    <div class="main-container-outer">
      <div class="main-container-inner">
        <div class="profile-info">
          <?php
            profile_browse_load();
            load_friends_btn();
          ?>
          <form class="form-profile-search" action="profile-search.php" method="get">
            <input type="search" name="searchProfile" value="" placeholder="Search profiles">
          </form>
          <a href="profile.php" class="my-profile-btn" >My profile</a>
        </div>
        <div class="profile-posts">
          <div class="posts-wrapper">
            <?php profile_browse_load_posts(); ?>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
