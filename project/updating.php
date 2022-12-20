<?php
session_start();
include 'include/functions.php';
$username = "";

if (isset($_SESSION["username"])) { //shield
  $username = $_SESSION["username"];

//VARIABLES

$header = <<<NOW
<HTML>
  <HEAD>
    <style>
      table, th, td {
        text-align: left;
        border: 1px solid;
      }
    </style>
    <script>
    function myFunction(te) {
      var a = window.location.href;
      if (a.includes("?")) {
        window.location.href = a+"&"+te.name+"="+te.value;
      }else{
        window.location.href = a+"?"+te.name+"="+te.value;
      }
    }

    </script>
  </HEAD>
<BODY>
NOW;

//MAIN

if (isset($_GET["table"])) {
  $table = $_GET["table"];
  $conn = pg_connect("host=localhost port=5432 dbname=hospitals user=postgres password=unimi");

//CHECK CONNECTION
  if (!$conn) {
    echo 'Connessione al database fallita.';
    exit();
  }else{
    $query = "select column_name from information_schema.columns where table_name='" . $table ."' ".table_additional_filtering($table). " order by ordinal_position;";
    $result = pg_query($conn, $query);

//CHECK QUERY
    if (!$result) {
      echo "Si è verificato un errore.<br/>";
      echo pg_last_error($conn);
      exit();
    }else{
//EVERYTHING IS OK:

//KEEP OLD VALUES
      if ($_GET["oldValuesFlag"] == true) {
        unset($_GET["oldValuesFlag"]);
        $i = 0;
        $oldData = '';
        foreach ($_GET as $key => $value) {
          if ($key=='table') {
            continue;
          }
          if ($i == count($_GET)-1) { //remove the oldValuesFlag value from the session insertion
            break;
          }
          if ($i == count($_GET)-2) { //remove the last ','
            $oldData = $oldData.$key."=".isNull(isString($conn, $key, $value));
          }else{
            $oldData = $oldData.$key."=".isNull(isString($conn, $key, $value))." AND ";
          }
          $i++;
        }
        $_SESSION["oldData"] = nullQueryAdaption($oldData);
        echo <<<EOS
          <script type='text/javascript'>
          url = document.URL;
          if (url.includes("oldValuesFlag=true")) {
            window.location.href = url.replace("&oldValuesFlag=true", "");
          }
          </script>
        EOS;
      }


      print ($header);
      echo <<<EOS
      <form method="GET" action="updating_submit.php">
      <table>
        <tr><h3><p style="font-weight: bold;">Tabella: $table </p></h3></tr>
        <input type="hidden" name="table" id="$table" value="$table">
        <tr style="color:grey;">
          <td>Nome</td>
          <td>Inserimento</td>
          <td>Tipologia dato</td>
        </tr>
      EOS;
      $columns = array();

      while ($row = pg_fetch_array($result)) {
        $columnName = $row[0];
        //COLUMN STYLE
        $styleRes = makeStyle($table, $columnName, $conn);
        $style = $styleRes[0];

        if(isForeignKey($table, $columnName, $conn)){
//FOREIGN KEY FIELDS
          echo '<tr><th '.$style.'>' . $columnName . '</th>'.'<td>
          <input type="text" name="'.$columnName.'" list="'."id_".$columnName.'" onChange="myFunction(this)" id="'.$columnName.'">
          <datalist id="'."id_".$columnName.'">
              '.printSuggestions($table, $columnName, $conn).'</datalist></td><td>'.printDomain($conn, $columnName).'</td>';

            $foreignValues = foreignKeyValues($table, $columnName, $conn);
            echo '<td><input type="button" value="Jump" onClick="window.open(\'selection.php?table='.$foreignValues[0].'\')"></td></tr>';
        }else{
//NORMAL FIELDS
          echo '<tr><th '.$style.'>' . $columnName . '</th>'.'<td><input type="text" name="'.$columnName.'" id="'.$columnName.'" onChange="myFunction(this)"></td><td>'.printDomain($conn, $columnName).'</td></tr>';
        }
      };

      //FOOTER
      echo <<<EOS
      <tr><td><input type="submit" value="update"></td></tr>
      </table></form>
      <button onclick="history.back()">Go Back</button>
      <button onClick="window.location.href='main.php'">Back to Home Page</button>
      <button onClick="var a = window.location.href;window.location.href=a+'&table=$table'">Reset</button>
      <img src="img_Ristrutturato.jpg" style="max-width:100%;">
      EOS;
    };

//INSERTION OF THE PRE VALUES
    foreach ($_GET as $key => $value) {
      if ($key=='table') {
        continue;
      }
      echo <<<EOS
        <script>
          document.getElementById("$key").value = "$value";
        </script>
      EOS;
    }
  };

//QUERY WENT WRONG
}else{
  print ($header);
  echo <<<EOS
    There are no data <br>
    If you want you can <a href="main.php"> retry </a> the access
  EOS;
}










}else{ //end of shield
  echo <<<EOS
    $username you can't access this site <br>
    If you want you can <a href="login/login.html"> retry </a> the access
  EOS;
}
?>
</BODY>
</HTML>
