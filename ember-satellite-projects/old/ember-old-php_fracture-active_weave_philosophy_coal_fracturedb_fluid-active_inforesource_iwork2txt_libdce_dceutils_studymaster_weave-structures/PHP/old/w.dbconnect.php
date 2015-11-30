<?php
//CONNECT TO DATABASE
define('DB_CHARSET', 'utf8');
$link = mysql_connect($dbServer, $dbUsername, $dbPassword);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
mysql_set_charset('utf8');
mysql_select_db($dbName);
?>
