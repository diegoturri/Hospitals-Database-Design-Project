<?php
//shield
session_start();
include '../include/functions.php';

$username = "";

if (isset($_SESSION["username"])) {
  $username = $_SESSION["username"];
  $table = $_GET["table"];
  $conn = pg_connect("host=localhost port=5432 dbname=hospitals user=postgres password=diegoturri.com");



//CHECK CONNECTION
  if (!$conn) {
    echo 'Connessione al database fallita.';
    exit();
  }else{

    $data = '';
    $i = 0;
    foreach ($_GET as $key => $value) {
      if ($key=='table') {
        continue;
      }
      if ($i == Count($_GET)-2){
        $data = $data.$key."=".isNull(isString($conn, $key, $value));
      }else{
        $data = $data.$key."=".isNull(isString($conn, $key, $value))." , ";
      }
      $i++;
    }


    $query = "UPDATE ".$_GET["table"]." SET ".$data." WHERE ".$_SESSION["oldData"];
    $result = pg_query($conn, $query);

//CHECK QUERY
    if (!$result) {
      echo <<<EOS
      <script type='text/javascript'>
      if (alert("There has been an error! :( query: $query")) {
        window.location="../pages/selection.php?table=$table";
      } else {
        window.location="../pages/selection.php?table=$table";
      }
      </script>
      EOS;
    }else{

//EVERYTHING IS OK:
      echo <<<EOS
      <script type='text/javascript'>
      if (alert("The values has been updated :)")) {
        window.location="../pages/selection.php?table=$table";
      } else {
        window.location="../pages/selection.php?table=$table";
      }
      </script>
      EOS;
    }
  }








}else{ //end of shield
  echo <<<EOS
    $username you can't access this site <br>
    If you want you can <a href="../index.html"> retry </a> the access
  EOS;
}

?>
