<?php
//shield
session_start();
include 'include/functions.php';

$username = "";

if (isset($_SESSION["username"])) {
  $username = $_SESSION["username"];
  $table = $_GET["table"];
  $conn = pg_connect("host=localhost port=5432 dbname=hospitals user=postgres password=unimi");



//CHECK CONNECTION
  if (!$conn) {
    echo 'Connessione al database fallita.';
    exit();
  }else{
    $i=0;
    $keys="";
    $values="";

    foreach ($_GET as $key => $value) {
      if ($key=='table') {
        continue;
      }
      if($i==0){
        $keys = $keys.$key;
        $values = $values.isNull(isString($conn, $key, $value));
      }else{
        $keys = $keys.",".$key;
        $values = $values.",".isNull(isString($conn, $key, $value));
      }
      $i++;

    }

    //CHECKING THE FORM VALUES
    $formControls = EDITABLE_FORM_CONTROLS();
    if ($formControls[0]==false) {
      $errorMessage=$formControls[1];
      echo <<<EOS
      <script type='text/javascript'>
      if (alert("$errorMessage")) {
        window.location="insertion.php?table=$table";
      } else {
        window.location="insertion.php?table=$table";
      }
      </script>
      EOS;
    }else{
      $query = "START TRANSACTION; INSERT INTO ".$table."(".$keys.") VALUES(".$values."); ".EDITABLE_ATOMIC_INSERTION_ADD_INSTRUCTIONS($table)." COMMIT WORK; END TRANSACTION;";
      $result = pg_query($conn, $query);

  //CHECK QUERY
      if (!$result) {
        echo <<<EOS
        <script type='text/javascript'>
        if (alert("There has been an error! :( query: $query")) {
          window.location="insertion.php?table=$table";
        } else {
          window.location="insertion.php?table=$table";
        }
        </script>
        EOS;
      }else{

  //EVERYTHING IS OK:
        echo <<<EOS
        <script type='text/javascript'>
        if (alert("The values has been inserted into the table :)")) {
          window.location="selection.php?table=$table";
        } else {
          window.location="selection.php?table=$table";
        }
        </script>
        EOS;
      }
    }
  }








}else{ //end of shield
  echo <<<EOS
    $username you can't access this site <br>
    If you want you can <a href="login/login.html"> retry </a> the access
  EOS;
}

?>
