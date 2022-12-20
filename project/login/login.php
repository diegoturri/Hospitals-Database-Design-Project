<?php
session_start();

//anti direct link
if (isset($_SESSION["username"])) {
  header( "Location: step.php" );
}

//program
if (!empty($_POST["username"]) && !empty($_POST["password"])) {
  $username = $_POST["username"];
  $password = $_POST["password"];

//connection
  $conn = pg_connect("host=localhost port=5432 dbname=hospitals user=postgres password=unimi");
  if (!$conn){ //if connection is not successful
  	echo 'Connessione al database fallita.';
  	exit();
  } else { //if connection is successful
  	$query = "SELECT username, password
              FROM persona P1 JOIN dipendente D1 ON P1.cf=D1.cf
              WHERE ruolo='front desk' AND username = '". $username. "' AND password = '" .$password. "'; "; //query gives a result if username and password exists
  	$result=pg_query($conn, $query);
  	$status = pg_result_status($result, PGSQL_STATUS_STRING);
    //echo $status;
  	if (!$result) { //if there was a problem with the query
  		echo "Si è verificato un errore.<br/>";
  		echo pg_last_error($conn);
  		exit();
  	} else { //if there weren't problems with the query
  		if ($row = pg_fetch_array($result)) { //if there's a result
        $_SESSION["username"] = $username;
        header( "Location: ../main.php" );
  		}else{ //if there's not a result
        echo <<<EOS
          User $username the password you have inserted is not valid <br>
          If you want you can <a href="login.html"> retry </a> the access
        EOS;
      }
  	};
  };
}else{
  echo <<<EOS
    you can't access this site <br>
    If you want you can <a href="login.html"> retry </a> the access
  EOS;
}
?>
