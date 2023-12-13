<?php
//shield
session_start();
include '../include/functions.php';
$username = "";

if (isset($_SESSION["username"])) {
  $username = $_SESSION["username"];
if (isset($_GET["table"])) {

  $header = <<<EOS
  <HTML>
    <HEAD>
      <style>
        table, th, td {
          text-align: left;
          border: 1px solid;
        }
      </style>
      <script>
      function myFunction(button, operation) {
        if(operation == 'deletion'){
          if(confirm("are you sure to delete it?")){
            window.location.href = "../scripts/deletion_submit.php?"+button.id;
          }
        }else if(operation == 'updating'){
          window.location.href = "updating.php?"+button.id+"&oldValuesFlag=true";
        }
      }

      </script>
    </HEAD>
  <BODY>
  EOS;



  $table = $_GET["table"];
  $conn = pg_connect("host=localhost port=5432 dbname=hospitals user=postgres password=diegoturri.com");
  if (!$conn) {
    echo 'Connessione al database fallita.';
    exit();
  }
  else { //connection successful

    //CREATING THE TABLE
    $queryTable = "select column_name from information_schema.columns where table_name='" . $table . "' order by ordinal_position;";
    //echo $queryTable;
    $resultTable = pg_query($conn, $queryTable);
    if (!$resultTable) {
      echo "Si è verificato un errore.<br/>";
      echo pg_last_error($conn);
      exit();
    }
    else {
      print ($header);
      $columnsOrder = '';
      $index = 0;
      echo <<<EOS
        <table>
        <tr><h3><p style="font-weight: bold;">Table: $table </p></h3></tr>
        <tr>
      EOS;
      while ($rowTable = pg_fetch_array($resultTable)) {
        $columns[] = $rowTable[0]; //append the columnName value in the array
        $columnName = $rowTable[0];

        //COLUMN STYLE
        $styleRes = makeStyle($table, $columnName, $conn);
        $style = $styleRes[0];
        $sub = $styleRes[1];

        //COLUMN
        echo '<th '.$style.' >'. $columnName .' <sub '.$sub.'</sub> '.'</th>';

        //ORDER BY VALUES SUPPORT COLUMN NAME LIST
        if ($index==0) {
          $columnsOrder=$columnsOrder.$columnName;
        }else{
          $columnsOrder = $columnsOrder.", ".$columnName;
        }
        $index++;
      };
      echo '</tr>';




      //INSERTING VALUES INSIDE THE TABLE
      $query = "SELECT * FROM " . $table . " order by ".$columnsOrder;
      $result = pg_query($conn, $query);
      if (!$result) {
        echo "Si è verificato un errore.<br/>";
        echo pg_last_error($conn);
        exit();
      }
      else {
        while ($row = pg_fetch_array($result)) {
          $id = 'table='.$table.'&';
          echo '<tr>';
          for ($i = 0;$i < count($columns);$i++) {
            echo '<td>' . $row[$i] . '</td>';
            if($i==count($columns)-1){
              $id = $id.$columns[$i].'='.$row[$i];
            }else{
              $id = $id.$columns[$i].'='.$row[$i].'&';
            }
          };

          //SIDE BUTTONS
          echo <<<EOS
            <td><input type="button" id="$id" onClick="myFunction(this, 'deletion')" value="delete"></td>
            <td><input type="button" id="$id" onClick="myFunction(this, 'updating')" value="update"></td>
            </tr>
          EOS;
        };
      }


      //FOOTER
      echo <<<EOS
                <tr><td><input type="button" value="insert" onClick="window.location.href='insertion.php?table=$table'"></td></tr>
                </table>
                <br>
              EOS;
      echo <<<EOS
                <button onclick="history.back()">Go Back</button>
              EOS;
      echo <<<EOS
                <button onClick="window.location.href='main.php'">Back to Home Page</button>
                <img src="../images/Restructured E-R.png" style="max-width:100%;">
              EOS;
    };
  };
}
else {
  print ($header);
  echo <<<EOS
    There are no data <br>
    If you want you can <a href="main.php"> retry </a> the access
  EOS;
}






}else{ //end of shield
  echo <<<EOS
    you can't access this site <br>
    If you want you can <a href="../index.html"> retry </a> the access
  EOS;
}
?>
</BODY>
</HTML>
