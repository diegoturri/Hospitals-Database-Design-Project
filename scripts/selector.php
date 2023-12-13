<?php
//shield
session_start();

$username = "";

if (isset($_SESSION["username"])) {
  $username = $_SESSION["username"];
  $table = $_GET["table"];

  if (isset($_GET["oldTable"])) { //after the JUMP button it appears a "Go Back" button
    $oldTable = $_GET["oldTable"];
    header("Location: ../pages/selection.php?table=$oldTable");
  }

if (isset($_POST['table'])) {
  $table = $_POST["table"];
  header( "Location: ../pages/selection.php?table=$table" );
}

if(isset($_GET["path"])){
  switch ($_GET["path"]) {
    case 'newPerson':
      header( "Location: ../pages/insertion.php?table=person" );
      break;

    case 'bookAnExam':
      header( "Location: ../pages/insertion.php?table=reservation" );
      break;

    case 'prescribeAnExam':
      header( "Location: ../pages/insertion.php?table=prescription" );
      break;

    default:
      header( "Location: ../pages/main.php" );
      break;
  }
}




}else{ //end of shield
  echo <<<EOS
    $username you can't access this site <br>
    If you want you can <a href="../index.html"> retry </a> the access
  EOS;
}

?>
