<?php
session_start();
session_regenerate_id(true);
require_once('db.php');


if (isset($_POST['username']) && isset($_POST['password'])) {
  if (!empty($_POST['username']) && !empty($_POST['password'])){
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

        header('Location: profile.php');
        }
      }

      else {
        ?>
        <script type="text/javascript">
          alert("Incorrect username or password, please try again");
        </script>
        <?php
      }
    }
  }
}

if (isset($_SESSION['id']) && isset($_SESSION['username'])){
  header('Location: profile.php');
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Login | SocialNetwork</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  </head>
  <body class="bg-img">
    <div class="main-container">
      <!--<img src="img/inntouch-logo.png" class="logo-img" alt="">-->
      <form class="" id="login-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <input type="text" name="username" value="" placeholder="Username" required>
        <input type="password" name="password" value="" placeholder="Password" required>
        <input type="submit" name="submit" value="Sign in">
        <span>Don't have a account? <a href="signup.php">Sign up.</a></span>
      </form>
    </div> <!-- end main container -->
  </body>
</html>
