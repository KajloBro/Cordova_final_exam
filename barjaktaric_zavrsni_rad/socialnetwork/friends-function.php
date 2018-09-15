<?php

function get_followings_num() {
  global $con;
  $query = sprintf("SELECT Friendships.id
                    FROM Friendships
                    WHERE Friendships.FK_Users_user1 = '%s';",
                    $con->real_escape_string($_SESSION['id']));
  if ($result = $con->query($query)) {
    if ($result->num_rows > 0) {
      ?>
      <h1><span class="number"><?=$result->num_rows?></span> Followings</h1>
      <?php
    }
    else {
      ?>
        <h1><span class="number">0</span> Followings</h1>
      <?php
    }
  }
}

function get_followers_num() {
  global $con;
  $query = sprintf("SELECT Friendships.id
                    FROM Friendships
                    WHERE Friendships.FK_Users_user2 = '%s';",
                    $con->real_escape_string($_SESSION['id']));
  if ($result = $con->query($query)) {
    if ($result->num_rows > 0) {
      ?>
      <h1><span class="number"><?=$result->num_rows?></span> Followers</h1>
      <?php
    }
    else {
      ?>
        <h1><span class="number">0</span> Followers</h1>
      <?php
    }
  }
}

// action for adding and removing followings
if (isset($_GET['id']) AND isset($_GET['action'])) {
  if (!empty($_GET['action'])) {
    if ($_GET['action'] == "follow") {
      $query = sprintf("INSERT INTO Friendships(FK_Users_user1, FK_Users_user2)
                        VALUES ('%s', '%s')",
                        $con->real_escape_string($_SESSION['id']),
                        $con->real_escape_string($_GET['id']));
      $con->query($query);
    }
    else if ($_GET['action'] == "unfollow") {
      $query = sprintf("DELETE FROM Friendships
                        WHERE FK_Users_user1 = '%s' AND FK_Users_user2 = '%s'",
                        $con->real_escape_string($_SESSION['id']),
                        $con->real_escape_string($_GET['id']));
      $con->query($query);
    }
    // When action completed redirect back to profile-browse
    $location = "Location: profile-browse.php?id=" . $_GET['id'];
    header($location);
  }
}

?>
