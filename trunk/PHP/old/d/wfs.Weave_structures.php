<?php
//Weave         abstraction layer
//   structures
//Add a new node
function newNodeExecute()
{
    if (fv('nodeDataUploadFlag')) {
        //					 echo 'Adding data…';
        $tablenamenewdata = "data";
        $next_incrementdata = 0;
        $qShowStatusdata = "SHOW TABLE STATUS LIKE '$tablenamenewdata'";
        $qShowStatusResultdata = mysql_query($qShowStatusdata) or die("Query failed: " . mysql_error() . "<br/>" . $qShowStatusdata);
        $rowdata = mysql_fetch_assoc($qShowStatusResultdata);
        $next_incrementdata = $rowdata['Auto_increment'];
        mysql_query('INSERT INTO `data` (`data_id`, `data_current_revision`) VALUES (NULL, \'' . $next_incrementdata . '\');');
        $addedDataId = mysql_insert_id();
        //					 echo 'data number ' . $addedDataId . 'and data revision number ';
        $fileTempName = $_FILES['uploadeddata']['tmp_name'];
        mysql_query('INSERT INTO `data_revision` (`data_revision_id`, `data_revision_name`, `data_revision_length`, `data_revision_type`, `data_revision_node_id`, `data_revision_md5`, `data_revision_data_id`, `data_revision_node_edit_id`) VALUES (NULL, \'' . $HTTP_POST_FILES['uploadeddata']['name'] . '\', \'' . $HTTP_POST_FILES['uploadeddata']['size'] . '\', \'' . fv('dataType') . '\', \'nodeid\', \'' . md5_file($fileTempName) . '\', \'' . $addedDataId . '\', \'not yet known\');');
        $targetULDirectory = 'weave/data/' . str_replace(0, '0/', str_replace(1, '1/', str_replace(2, '2/', str_replace(3, '3/', str_replace(4, '4/', str_replace(5, '5/', str_replace(6, '6/', str_replace(7, '7/', str_replace(8, '8/', str_replace(9, '9/', mysql_insert_id()))))))))));
        mkdir($targetULDirectory, 0700, true);
        /*					  $ck = mysql_insert_id();
                $subdirs = array();
                
                for ($i = 0;$i < strlen($ck);$i++) $subdirs[] = $ck[$i];
        */
        $addedDataRevisionId = mysql_insert_id();
        //					echo $addedDataRevisionId;
        $targetULDirectory = $targetULDirectory . $addedDataRevisionId . '.wdf';
        //					 echo $targetULDirectory;
        move_uploaded_file($fileTempName, $targetULDirectory);
    } else {
        //					 echo 'not adding data. ';
        
    }
    $tablenamenewnode = "node_revision";
    $next_incrementnode = 0;
    $qShowStatusnode = "SHOW TABLE STATUS LIKE '$tablenamenewnode'";
    $qShowStatusResultnode = mysql_query($qShowStatusnode) or die("Query failed: " . mysql_error() . "<br/>" . $qShowStatusnode);
    $rownode = mysql_fetch_assoc($qShowStatusResultnode);
    $next_incrementnode_revision = $rownode['Auto_increment'];
    mysql_query('INSERT INTO  `node` (	`node_id` , `node_current_revision` ) VALUES (NULL ,  \'' . $next_incrementnode_revision . '\');');
    $nodeAddedId = mysql_insert_id();
    /* TODO: Rewrite this whole next query to use the interface table for localisation */
    /*				$nodeRevisionInsertQuery = 'INSERT INTO
            `node_revision` (
            
            `node_revision_id` ,
            `node_revision_type` ,
            `node_revision_title` ,
            `node_revision_permissions` ,
            `node_revision_relationships` ,
            `node_revision_source` ,
            `node_revision_sort_title` ,
            `node_revision_description` ,
            `node_revision_disambiguation_description` ,
            `node_revision_metadata` ,
            `node_revision_comment` ,
            `node_revision_short_description` ,
            `node_revision_universe_status` ,
            `node_revision_owner` ,
            `node_revision_copyright_flag` ,
            `node_revision_morality_flag` ,
            `node_revision_personal_flag` ,
            `node_revision_data_id` ,
            `node_revision_minor_flag`
            
            )
            
            VALUES (
            
            NULL,
            \'' . mysql_real_escape_string($_POST['nodeType']) . '\',
            \'' . mysql_real_escape_string($nodeTitleIntfId) . '\',
            \'' . mysql_real_escape_string($_POST['nodePermissions']) . '\',
            \'' . mysql_real_escape_string($_POST['nodeRelationships']) . '\',
            \'' . mysql_real_escape_string($nodeSourceIntfId) . '\',
            \'' . mysql_real_escape_string($nodeSortTitleIntfId) . '\',
            \'' . mysql_real_escape_string($nodeDescriptionIntfId) . '\',
            \'' . mysql_real_escape_string($nodeDisambiguationDescriptionIntfId) . '\',
            \'' . mysql_real_escape_string($_POST['nodeMetadata']) . '\',
            \'' . mysql_real_escape_string($nodeCommentIntfId) . '\',
            \'' . mysql_real_escape_string($nodeShortDescriptionIntfId) . '\',
            \'' . mysql_real_escape_string($_POST['nodeUniverseStatus']) . '\',
            \'' . mysql_real_escape_string($newNodeOwnerId) . '\',
            \'' . mysql_real_escape_string($_POST['nodeCopyrightFlag']) . '\',
            \'' . mysql_real_escape_string($_POST['nodeMoralityFlag']) . '\',
            \'' . mysql_real_escape_string($_POST['nodePersonalFlag']) . '\',
            \'' . mysql_real_escape_string($addedDataId) . '\',
            \'' . mysql_real_escape_string($_POST['nodeMinorFlag']) . "
            
            ');";
    */
    newintf($_POST['nodeDisplayTitle']);
    global $newIntfId;
    $nodeDisplayTitleIntfId = $newIntfId;
    newintf($_POST['nodeShortTitle']);
    global $newIntfId;
    $nodeShortTitleIntfId = $newIntfId;
    newintf($_POST['nodeTitle']);
    global $newIntfId;
    $nodeTitleIntfId = $newIntfId;
    newintf($_POST['nodeSource']);
    global $newIntfId;
    $nodeSourceIntfId = $newIntfId;
    newintf($_POST['nodeSortTitle']);
    global $newIntfId;
    $nodeSortTitleIntfId = $newIntfId;
    newintf($_POST['nodeDescription']);
    global $newIntfId;
    $nodeDescriptionIntfId = $newIntfId;
    newintf($_POST['nodeDisambiguationDescription']);
    global $newIntfId;
    $nodeDisambiguationDescriptionIntfId = $newIntfId;
    newintf($_POST['nodeComment']);
    global $newIntfId;
    $nodeCommentIntfId = $newIntfId;
    newintf($_POST['nodeShortDescription']);
    global $newIntfId;
    $nodeShortDescriptionIntfId = $newIntfId;
    $newNodeOwnerId = qry('user', 'user_id', 'user_name', mysql_real_escape_string($_POST['userName']));
    $newNodeData = array("node_revision_type" => $_POST['nodeType'], "node_revision_display_title" => $nodeDisplayTitleIntfId, "node_revision_short_title" => $nodeShortTitleIntfId, "node_revision_title" => $nodeTitleIntfId, "node_revision_permissions" => $_POST['nodePermissions'], "node_revision_relationships" => $_POST['nodeRelationships'], "node_revision_source" => $nodeSourceIntfId, "node_revision_sort_title" => $nodeSortTitleIntfId, "node_revision_description" => $nodeDescriptionIntfId, "node_revision_disambiguation_description" => $nodeDisambiguationDescriptionIntfId, "node_revision_metadata" => $_POST['nodeMetadata'], "node_revision_comment" => $nodeCommentIntfId, "node_revision_short_description" => $nodeShortDescriptionIntfId, "node_revision_universe_status" => $_POST['nodeUniverseStatus'], "node_revision_owner" => $newNodeOwnerId, "node_revision_copyright_flag" => $_POST['nodeCopyrightFlag'], "node_revision_morality_flag" => $_POST['nodeMoralityFlag'], "node_revision_personal_flag" => $_POST['nodePersonalFlag'], "node_revision_data_id" => $addedDataId,
    //TODO
    "node_revision_node_id" => fv('nodeId'), "node_revision_minor_flag" => $_POST['nodeMinorFlag'],);
    ins('node_revision', $newNodeData);
    //				mysql_query($nodeRevisionInsertQuery);
    $nodeRevisionAddedId = mysql_insert_id();
    return $nodeAddedId;
}
function newNodeRevisionExecute()
{
    if (fv('nodeDataUploadFlag')) {
        //					 echo 'Adding data…';
        $tablenamenewdata = "data";
        $next_incrementdata = 0;
        $qShowStatusdata = "SHOW TABLE STATUS LIKE '$tablenamenewdata'";
        $qShowStatusResultdata = mysql_query($qShowStatusdata) or die("Query failed: " . mysql_error() . "<br/>" . $qShowStatusdata);
        $rowdata = mysql_fetch_assoc($qShowStatusResultdata);
        $next_incrementdata = $rowdata['Auto_increment'];
        mysql_query('INSERT INTO `data` (`data_id`, `data_current_revision`) VALUES (NULL, \'' . $next_incrementdata . '\');');
        $addedDataId = mysql_insert_id();
        //					 echo 'data number ' . $addedDataId . 'and data revision number ';
        $fileTempName = $_FILES['uploadeddata']['tmp_name'];
        mysql_query('INSERT INTO `data_revision` (`data_revision_id`, `data_revision_name`, `data_revision_length`, `data_revision_type`, `data_revision_node_id`, `data_revision_md5`, `data_revision_data_id`, `data_revision_node_edit_id`) VALUES (NULL, \'' . $HTTP_POST_FILES['uploadeddata']['name'] . '\', \'' . $HTTP_POST_FILES['uploadeddata']['size'] . '\', \'' . fv('dataType') . '\', \'nodeid\', \'' . md5_file($fileTempName) . '\', \'' . $addedDataId . '\', \'not yet known\');');
        $targetULDirectory = 'weave/data/' . str_replace(0, '0/', str_replace(1, '1/', str_replace(2, '2/', str_replace(3, '3/', str_replace(4, '4/', str_replace(5, '5/', str_replace(6, '6/', str_replace(7, '7/', str_replace(8, '8/', str_replace(9, '9/', mysql_insert_id()))))))))));
        mkdir($targetULDirectory, 0700, true);
        /*					  $ck = mysql_insert_id();
                $subdirs = array();
                
                for ($i = 0;$i < strlen($ck);$i++) $subdirs[] = $ck[$i];
        */
        $addedDataRevisionId = mysql_insert_id();
        //					echo $addedDataRevisionId;
        $targetULDirectory = $targetULDirectory . $addedDataRevisionId . '.wdf';
        //					 echo $targetULDirectory;
        move_uploaded_file($fileTempName, $targetULDirectory);
    } else {
        //					 echo 'not adding data. ';
        
    }
    $tablenamenewnode = "node_revision";
    $next_incrementnode = 0;
    $qShowStatusnode = "SHOW TABLE STATUS LIKE '$tablenamenewnode'";
    $qShowStatusResultnode = mysql_query($qShowStatusnode) or die("Query failed: " . mysql_error() . "<br/>" . $qShowStatusnode);
    $rownode = mysql_fetch_assoc($qShowStatusResultnode);
    $next_incrementnode_revision = $rownode['Auto_increment'];
    mysql_query('UPDATE `node` SET `node_current_revision` = \'' . $next_incrementnode_revision . '\' WHERE `node_id` =' . fv('nodeId') . ' LIMIT 1 ;');
    //INSERT INTO  `node` (	`node_id` , `node_current_revision` ) VALUES (' . fv('nodeId') . ',  \'' . $next_incrementnode_revision . '\');');
    $nodeEditedId = mysql_insert_id();
    newintf($_POST['nodeDisplayTitle']);
    global $newIntfId;
    $nodeDisplayTitleIntfId = $newIntfId;
    newintf($_POST['nodeShortTitle']);
    global $newIntfId;
    $nodeShortTitleIntfId = $newIntfId;
    newintf($_POST['nodeTitle']);
    global $newIntfId;
    $nodeTitleIntfId = $newIntfId;
    newintf($_POST['nodeSource']);
    global $newIntfId;
    $nodeSourceIntfId = $newIntfId;
    newintf($_POST['nodeSortTitle']);
    global $newIntfId;
    $nodeSortTitleIntfId = $newIntfId;
    newintf($_POST['nodeDescription']);
    global $newIntfId;
    $nodeDescriptionIntfId = $newIntfId;
    newintf($_POST['nodeDisambiguationDescription']);
    global $newIntfId;
    $nodeDisambiguationDescriptionIntfId = $newIntfId;
    newintf($_POST['nodeComment']);
    global $newIntfId;
    $nodeCommentIntfId = $newIntfId;
    newintf($_POST['nodeShortDescription']);
    global $newIntfId;
    $nodeShortDescriptionIntfId = $newIntfId;
    $newNodeOwnerId = qry('user', 'user_id', 'user_name', mysql_real_escape_string($_POST['userName']));
    $newNodeData = array("node_revision_type" => $_POST['nodeType'], "node_revision_display_title" => $nodeDisplayTitleIntfId, "node_revision_short_title" => $nodeShortTitleIntfId, "node_revision_title" => $nodeTitleIntfId, "node_revision_permissions" => $_POST['nodePermissions'], "node_revision_relationships" => $_POST['nodeRelationships'], "node_revision_source" => $nodeSourceIntfId, "node_revision_sort_title" => $nodeSortTitleIntfId, "node_revision_description" => $nodeDescriptionIntfId, "node_revision_disambiguation_description" => $nodeDisambiguationDescriptionIntfId, "node_revision_metadata" => $_POST['nodeMetadata'], "node_revision_comment" => $nodeCommentIntfId, "node_revision_short_description" => $nodeShortDescriptionIntfId, "node_revision_universe_status" => $_POST['nodeUniverseStatus'], "node_revision_owner" => $newNodeOwnerId, "node_revision_copyright_flag" => $_POST['nodeCopyrightFlag'], "node_revision_morality_flag" => $_POST['nodeMoralityFlag'], "node_revision_personal_flag" => $_POST['nodePersonalFlag'], "node_revision_data_id" => $addedDataId, "node_revision_node_id" => fv('nodeId'), "node_revision_minor_flag" => $_POST['nodeMinorFlag'], "node_revision_time" => getnow());
    ins('node_revision', $newNodeData);
    $nodeRevisionAddedId = mysql_insert_id();
    $nodeEditedId = fv('nodeId');
    $user=new user(0, '', 0, fv('wvUserName'), 0, '', '', '');
    $user->request_content('user_name',fv('wvUserName'));
    $newnodeeditids=$user->node_edit_ids . itr(1494) . $nodeRevisionAddedId;
    $user->set_variable('node_edit_ids',$newnodeeditids);
    return $nodeEditedId;
}

