<?php

session_start();
require_once('db.php');
require_once('friends-function.php');


function load_friends_list($action) {
  global $con;
  if ($action == "list-followings") {
    $query = sprintf("SELECT Friendships.FK_Users_user2, Users.firstName, Users.lastName, Users.profilePicture
                      FROM Friendships
                      INNER JOIN Users ON Friendships.FK_Users_user2 = Users.id
                      WHERE Friendships.FK_Users_user1 = '%s';",
                      $con->real_escape_string($_SESSION['id']));
  }
  else if ($action == "list-followers") {
    $query = sprintf("SELECT Friendships.FK_Users_user1, Users.firstName, Users.lastName, Users.profilePicture
                      FROM Friendships
                      INNER JOIN Users ON Friendships.FK_Users_user1 = Users.id
                      WHERE Friendships.FK_Users_user2 = '%s';",
                      $con->real_escape_string($_SESSION['id']));
  }

  if ($result = $con->query($query)) {
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        ?>
        <div class="friendships-profile-wrapper">
          <a href="profile-browse.php?id=
            <?php
            if ($_GET['action'] == "list-followings") echo $row['FK_Users_user2'];
                  else echo $row['FK_Users_user1'];
            ?>
          ">
            <div class="friendships-profile-picture-wrapper">
            <?php
            if ($row['profilePicture'] == NULL) {
              ?>
              <img src="img/profile.jpg" class="friendships-profile-img" alt="">
              <?php
            } else {
              ?>
              <img src="<?=$row['profilePicture']?><?php echo "?=" . filemtime($row['profilePicture']);?>" class="friendships-profile-img" alt="">
              <?php
            }
            ?>
            </div>
            <h2><?=$row['firstName']?> <?=$row['lastName']?></h2>
          </a>
        </div>
        <?php
      }
    }
  }
}



?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Friends</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  </head>
  <body>
    <div class="main-container-outer">
      <?php
        if ($_GET['action'] == "list-followings") get_followings_num();
        else get_followers_num();
      ?>
      <div class="friendships-main-container" style="margin-top:0;">
        <?php load_friends_list($_GET['action']); ?>
      </div>
    </div>
  </body>
</html>
