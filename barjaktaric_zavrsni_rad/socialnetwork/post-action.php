<?php
session_start();
require_once('db.php');

if (isset($_GET['action'])) {
  if ($_GET['action'] == "delete") {
    $query = sprintf("DELETE FROM Posts WHERE id='%s';",
                $con->real_escape_string($_GET['id']));
    $con->query($query);
    header('Location: /profile.php');
  }
}

?>
