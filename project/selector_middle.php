<?php
//shield
session_start();

$username = "";

if (isset($_SESSION["username"])) {
  $username = $_SESSION["username"];
  $table = $_GET["table"];

  if (isset($_GET["oldTable"])) { //after the JUMP button it appears a "Go Back" button
    $oldTable = $_GET["oldTable"];
    header("Location: selection.php?table=$oldTable");
  }

if (isset($_POST['tabella'])) {
  $table = $_POST["tabella"];
  header( "Location: selection.php?table=$table" );
}

if(isset($_GET["path"])){
  switch ($_GET["path"]) {
    case 'newGuest':
      header( "Location: insertion.php?table=persona" );
      break;

    case 'bookAnExam':
      header( "Location: insertion.php?table=prenotazione" );
      break;

    case 'prescribeAnExam':
      header( "Location: insertion.php?table=prescrizione" );
      break;

    default:
      header( "Location: main.php" );
      break;
  }
}




}else{ //end of shield
  echo <<<EOS
    $username you can't access this site <br>
    If you want you can <a href="login/login.html"> retry </a> the access
  EOS;
}

?>
