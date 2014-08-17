<?php
//Run a query based on a given single filter.
function qry($table, $field, $filter, $filtervalue)
{
    $mysqlquery = "SELECT `" . $field . "` FROM `" . $table . "` WHERE `" . $filter . '`=\'' . $filtervalue . '\'';
    //echo $mysqlquery;
    $arrayir = mysql_fetch_array(mysql_query($mysqlquery));
    return $arrayir[$field];
}
?>
