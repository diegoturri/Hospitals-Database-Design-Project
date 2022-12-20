<?php
session_start();

$username = "";

if (isset($_SESSION["username"])) {
  $username = $_SESSION["username"];
  echo "User " .$username. " your credential are valid";

}else{ //if there's not a result
  echo <<<EOS
    you can't access this site <br>
    If you want you can <a href="login.html"> retry </a> the access
  EOS;
}

?>
