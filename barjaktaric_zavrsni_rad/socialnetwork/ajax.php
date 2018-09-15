<?php

require_once('db.php');

if (is_ajax()) {
  if (isset($_REQUEST['action']) && !empty($_REQUEST['action'])) {
    switch ($_REQUEST['action']) {
      case "signin": sign_in(); break;
      case "signup": sign_up(); break;
      case "loadPosts": load_posts(); break;
      case "new-post": new_post(); break;
      case "delete-post": delete_post(); break;
      case "reload-profile-photo": reload_profile_photo(); break;
      case "search-profile": search_profile(); break;
      case "browse-profile": browse_profile(); break;
      case "load-follow-btn": load_follow_btn(); break;
      case "load-browse-profile-posts": load_browse_profile_posts(); break;
      case "unfollow": unfollow(); break;
      case "follow": follow(); break;
      case "get-followings-num": get_followings_num(); break;
      case "get-followers-num": get_followers_num(); break;
      case "list-friends": load_friends_list(); break;
      case "upload-profile-photo": upload_profile_photo(); break;
    }
  }
}


//Function to check if the request is an AJAX request
function is_ajax() {
  if ($_REQUEST['ajax'] == 1) {
    return true;
  }
}


function sign_in() {
  if (isset($_POST['username']) && isset($_POST['password'])) {
    global $con;
    $query = sprintf("SELECT * FROM Users WHERE username = '%s' AND BINARY password = '%s';",
                      $con->real_escape_string($_POST['username']),
                      $con->real_escape_string($_POST['password']));

    if ($result = $con->query($query)) {
      if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $row['status'] = "success";
        echo json_encode($row);
      }
      else {
          $row['status'] = "fail";
          echo json_encode($row);
      }
    }
  }
}


function sign_up() {
  if (
      isset($_POST['firstName']) && !empty($_POST['firstName']) &&
      isset($_POST['lastName']) && !empty($_POST['lastName']) &&
      isset($_POST['email']) && !empty($_POST['email']) &&
      isset($_POST['username']) && !empty($_POST['username']) &&
      isset($_POST['password']) && !empty($_POST['password']))
      {
        global $con;
        $query = sprintf("INSERT INTO Users(firstName,
                                            lastName,
                                            email,
                                            username,
                                            password)
                          VALUES('%s','%s','%s','%s','%s');",
                          $con->real_escape_string($_POST['firstName']),
                          $con->real_escape_string($_POST['lastName']),
                          $con->real_escape_string($_POST['email']),
                          $con->real_escape_string($_POST['username']),
                          $con->real_escape_string($_POST['password']));

        if ($result = $con->query($query)) {
          sign_in();
        }
      }
}


function load_posts() {
  global $con;
  $query = sprintf("SELECT * FROM Posts
                    WHERE FK_Users_id = '%s' ORDER BY id DESC",
                    $con->real_escape_string($_POST['id']));

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
          <a href="#" class="delete-post-button" data-post-id="<?=$row['id'];?>">
            <img src="img/close-button.png" class="delete-post-btn-img" alt="">
          </a>
        </div>
        <?php
      }
    }
  }
}


function new_post() {
  global $con;
  $query = sprintf("INSERT INTO Posts(title, postText, dateCreated, FK_Users_id)
                    VALUES ('%s', '%s', '%s', '%s');",
                    $con->real_escape_string($_POST['title']),
                    $con->real_escape_string(nl2br($_POST['postText'])),
                    $con->real_escape_string(date("H:i d.m.Y.")),
                    $con->real_escape_string($_POST['id']));
  $con->query($query);
}


function delete_post() {
  global $con;
  $query = sprintf("DELETE FROM Posts WHERE id='%s';",
                    $con->real_escape_string($_POST['id']));
  $con->query($query);
}


function reload_profile_photo() {
  global $con;
  $query = sprintf("SELECT profilePicture FROM Users WHERE id = '%s';",
                    $con->real_escape_string($_POST['id']));
  if ($result = $con->query($query)) {
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $row['profilePicture'] = $row['profilePicture'] . "?=" . filemtime($row['profilePicture']);
      echo $row['profilePicture'];
    }
  }
}


function search_profile() {
  global $con;
  $query = sprintf("SELECT * FROM Users WHERE firstName LIKE '%s%%' OR lastName LIKE '%s%%';",
                    $con->real_escape_string($_POST['searchProfile']),
                    $con->real_escape_string($_POST['searchProfile']));

  if ($result = $con->query($query)) {
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        ?>
        <div class="search-profile-wrapper">
          <a href="#" data-profile-id="<?=$row['id']?>">
            <div class="search-profile-picture-wrapper">
              <?php
              if ($row['profilePicture'] == NULL) {
                ?>
                <div class="search-profile-photo" style="background-image: url('img/profile.jpg')">
                </div>
                <?php
              }
              else {
                if (strpos($row[profilePicture], 'robohash')) {
                  ?>
                  <div class="search-profile-photo" style="background-image: url('<?php echo $row['profilePicture'];?>')"></div>
                  <?php
                }
                else {
                  ?>
                  <div class="search-profile-photo" style="background-image: url('<?php echo $_POST['domainBase'] . $row['profilePicture'] . "?=" . filemtime($row['profilePicture']);?>')"></div>
                <?php
                }
              }
              ?>
              <h2 class="search-profile-name"><?=$row['firstName']?> <?=$row['lastName']?></h2>
            </div>
          </a>
        </div>
        <?php
      }
    }
    else { ?>
      <h2 class="search-message">No profiles found.</h2>
      <?php
    }
  }
}


function browse_profile() {
  global $con;
  $query = sprintf("SELECT firstName, lastName, profilePicture
                    FROM Users WHERE id = '%s';",
                    $con->real_escape_string($_POST['id']));

  if ($result = $con->query($query)) {
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        if ($row['profilePicture'] == NULL) {
          ?>
          <div class="profile-photo-wrapper" style="background-image: url('img/profile.jpg')">
          </div>
          <?php
        }
        else {
          if (strpos($row[profilePicture], 'robohash')) {
            ?>
            <div class="profile-photo-wrapper" style="background-image: url('<?php echo $row['profilePicture'];?>')"></div>
            <?php
          }
          else {
            ?>
            <div class="profile-photo-wrapper" style="background-image: url('<?php echo $_POST['domainBase'] . $row['profilePicture'] . "?=" . filemtime($row['profilePicture']);?>')"></div>
            <?php
          }
        }
        ?>
          <h2 class="profile-browse-name"><?=$row['firstName']?> <?=$row['lastName']?></h2>
        <?php
      }
    }
  }
}


function load_follow_btn() {
    global $con;
    $query = sprintf("SELECT * FROM Friendships
                      WHERE FK_Users_user1 = '%s' AND FK_Users_user2 = '%s'",
                      $con->real_escape_string($_POST['id']),
                      $con->real_escape_string($_POST['browse-profile-id']));

    if ($result = $con->query($query)) {
      if ($result->num_rows > 0) {
        ?>
        <a href="#" class="remove-friend-btn" data-follow-action="unfollow">
          <h2>Unfollow</h2>
        </a>
        <?php
      }
      else {
        ?>
        <a href="#" class="add-friend-btn" data-follow-action="follow">
          <h2>Follow</h2>
        </a>
        <?php
      }
    }
}


function load_browse_profile_posts() {
  global $con;
  $query = sprintf("SELECT title, postText, dateCreated
                    FROM Posts WHERE FK_Users_id = '%s';",
                    $con->real_escape_string($_POST['id']));

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
    else {
      ?>
      <div class="post">
        <h1>
          No posts yet.
        </h1>
      </div>
      <?php
    }
  }
}


function unfollow() {
  global $con;
  $query = sprintf("DELETE FROM Friendships
                    WHERE FK_Users_user1 = '%s' AND FK_Users_user2 = '%s'",
                    $con->real_escape_string($_POST['id']),
                    $con->real_escape_string($_POST['browse-profile-id']));
  $con->query($query);
}


function follow() {
  global $con;
  $query = sprintf("INSERT INTO Friendships(FK_Users_user1, FK_Users_user2)
                    VALUES ('%s', '%s')",
                    $con->real_escape_string($_POST['id']),
                    $con->real_escape_string($_POST['browse-profile-id']));
   $con->query($query);
}


function get_followings_num() {
  global $con;
  $query = sprintf("SELECT Friendships.id
                    FROM Friendships
                    WHERE Friendships.FK_Users_user1 = '%s';",
                    $con->real_escape_string($_POST['id']));

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
                    $con->real_escape_string($_POST['id']));

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


function load_friends_list() {
  global $con;

  if ($_POST['type'] == "list-followings") {
    $query = sprintf("SELECT Friendships.FK_Users_user2, Users.firstName, Users.lastName, Users.profilePicture
                      FROM Friendships
                      INNER JOIN Users ON Friendships.FK_Users_user2 = Users.id
                      WHERE Friendships.FK_Users_user1 = '%s';",
                      $con->real_escape_string($_POST['id']));
  }
  else if ($_POST['type'] == "list-followers") {
    $query = sprintf("SELECT Friendships.FK_Users_user1, Users.firstName, Users.lastName, Users.profilePicture
                      FROM Friendships
                      INNER JOIN Users ON Friendships.FK_Users_user1 = Users.id
                      WHERE Friendships.FK_Users_user2 = '%s';",
                      $con->real_escape_string($_POST['id']));
  }

  if ($result = $con->query($query)) {
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        ?>
        <div class="friendships-profile-wrapper">
          <a href="#" data-profile-id="<?php
            if ($_POST['type'] == "list-followings") echo $row['FK_Users_user2'];
            else if ($_POST['type'] == "list-followers") echo $row['FK_Users_user1'];?>">

            <div class="friendships-profile-picture-wrapper">
            <?php
            if ($row['profilePicture'] == NULL) {
              ?>
              <div class="friends-list-photo" style="background-image: url('img/profile.jpg')"></div>
              <?php
            }
            else {
              if (strpos($row[profilePicture], 'robohash')) {
                ?>
                <div class="friends-list-photo" style="background-image: url('<?php echo $row['profilePicture'];?>')"></div>
                <?php
              }
              else {
                ?>
                <div class="friends-list-photo" style="background-image: url('<?php echo $_POST['domainBase'] . $row['profilePicture'] . "?=" . filemtime($row['profilePicture']);?>')"></div>
                <?php
              }
            }
            ?>
            </div>
            <h2 class="friends-list-name"><?=$row['firstName']?> <?=$row['lastName']?></h2>
          </a>
        </div>
        <?php
      }
    }
  }
}


function upload_profile_photo() {
  if (isset($_FILES['file'])) {
    global $con;
    $uploadDir = "./uploads/";
    $uploadFilename = $uploadDir . "profile-" . $_REQUEST['id'] . ".jpg";

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilename)) {
      $query = sprintf("UPDATE Users SET profilePicture = '%s' WHERE id = '%s';",
                  $con->real_escape_string($uploadFilename),
                  $con->real_escape_string($_REQUEST['id']));
      $con->query($query);
    }
  }
}


?>
