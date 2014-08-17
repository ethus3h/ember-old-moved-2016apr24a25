<?php
//Insert a record into a table, merging duplicates, and return the inserted value.
//TODO: Figure out what this actually does, how it does it, and make it work
function ins($xqtable, $xqdata)
{
    $xqactionstring = str_replace('insert', 'INSERT INTO ', $xqaction);
    $xqactionmiddle = str_replace('insert', ' VALUES ', $xqaction);
    $xqfieldnames = implode(mysql_real_escape_array(array_keys($xqdata)), '`, `');
    $xqvalues = implode(mysql_real_escape_array(array_values($xqdata)), '\', \'');
    foreach ($xqdata as $key => $value) {
        $sqlstring.= "'split'`$key` = '$value'";
    }
    $xqselectconditions = implode(explode("'split'", $sqlstring), ' AND ');
    //	print_r($xqselectconditions);
    $xqidfield = $xqtable . '_id';
    //This next block of code needs to be uncommented for the duplicate-checking feature to work… as does the closing brace a ways down…
    /*			$xqinitqueryarray = mysql_fetch_array(mysql_query("SELECT `" . $xqidfield . "` FROM `" . $xqtable . "` WHERE `" . $xqselectconditions . ';'));
        
        if ($xqinitqueryarray[$xqidfield])
        {
        $xqnewitemid = $xqinitqueryarray[$xqidfield];
        }
        else
        { */
    $insquery = 'INSERT INTO  `' . $xqtable . '` ( `node_revision_id`, `' . $xqfieldnames . '` ) VALUES ( NULL ,  \'' . $xqvalues . '\');';
    //echo $insquery;
    mysql_query($insquery);
    global $newIntfId;
    $xqnewitemid = mysql_insert_id();
    //			}
    global $newItemId;
    $newItemId = $xqnewitemid;
}
?>
