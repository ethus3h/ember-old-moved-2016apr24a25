<?php
//17 July 2013
error_reporting(E_ALL);
ini_set('display_errors', '1');
echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<style type="text/css" media="all">table,tr,td{border:1px dotted maroon;}"</style>
<title>StudyMaster</title>
</head>
<body><a href="StudyMaster.php?a=home">→StudyMaster Home</a><br><br>';
$mysqli = new mysqli("localhost", "futuqiur_study", "KuTax5.", "futuqiur_studymaster");
/*$result = $mysqli->query("SELECT 'Hello, dear MySQL user!' AS _message FROM DUAL");
$row = $result->fetch_assoc();
echo htmlentities($row['_message']);*/
class baggage_claim
{
    public $temp_temp_table;
    public $tableid;
    public $table;
    public $next;
    function check_luggage($variable, $new_content)
    {
        $this->$variable = $new_content;
    }
    function claim_luggage($variable)
    {
        return $this->$variable;
    }
}
global $baggage_claim;
$baggage_claim = new baggage_claim;
//Get action
if (isset($_GET['a'])) {
    $action = $_GET['a'];
} else {
    $action = $_POST['a'];
}
//Analyze Request Data
if ($_REQUEST['a'] == 'sync') {
    if (isset($_REQUEST['id'])) {
        $columns = $_REQUEST['table'] . "_id";
        $data = '\'' . $_REQUEST['id'] . '\', ';
    }
    if (isset($_REQUEST['name'])) {
        $columns = $columns . ", " . $_REQUEST['table'] . "_name";
        $data = $data . '\'' . $_REQUEST['name'] . '\', ';
        $set = $_REQUEST['table'] . '_name = \'' . $_REQUEST['name'];
    }
    if (isset($_REQUEST['prompt'])) {
        $columns = $columns . ", " . $_REQUEST['table'] . "_prompt";
        $data = $data . '\'' . $_REQUEST['prompt'] . '\', ';
        $set = $set . '\', ' . $_REQUEST['table'] . '_prompt = \'' . $_REQUEST['prompt'];
    }
    if (isset($_REQUEST['response'])) {
        $columns = $columns . ", " . $_REQUEST['table'] . "_response";
        $data = $data . '\'' . $_REQUEST['response'] . '\', ';
        $set = $set . '\', ' . $_REQUEST['table'] . '_response = \'' . $_REQUEST['response'];
    }
    if (isset($_REQUEST['year'])) {
        $columns = $columns . ", " . $_REQUEST['table'] . "_year";
        $data = $data . '\'' . $_REQUEST['year'] . '\', ';
        $set = $set . '\', ' . $_REQUEST['table'] . '_year = \'' . $_REQUEST['year'];
    }
    if (isset($_REQUEST['class'])) {
        $columns = $columns . ", " . $_REQUEST['table'] . "_class";
        $data = $data . '\'' . $_REQUEST['class'] . '\', ';
        $set = $set . '\', ' . $_REQUEST['table'] . '_class = \'' . $_REQUEST['class'];
    }
    if (isset($_REQUEST['unit'])) {
        $columns = $columns . ", " . $_REQUEST['table'] . "_unit";
        $data = $data . '\'' . $_REQUEST['unit'] . '\'';
        $set = $set . '\', ' . $_REQUEST['table'] . '_unit = \'' . $_REQUEST['unit'];
    }
    if (isset($_REQUEST['source'])) {
        $columns = $columns . ", " . $_REQUEST['table'] . "_source";
        $data = $data . '\'' . $_REQUEST['source'] . '\', ';
        $set = $set . '\', ' . $_REQUEST['table'] . '_source = \'' . $_REQUEST['source'];
    }
    if (isset($_REQUEST['enabled'])) {
        $columns = $columns . ", " . $_REQUEST['table'] . "_enabled";
        $data = $data . '\'' . $_REQUEST['enabled'] . '\'';
        $set = $set . '\', ' . $_REQUEST['table'] . '_enabled = \'' . $_REQUEST['enabled'] . '\'';
    }
    $statement = "INSERT INTO " . $_REQUEST['table'] . ' (' . $columns . ') VALUES (' . $data . ") ON DUPLICATE KEY UPDATE " . $set . ';';
} else {
}
if (isset($_GET['why'])) {
    global $why;
    $why = $_GET['why'];
} else {
}
function build_table($table, $columns)
{
    global $mysqli;
    $nextid = $mysqli->query('SHOW TABLE STATUS LIKE \'' . $table . '\';');
    $tables = $table . 's';
    $$tables = $mysqli->query('SELECT * FROM ' . $table . ';');
    $temp_tables = $$tables;
    $next = $nextid->fetch_assoc();
    $next = $next['Auto_increment'];
    $tableid = $table . '_id';
    global $baggage_claim;
    $baggage_claim->check_luggage('tableid', $table . '_id');
    $baggage_claim->check_luggage('table', $table);
    $table_enabled = $table . '_enabled';
    $table_name = $table . '_name';
    $temp_table = $table;
    while ($$temp_table = $temp_tables->fetch_assoc()) {
        global $counter;
        global $baggage_claim;
        $baggage_claim->check_luggage('temp_temp_table', $$temp_table);
        $temp_temp_table = $$temp_table;
        if ($temp_temp_table[$table_enabled] == '0') {
            $checked = '';
        } else {
            $checked = 'checked';
        }
        echo '<tr><td id="' . $table . '_id_' . $temp_temp_table["$tableid"] . '">' . $temp_temp_table["$tableid"] . '</td>';
        $counter = 1;
        while ($counter < (count(explode(',', $columns)) - 1)) {
            $columnarray = explode(',', $columns);
            global $baggage_claim;
            $temp_temp_table = $baggage_claim->claim_luggage('temp_temp_table');
            $tableid = $baggage_claim->claim_luggage('tableid');
            $table = $baggage_claim->claim_luggage('table');
            //print_r($temp_temp_table);
            echo '<td contentEditable="true" id="' . $table . '_' . $columnarray[$counter] . '_' . $temp_temp_table["$tableid"] . '" onKeyPress="sync_' . $table . '_' . $temp_temp_table["$tableid"] . '();">' . $temp_temp_table[$table . '_' . $columnarray[$counter]] . '</td>';
            $counter++;
        }
        echo '<td><input type="checkbox" value="1" ' . $checked . ' id="' . $table . '_enabled_' . $temp_temp_table["$tableid"] . '" onClick="sync_' . $table . '_' . $temp_temp_table["$tableid"] . '();"></td></tr>
    ';
        echo '<script type="text/javascript">function sync_' . $table . '_' . $temp_temp_table["$tableid"] . '() {
     setTimeout(function () {';
        $counter = 0;
        while ($counter <= (count(explode(',', $columns)) - 1)) {
            $columnarray = explode(',', $columns);
            global $baggage_claim;
            $temp_temp_table = $baggage_claim->claim_luggage('temp_temp_table');
            $tableid = $baggage_claim->claim_luggage('tableid');
            $table = $baggage_claim->claim_luggage('table');
            //print_r($temp_temp_table);
            echo '   var ' . $table . '_' . $columnarray[$counter] . '_' . $temp_temp_table["$tableid"] . ' = document.getElementById(\'' . $table . '_' . $columnarray[$counter] . '_' . $temp_temp_table["$tableid"] . '\').innerHTML;

       ';
            $counter++;
        }
        echo 'if (document.getElementById(\'' . $table . '_enabled_' . $temp_temp_table["$tableid"] . '\').checked == true)
        {
var ' . $table . '_enabled_' . $temp_temp_table["$tableid"] . ' = 1;
        }
        else
        {
        var ' . $table . '_enabled_' . $temp_temp_table["$tableid"] . ' = 0;
        }
   var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  
  var send="a=sync&table=' . $table . '&id=' . $temp_temp_table["$tableid"];
        $counter = 0;
        while ($counter < (count(explode(',', $columns)) - 1)) {
            $columnarray = explode(',', $columns);
            global $baggage_claim;
            $temp_temp_table = $baggage_claim->claim_luggage('temp_temp_table');
            $tableid = $baggage_claim->claim_luggage('tableid');
            $table = $baggage_claim->claim_luggage('table');
            //print_r($temp_temp_table);
            echo '&' . $columnarray[$counter] . '=" + ' . $table . '_' . $columnarray[$counter] . '_' . $temp_temp_table["$tableid"] . ' + "';
            $counter++;
        }
        echo '" + "&enabled=";
  if(' . $table . '_enabled_' . $temp_temp_table["$tableid"] . ' == null) {
  send=send;
  }
  else 
  {
  send=send + ' . $table . '_enabled_' . $temp_temp_table["$tableid"] . ';
  }
xmlhttp.open("POST","/d/r/StudyMaster.php",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

xmlhttp.send(send);
   }, 100);
}

</script>';
    }
    echo '<tr><td id="' . $table . '_id_' . $next . '">' . $next . '</td>';
    $counter = 1;
    $baggage_claim->check_luggage('next', $next);
    while ($counter < (count(explode(',', $columns)) - 1)) {
        $columnarray = explode(',', $columns);
        global $baggage_claim;
        $temp_temp_table = $baggage_claim->claim_luggage('temp_temp_table');
        $tableid = $baggage_claim->claim_luggage('tableid');
        $table = $baggage_claim->claim_luggage('table');
        $next = $baggage_claim->claim_luggage('next');
        //print_r($temp_temp_table);
        echo '<td contentEditable="true" id="' . $table . '_' . $columnarray[$counter] . '_' . $next . '" onKeyPress="sync_' . $table . '_' . $next . '();document.getElementById(\'' . $table . '_enabled_' . $next . '\').checked=true;setTimeout(function(){document.location.reload(true);},500);"></td>';
        $counter++;
    }
    echo '<td><input type="checkbox" value="1" id="' . $table . '_enabled_' . $next . '" onClick="sync_' . $table . '_' . $next . '();setTimeout(function(){document.location.reload(true);},500);"></td></tr>
    ';
    echo '<script type="text/javascript">function sync_' . $table . '_' . $next . '() {
     setTimeout(function () {
    var ' . $table . '_name_' . $next . ' = document.getElementById(\'' . $table . '_name_' . $next . '\').innerHTML;
if (document.getElementById(\'' . $table . '_enabled_' . $next . '\').checked == true)
        {
var ' . $table . '_enabled_' . $next . ' = 1;
        }
        else
        {
        var ' . $table . '_enabled_' . $next . ' = 0;
        }
   var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
 var send="a=sync&table=' . $table . '&id=' . $next;
    $counter = 0;
    while ($counter < (count(explode(',', $columns)) - 1)) {
        $columnarray = explode(',', $columns);
        global $baggage_claim;
        $temp_temp_table = $baggage_claim->claim_luggage('temp_temp_table');
        $tableid = $baggage_claim->claim_luggage('tableid');
        $table = $baggage_claim->claim_luggage('table');
        $next = $baggage_claim->claim_luggage('next');
        //print_r($temp_temp_table);
        echo '&' . $columnarray[$counter] . '=" + ' . $table . '_' . $columnarray[$counter] . '_' . $next . ' + "';
        $counter++;
    }
    echo '" + "&enabled=";
  if(' . $table . '_enabled_' . $next . ' == null) {
  send=send;
  }
  else 
  {
  send=send + ' . $table . '_enabled_' . $next . ';
  }
xmlhttp.open("POST","/d/r/StudyMaster.php",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

xmlhttp.send(send);
   }, 100);
}

</script>';
    echo '</table>';
}
switch ($action) {
case 'home':
    echo 'Welcome to StudyMaster! Please select:<br><br><ul><li>
<a href="StudyMaster.php?a=study">Study existing facts</a>
</li>
<li>
<a href="StudyMaster.php?a=years">Add a year</a></li>
<li>
<a href="StudyMaster.php?a=pick_year&why=classes">Add a class</a></li>
<li>
<a href="StudyMaster.php?a=pick_year&why=units">Add a unit</a></li>
<li>
<a href="StudyMaster.php?a=pick_year&why=sources">Add a source</a></li>
<li>
<a href="StudyMaster.php?a=pick_year&why=rules">Edit rules for a source</a></li>
<li>
<a href="StudyMaster.php?a=pick_run">View a run</a></li>';
    break;

case 'sync':
    global $statement;
    echo $statement;
    $mysqli->query($statement);
    break;

case 'pick_year':
    global $why;
    echo 'Years:<br><br><ul>';
    $years = $mysqli->query('SELECT * FROM year;');
    switch ($why) {
    case 'classes':
        $newwhy = '';
        while ($year = $years->fetch_assoc()) {
            echo '<li><a href="StudyMaster.php?a=classes&year=' . $year['year_id'] . '">' . $year['year_name'] . '</a></li>';
        }
        break;

    case 'units':
        $newwhy = '';
        while ($year = $years->fetch_assoc()) {
            echo '<li><a href="StudyMaster.php?a=pick_class&year=' . $year['year_id'] . '&why=units">' . $year['year_name'] . '</a></li>';
        }
        break;

    case 'sources':
        $newwhy = '';
        while ($year = $years->fetch_assoc()) {
            echo '<li><a href="StudyMaster.php?a=pick_class&year=' . $year['year_id'] . '&why=sources">' . $year['year_name'] . '</a></li>';
        }
        break;

    case 'facts':
        $newwhy = '';
        while ($year = $years->fetch_assoc()) {
            echo '<li><a href="StudyMaster.php?a=pick_class&year=' . $year['year_id'] . '&why=facts">' . $year['year_name'] . '</a></li>';
        }
        break;
    }
    while ($year = $years->fetch_assoc()) {
        echo '<li><a href="StudyMaster.php?a=pick_classes&why="' . $GLOBALS['why'] . '">' . $year['year_name'] . '</a></li>';
    }
    break;

case 'years':
    echo 'Years:<br><br><table><tr><td style="font-weight:bold;">ID</td><td style="font-weight:bold;">Year</td><td style="font-weight:bold;">Enabled</td></tr>
';
    build_table('year', 'id,name,enabled');
    break;

case 'classes':
    echo 'Classes:<br><br><table><tr><td style="font-weight:bold;">ID</td><td style="font-weight:bold;">Class</td><td style="font-weight:bold;">Year</td><td style="font-weight:bold;">Enabled</td></tr>
';
    build_table('class', 'id,name,year,enabled');
    break;

case 'pick_class':
    global $why;
    echo 'classes:<br><br><ul>';
    $classes = $mysqli->query('SELECT * FROM class;');
    switch ($why) {
    case 'units':
        $newwhy = '';
        while ($class = $classes->fetch_assoc()) {
            echo '<li><a href="StudyMaster.php?a=units&class=' . $class['class_id'] . '">' . $class['class_name'] . '</a></li>';
        }
        break;

    case 'sources':
        $newwhy = '';
        while ($class = $classes->fetch_assoc()) {
            echo '<li><a href="StudyMaster.php?a=pick_unit&class=' . $class['class_id'] . '&why=sources">' . $class['class_name'] . '</a></li>';
        }
        break;

    case 'facts':
        $newwhy = '';
        while ($class = $classes->fetch_assoc()) {
            echo '<li><a href="StudyMaster.php?a=pick_unit&class=' . $class['class_id'] . '&why=facts">' . $class['class_name'] . '</a></li>';
        }
        break;
    }
    while ($class = $classes->fetch_assoc()) {
        echo '<li><a href="StudyMaster.php?a=pick_unit&why="' . $GLOBALS['why'] . '">' . $class['class_name'] . '</a></li>';
    }
    break;

case 'classes':
    echo 'classes:<br><br><table><tr><td style="font-weight:bold;">ID</td><td style="font-weight:bold;">class</td><td style="font-weight:bold;">Enabled</td></tr>
';
    build_table('class', 'id,name,enabled');
    break;

case 'classes':
    echo 'Classes:<br><br><table><tr><td style="font-weight:bold;">ID</td><td style="font-weight:bold;">Class</td><td style="font-weight:bold;">class</td><td style="font-weight:bold;">Enabled</td></tr>
';
    build_table('class', 'id,name,class,enabled');
    break;

case 'pick_unit':
    global $why;
    echo 'units:<br><br><ul>';
    $units = $mysqli->query('SELECT * FROM unit;');
    switch ($why) {
    case 'units':
        $newwhy = '';
        while ($unit = $units->fetch_assoc()) {
            echo '<li><a href="StudyMaster.php?a=pick_unit&unit=' . $unit['unit_id'] . '&why=units">' . $unit['unit_name'] . '</a></li>';
        }
        break;

    case 'sources':
        $newwhy = '';
        while ($unit = $units->fetch_assoc()) {
            echo '<li><a href="StudyMaster.php?a=pick_unit&unit=' . $unit['unit_id'] . '&why=sources">' . $unit['unit_name'] . '</a></li>';
        }
        break;

    case 'facts':
        $newwhy = '';
        while ($unit = $units->fetch_assoc()) {
            echo '<li><a href="StudyMaster.php?a=pick_unit&unit=' . $unit['unit_id'] . '&why=facts">' . $unit['unit_name'] . '</a></li>';
        }
        break;
    }
    while ($unit = $units->fetch_assoc()) {
        echo '<li><a href="StudyMaster.php?a=pick_units&why="' . $GLOBALS['why'] . '">' . $unit['unit_name'] . '</a></li>';
    }
    break;

case 'units':
    echo 'units:<br><br><table><tr><td style="font-weight:bold;">ID</td><td style="font-weight:bold;">unit</td><td style="font-weight:bold;">Enabled</td></tr>
';
    build_table('unit', 'id,name,enabled');
    break;

case 'units':
    echo 'units:<br><br><table><tr><td style="font-weight:bold;">ID</td><td style="font-weight:bold;">unit</td><td style="font-weight:bold;">unit</td><td style="font-weight:bold;">Enabled</td></tr>
';
    build_table('unit', 'id,name,unit,enabled');
    break;

default:
    break;
}
echo '</body></html>';
?>