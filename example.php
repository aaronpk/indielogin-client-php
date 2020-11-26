<?php
require('vendor/autoload.php');

if(count($_GET) == 0 && count($_POST) == 0) {
  ?>
  <form action="example.php" method="post">
    <input type="text" name="url">
    <input type="submit" value="Log In">
  </form>
  <?php
  die();
}

session_start();

IndieLogin\Client::$server = 'https://indielogin.com';
IndieLogin\Client::$clientID = 'http://localhost:9999/example.php';
IndieLogin\Client::$redirectURL = 'http://localhost:9999/example.php';


if(isset($_GET['error'])) {
  echo "<p>Error: ".htmlspecialchars($_GET['error'])."</p>";
  echo "<p>".htmlspecialchars($_GET['error_description'])."</p>";
  die();
}

if(!isset($_GET['code'])) {
  $url = IndieLogin\Client::normalizeMeURL($_POST['url']);

  list($authorizationURL, $error) = IndieLogin\Client::begin($_POST['url']);

  echo '<a href="'.$authorizationURL.'">continue to authorize</a>';
  die();
}

list($user, $error) = IndieAuth\Client::complete($_GET);

echo '<pre>';
print_r($user);
if($error) {
  echo "Error:\n";
  print_r($error);
}
echo '</pre>';

