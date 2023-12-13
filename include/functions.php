<?php

//the EDITABLE functions are the Not-Automatic part of the project,
//they depends on a lot of factors that are not automatically changed






function EDITABLE_SUGGESTIONS_WHERE_FILTERING($tableName, $columnName, $conn, $foreignColumnName, $foreignTableName){ //add filters to the suggestions of a specific table
  //if the table is "prenotazione" you must say that paziente must be selected from the prescrizione
  switch ([$_GET["table"],$foreignTableName]) {

    case ['prenotazione','slottemporale']:
      //echo "<script type='text/javascript'>alert('$message');</script>";
      return "AND dataesame>CURRENT_DATE AND occupato<>'t' AND stanza IN (SELECT S1.stanza FROM riempimento R1 JOIN slottemporale S1 ON R1.stanza=S1.stanza WHERE R1.allestimento='Ambulatorio generale')";
      break;

    case ['ricovero','postoletto']:
      return "AND stato='libero'";
      break;

    default:
      return '';
      break;
  }
}



function EDITABLE_ATOMIC_INSERTION_ADD_INSTRUCTIONS($table){
  if ($table=='prenotazione') {
    $dataEsame=$_GET["dataesame"];
    $oraEsame=$_GET["oraesame"];
    $res = "UPDATE slottemporale SET occupato='t' WHERE dataesame='".$dataEsame."' AND oraesame='".$oraEsame."';";
    return $res;
  }

  return '';
}


function EDITABLE_FORM_CONTROLS(){
  $table = $_GET["table"];

  switch ($table) {
    case 'postoletto':
      if ($_GET['allestimentoriempimento']=='ricovero' && $_GET['stanzariempimento']=='') {
        return [false, "stanzariempimento is mandatory if allestimentoriempimento is 'ricovero'"];
      }
      break;

    default:
      return [true, ""];
      break;
  }
  return [true, ""];
}



function table_additional_filtering($table){ //add filters to the specific table
  return "";
}



function printDomain($conn, $attributeName){

  $query = "select data_type, character_maximum_length, numeric_precision, is_nullable
            FROM information_schema.columns
            WHERE table_name='".$_GET["table"]."' AND column_name='".$attributeName."'";

  $result = pg_query($conn, $query);
  if (!$result) {
    echo "Si è verificato un errore.<br/>";
    echo pg_last_error($conn);
  }else{
    while ($res = pg_fetch_array($result)) {
      if($res[3]=="NO"){
        return $res[0]." ".$res[1]." ".$res[2]. "   ----- Not nullable";
      }else{
        return $res[0]." ".$res[1]." ".$res[2]. "   ----- Nullable";
      }

    }
  }
  return "";

}



function isForeignKey($tableName, $columnName, $conn){
  $query = "select *
            FROM information_schema.table_constraints T1 JOIN information_schema.constraint_column_usage T2 ON T1.constraint_name = T2.constraint_name
            WHERE T1.constraint_type = 'FOREIGN KEY' AND T1.table_name='".$tableName."' AND T1.constraint_name LIKE '%_".$columnName."_%';
            ";
  $result = pg_query($conn, $query);
  if (!$result) {
    echo "Si è verificato un errore.<br/>";
    echo pg_last_error($conn);
  }else{
    while ($res = pg_fetch_array($result)) {
      // the column is a foreign key
      return true;
    }
  }

  return false;
}



function foreignKeyValues($tableName, $columnName, $conn){
  $query = "select *
            FROM information_schema.table_constraints T1 JOIN information_schema.constraint_column_usage T2 ON T1.constraint_name = T2.constraint_name
            WHERE T1.constraint_type = 'FOREIGN KEY' AND T1.table_name='".$tableName."' AND T1.constraint_name LIKE '%_".$columnName."_%';
            ";
  $result = pg_query($conn, $query);
  if (!$result) {
    echo "Si è verificato un errore.<br/>";
    echo pg_last_error($conn);
  }else{
    $i=1;
    while ($res = pg_fetch_array($result)) {
      $stringDivided = explode("_", $res["constraint_name"]);

      if ($columnName != $stringDivided[$i]) { //if there are multiple values inside a foreign key this will be helpful
        $i++;
        continue;
      }

      return array($res["table_name"], $res["column_name"]);
    }
  }


  return array("", "");
}



function sql_suggestions_where_filtering($tableName, $columnName, $conn, $foreignColumnName, $foreignTableName){ //it puts the WHERE clausole if necessary or even a JOIN
  //Remember that I'm using this after being sure it's a foreign key, so everythhing you do here below is related to the foreign keys

  //PROTECTION for the not-multiple foreign keys
  $query = "select *
            FROM information_schema.table_constraints T1 JOIN information_schema.constraint_column_usage T2 ON T1.constraint_name = T2.constraint_name
            WHERE T1.constraint_type = 'FOREIGN KEY' AND T1.table_name='".$tableName."' AND T1.constraint_name LIKE '%_".$columnName."_%';
            ";
  $result = pg_query($conn, $query);
  if (!$result) {
    echo "Si è verificato un errore.<br/>";
    echo pg_last_error($conn);
  }else{
    $i=1;
    while ($res = pg_fetch_array($result)) {
      $stringDivided = explode("_", $res["constraint_name"]);
      if (count($stringDivided) <= 3) { //if it's not a multiple foreign key
        //EXIT WITHOUT THE FILTER
        return "";
      }
    }
  }
  //END OF PROTECTION of not-multiple foreign keys


    //the column has already a value, put it in the filter
    if (!isset($_GET["filter"]) && $_GET[$columnName] != "") { //if it's the first filter
      $_GET["filter"] = " WHERE ".$foreignColumnName." = '".$_GET[$columnName]."' ".EDITABLE_SUGGESTIONS_WHERE_FILTERING($tableName, $columnName, $conn, $foreignColumnName, $foreignTableName);
      return $baseFilter.$_GET["filter"];
    }else if ($_GET[$columnName] != ""){ //from the second filter on
      $_GET["filter"] = $_GET["filter"]." AND ".$foreignColumnName." = '".$_GET[$columnName]."' ".EDITABLE_SUGGESTIONS_WHERE_FILTERING($tableName, $columnName, $conn, $foreignColumnName, $foreignTableName);
      return $_GET["filter"];
    }


  return $_GET["filter"];
}



function printSuggestions($tableName, $columnName, $conn){
  $update = foreignKeyValues($tableName, $columnName, $conn);
  $foreignTableName = $update[0];
  $foreignColumnName = $update[1];

  $options = '';
  $query = "SELECT DISTINCT ".$foreignColumnName." FROM " . $foreignTableName . sql_suggestions_where_filtering($tableName, $columnName, $conn, $foreignColumnName, $foreignTableName) . " ORDER BY ".$foreignColumnName.";";
  //echo $query."<br>";
  $result = pg_query($conn, $query);
  if (!$result) {
    echo "Si è verificato un errore.<br/>";
    echo pg_last_error($conn);
  }else{
    while ($res = pg_fetch_array($result)) {
      $options = $options.'<option value="'.$res[0].'" name="'.$res[0].'">';
    }
  }

  return $options;
}






//_SUBMIT

function isString($conn, $key, $value){

  $query = "select data_type
            FROM information_schema.columns
            WHERE table_name='".$_GET["table"]."' AND column_name='".$key."'";

  $result = pg_query($conn, $query);
  if (!$result) {
    echo "Si è verificato un errore.<br/>";
    echo pg_last_error($conn);
  }else{
    while ($res = pg_fetch_array($result)) {
      if ($res[0] == "date" || $res[0] == "character varying" || $res[0] == "character") {
        return "'".$value."'";
      }
    }
  }

  return $value;

}



function isNull($value){
  if ($value=='""' || $value=="''") {
    return "NULL";
  }else{
    return $value;
  }
}

function nullQueryAdaption($queryValue){
  return str_replace("=NULL"," IS NULL ",$queryValue);
}



function isPrimaryKey($tableName, $columnName, $conn){
  $query=<<<EOS
  SELECT c.column_name, c.data_type
  FROM information_schema.table_constraints tc
    JOIN information_schema.constraint_column_usage AS ccu USING (constraint_schema, constraint_name)
    JOIN information_schema.columns AS c ON c.table_schema = tc.constraint_schema
      AND tc.table_name = c.table_name AND ccu.column_name = c.column_name
  WHERE constraint_type = 'PRIMARY KEY' and tc.table_name = '$tableName' AND c.column_name='$columnName';
  EOS;
  $result = pg_query($conn, $query);
  if (!$result) {
    echo "Si è verificato un errore.<br/>";
    echo pg_last_error($conn);
  }else{
    while ($res = pg_fetch_array($result)) {
      // the column is a foreign key
      return true;
    }
  }

  return false;
}



function firstColumnName($tableName, $conn){
  $query=<<<EOS
  SELECT column_name
  FROM information_schema.columns
  WHERE table_name='$tableName'
  ORDER BY ordinal_position;
  EOS;
  $result = pg_query($conn, $query);
  if (!$result) {
    echo "Si è verificato un errore.<br/>";
    echo pg_last_error($conn);
  }else{
    while ($res = pg_fetch_array($result)) {
      // the column is a foreign key
      return $res[0];
    }
  }

  return '';
}

function makeStyle($table, $columnName, $conn){
  $style = '';
  $sub = '';
  if (isPrimaryKey($table, $columnName, $conn)) {
    $style = 'style="background-color:#FFA07A;text-decoration: underline;"';
  }else if(isForeignKey($table,$columnName,$conn)){
    $style = 'style="background-color:#AFEEEE"';
    $sub = 'style="vertical-align: sub;font-size: xx-small;">FK';
  }
  return [$style, $sub];
}
?>
