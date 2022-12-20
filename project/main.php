<html>
  <head>
    <title>Select automatic</title>
  </head>
<body>
<?php
//shield
session_start();

$username = "";

if (isset($_SESSION["username"])) {
  $username = $_SESSION["username"];








$conn = pg_connect("host=localhost port=5432 dbname=hospitals user=postgres password=unimi");
if (!$conn) {
  echo 'Connessione al database fallita.';
  exit();
}
else {
  //echo "Connessione riuscita."."<br/>";
  $query = "SELECT table_name as table FROM information_schema.tables WHERE table_type='BASE TABLE' and table_schema='public' and table_name <> 'utentiautenticati' and table_name <> 'timetablefree' ORDER BY table_name";
  $result = pg_query($conn, $query);
  if (!$result) {
    echo "Si è verificato un errore.<br/>";
    echo pg_last_error($conn);
    exit();
  }
  else {
    print ("<form action=\"selector_middle.php\" method=\"POST\">");
    echo '<tr><h3><p style="font-weight: bold;"> Select a table </p></h3></tr>';
    print ("<select name=\"tabella\">");
    while ($row = pg_fetch_array($result)) {
      print ("<option value=\"" . htmlspecialchars($row["table"]) . "\">");
      echo $row["table"];
      print ("</option>");
    };
    print ("</select>");
    print ("<input  type='submit' value='select'>");
    print ("<br><br>");
    print ("<input  type='button' value='new guest' onClick='window.location.href=\"selector_middle.php?path=newGuest\"'>");
    print ("<input  type='button' value='prescribe an exam' onClick='window.location.href=\"selector_middle.php?path=prescribeAnExam\"'>");
    print ("<input  type='button' value='book an exam' onClick='window.location.href=\"selector_middle.php?path=bookAnExam\"'>");
    print ("</form>");
  };
};

echo '<img src="img_Ristrutturato.jpg" style="max-width:100%;">';





}else{ //end of shield
  echo <<<EOS
    $username you can't access this site <br>
    If you want you can <a href="login/login.html"> retry </a> the access
  EOS;
}
?>
</body>
</html>
