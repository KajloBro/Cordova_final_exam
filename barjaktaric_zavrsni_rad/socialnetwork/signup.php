<?php
session_start();
require_once('db.php');

if (isset($_POST['submit']) &&
    isset($_POST['firstName']) &&
    isset($_POST['lastName']) &&
    isset($_POST['email']) &&
    isset($_POST['username']) &&
    isset($_POST['password'])) {
      if (!empty($_POST['submit']) &&
          !empty($_POST['firstName']) &&
          !empty($_POST['lastName']) &&
          !empty($_POST['email']) &&
          !empty($_POST['username']) &&
          !empty($_POST['password'])) {
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
              $query = sprintf("SELECT * FROM Users WHERE username = '%s' AND password = '%s';",
                          $con->real_escape_string($_POST['username']),
                          $con->real_escape_string($_POST['password']));
              if ($result = $con->query($query)) {
                if ($result->num_rows == 1){
                  while ($row = $result->fetch_assoc()){
                  $_SESSION['id'] = $row['id'];
                  $_SESSION['firstName'] = $row['firstName'];
                  $_SESSION['lastName'] = $row['lastName'];
                  $_SESSION['email'] = $row['email'];
                  $_SESSION['username'] = $row['username'];
                  $_SESSION['password'] = $row['password'];
                  $_SESSION['profilePicture'] = $row['profilePicture'];

                  header('Location: index.php');
                  }
                }
              }
            }
      }
}

//if user is already logged in, redirect to profile
if (isset($_SESSION['id'])) {
  if (!empty($_SESSION['id'])) {
    header('Location: profile.php');
  }
}

 ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Signup | SocialNetwork</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  </head>
  <body class="">
    <div class="main-container">
      <form class="" id="signup-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <input type="text" name="firstName" value="" placeholder="First name" required>
        <input type="text" name="lastName" value="" placeholder="Last name" required>
        <input type="email" name="email" value="" placeholder="Email" required>
        <input type="text" name="username" value="" placeholder="Username" required>
        <input type="password" name="password" value="" placeholder="Password" required>
        <input type="submit" name="submit" value="Signup">
        <a href="index.php">Back</a>

      </form>
    </div> <!-- end main container -->
  </body>
</html>
