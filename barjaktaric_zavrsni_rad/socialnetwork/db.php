<?php

define("DB_DATA", array('username' => 'bone7ord',
                        'password' => 'dIIq7hH43',
                        'host' => 'localhost',
                        'database' => 'SocialNetwork'
                      ));

$con = new mysqli(DB_DATA['host'],
                  DB_DATA['username'],
                  DB_DATA['password'],
                  DB_DATA['database']
                );

if ($con->connect_errno){ //check if connection is establiches
  echo "Failed to connect to MySQL: " . $con->connect_error;
  die();
}

?>
