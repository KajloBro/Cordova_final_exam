<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['id'])) header('Location: index.php');

if (isset($_POST['submit'])) {
  $uploaddir = "./uploads/";
  $uploadfile = $uploaddir . "profile-" . $_SESSION['id'] . ".jpg";

  if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $uploadfile)) {
    $query = sprintf("UPDATE Users SET profilePicture = '%s' WHERE id = '%s';",
                $con->real_escape_string($uploadfile),
                $con->real_escape_string($_SESSION['id']));
    $con->query($query);

    // Update session for new profile photo to be displayed while logged in
    $_SESSION['profilePicture'] = $uploadfile;
    header('Location: profile.php');
  } else {
    echo "File not uplaoded";
  }
}


?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Profile settings</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  </head>
  <body>
    <div class="main-container">
      <form id="uploadPhoto-form" class="" action="profile-settings.php" method="post" enctype="multipart/form-data">
        <input type="file" name="fileToUpload" value="">
        <br>
        <input type="submit" name="submit" value="Submit">
        <br>
        <a href="profile.php">Back</a>
      </form>
    </div>
  </body>
</html>
