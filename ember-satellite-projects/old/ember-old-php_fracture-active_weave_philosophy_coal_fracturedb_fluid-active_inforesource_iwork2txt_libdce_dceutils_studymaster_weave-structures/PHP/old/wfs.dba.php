<?php
/*					 mysql_query('INSERT INTO `data_revision` (`data_revision_id`, `data_revision_name`, `data_revision_length`, `data_revision_type`, `data_revision_node_id`, `data_revision_md5`, `data_revision_data_id`, `data_revision_node_edit_id`) VALUES (NULL, \'' . $HTTP_POST_FILES['uploadeddata']['name'] . '\', \'' . $HTTP_POST_FILES['uploadeddata']['size'] . '\', \'' . fv('dataType') . '\', \'nodeid\', \'' . md5_file($fileTempName) . '\', \'' . $addedDataId . '\', \'not yet known\');'); */
//Run a query based on a given single filter.
include ('d/r/wf.qry.php');
//Print the specified field returned by a query.
include ('d/r/wf.e.php');
//Insert a record into a table, -merging duplicates-, and return the inserted value.
include ('d/r/wf.ins.php');
//Return a specified localised string from the database.
include ('d/r/wf.itr.php');
//Print a specified localised string from the database.
include ('d/r/wf.itf.php');
//Add a new interface record
include ('d/r/wf.newintf.php');
?>
