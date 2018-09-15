<?php
session_start();
require_once('db.php');
require_once('friends-function.php');

// If session id is not set (user not logged in), redirect to entry point (index.php)
if (!isset($_SESSION['id'])) header('Location: index.php');


/* Perform action
 * LOGOUT
 * NEW post
*/
if (isset($_GET['action'])) {
  if ($_GET['action'] == "logout") {
    session_destroy();
    $_SESSION = array();
    header('Location: index.php');
  }
  else if ($_GET['action'] == "post") {
    // Check if title is entered
    if (empty($_GET['title'])) $_GET['title'] = "No title";
    // Check if post text is entered
    if (empty($_GET['postText'])) $_GET['postText'] = "No content";

    $query = sprintf("INSERT INTO Posts(title, postText, dateCreated, FK_Users_id)
                      VALUES ('%s', '%s', '%s', '%s');",
                      $con->real_escape_string($_GET['title']),
                      $con->real_escape_string(nl2br($_GET['postText'])),
                      $con->real_escape_string(date("H:i d.m.Y.")),
                      $con->real_escape_string($_SESSION['id']));
    $con->query($query);
  }
}

// Read posts from DB
function read_posts() {
  global $con;
  $query = sprintf("SELECT * FROM Posts WHERE FK_Users_id = '%s' ORDER BY id DESC",
              $con->real_escape_string($_SESSION['id']));
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
          <a href="post-action.php/?id=<?=$row['id']?>&action=delete" class="delete-post-button hide">
            <img src="img/close-button.png" class="delete-post-btn-img" alt="">
          </a>
        </div>
        <?php
      }
    }
  }
}

// Load profile picture
function load_profile_picture() {
  if ($_SESSION['profilePicture'] == NULL) {
    ?>
      <img src="img/profile.jpg" class="profilePicture">
    <?php
  }

  // Checks if profilePicture contains http
  /*else if (stripos($_SESSION['profilePicture'], "http") === TRUE) {
    ?>
    <img src="<?=$_SESSION['profilePicture']?>" class="profilePicture">
    <?php
  }*/
  else {
    ?>
    <img src="<?=$_SESSION['profilePicture']?><?php echo "?=" . filemtime($_SESSION['profilePicture']);?>" class="profilePicture">
    <?php
  }
}
 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>Profile | SocialNetwork</title>
     <link rel="stylesheet" href="/css/style.css">
     <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
   </head>
   <body>
     <div class="main-container-outer">
       <div class="main-container-inner">
         <div class="profile-info">
           <?php load_profile_picture();?>
           <br>
           <h2>Hello,<br><?=$_SESSION['firstName']?> <?=$_SESSION['lastName']?></h2>
           <a href="friends.php?action=list-followings"><?php get_followings_num(); ?></a>
           <a href="friends.php?action=list-followers"><?php get_followers_num(); ?></a>
           <form class="form-profile-search" action="profile-search.php" method="get">
             <input type="search" name="searchProfile" value="" placeholder="Search profiles">
           </form>
           <a href="profile-settings.php" class="profile-settings-btn">
             <h2>Upload profile photo</h2>
           </a>
           <a href="<?=$_SERVER['PHP_SELF'].'?action=logout'?>" class="logout-btn">
             <h2>Logout</h2>
           </a>
         </div>
         <div class="profile-posts">
           <div class="posts-wrapper">
             <div class="form-wrapper post">
               <form class="" id="post-form" action="<?=$_SERVER['PHP_SELF']?>" method="get">
                 <input type="text" name="title" placeholder="Title">
                 <textarea name="postText" rows="8" cols="80" placeholder="What's on your mind?"></textarea>
                 <input type="text" name="action" value="post" hidden>
                 <div class="form-buttons-wrapper">
                   <input type="submit" name="submit" value="Submit">
                   <input type="reset" name="reset" value="Clear">
                 </div>
               </form>
             </div>

             <?php read_posts(); ?>
           </div>
         </div>
      </div>
     </div>
     <script src="bower_components/jquery/dist/jquery.min.js"></script>
     <script src="js/master.js" charset="utf-8"></script>
   </body>
 </html>
