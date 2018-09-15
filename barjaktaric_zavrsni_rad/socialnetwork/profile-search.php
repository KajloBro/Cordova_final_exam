<?php
session_start();
require_once('db.php');

function search_profiles() {
  if (isset($_GET['searchProfile'])) {
    if (!empty($_GET['searchProfile'])) {
      global $con;
      $query = sprintf("SELECT * FROM Users WHERE firstName LIKE '%s%%' OR lastName LIKE '%s%%';",
                        $con->real_escape_string($_GET['searchProfile']),
                        $con->real_escape_string($_GET['searchProfile']));

      if ($result = $con->query($query)) {
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            ?>
            <div class="search-profile-wrapper">
              <a href="profile-browse.php?id=<?=$row['id']?>">
                <div class="search-profile-picture-wrapper">
                  <?php
                  if ($row['profilePicture'] == NULL) {
                    ?>
                    <img src="img/profile.jpg" class="search-profile-img" alt="">
                    <?php
                  } else {
                    ?>
                    <img src="<?=$row['profilePicture']?><?php echo "?=" . filemtime($row['profilePicture']);?>" class="search-profile-img" alt="">
                    <?php
                  }
                  ?>
                </div>
                <h2><?=$row['firstName']?> <?=$row['lastName']?></h2>
              </a>
            </div>
            <?php
          }
        } else {
          echo "No profiles found in loop";
        }
      }

    } else {
      echo "<h3>No profiles found</h3>";
    }
  }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Search profile</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  </head>
  <body>
    <div class="main-container-outer">
      <div class="main-container-inner fixed">
        <form class="form-profile-search shadow-box" action="profile-search.php" method="get">
          <input type="search" name="searchProfile" value="" placeholder="Search profiles">
        </form>
      </div>
      <div class="search-profile-main-container">
        <?php search_profiles(); ?>
      </div>
    </div>
  </body>
</html>
