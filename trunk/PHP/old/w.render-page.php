<?php
/* WRITE PAGE */
header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: max-age=6000");
if (isset($error)) {
    $login = '';
    $pageTitle = itr(23);
    $pageBody = itr(49) . itr($error);
} else {
}
/* this does nothing at the moment
        if (isset($nodeId))
        {
        $nodeArray = explode_escaped('|', $arrayNodeData);
        list($nodeTitle, $nodeRevisionNumber, $nodeDescription, $nodeDisambiguationDescription, $nodeRelationships, $nodeEditTime, $nodeCreationTime, $nodeType, $nodeComment, $nodeShortDescription, $nodeNSMetadata, $nodePermissions) = $nodeArray;
        $nodeRelParse = str_replace('/', '; ', $nodeRelationships);
        
        if ($nodeDisambiguationDescription == '')
        {
        $nodeDDString = $nodeDisambiguationDescription;
        }
        else
        {
        $nodeDDString = '(' . $$nodeDisambiguationDescription . ')';
        }
        
        if (strlen($nodeTitle) > 64)
        {
        $titleTrimmed = substr($nodeTitle, 0, 63) . '� ' . $nodeDDString;
        }
        else
        {
        $titleTrimmed = $nodeTitle . $nodeDDString . $titleAttr . itr(24);
        echo $titleTrimmed;
        }
        $pageTitle = $titleTrimmed . $nodeDDString;
        }
        else
        {
        
        #No node id is specified
        $nothing = 'No node id specified: Error';
        }
*/
// Echo html bits
e(res('1.d2'));
e(str_replace(itr(772), '', str_replace(itr(773), '', $pageTitle)) . $titleAttr);
e(res('2.d2'));
e(res('3.d2'));
itf(25);
if ($login == 1) {
    /*
            
            39: ">
            
            42: <form target="weave.php" action="post"><input type="hidden" name="action" value="
            
            43: "><input type="hidden" name="
            
            44: " value="
            
            45: "><input type="submit"></form>
            
            55: home
            New inter
            57: <input type="hidden" name="userName" value="
            
            58: "><input type="password" name="password" value="
    */
    itf(42);
    e('1');
    itf(39);
    itf(286);
    e(fv('wvSession'));
    /* This contains the logo link */
    itf(287);
} else {
    echo buildLink('1', '', itr(288));
}
if ($login == 1) {
    itf(28);
    echo buildLink('15', '', itr(692));
    itf(66);
    echo buildLink('11', '', itr(84));
} else {
    itf(29);
    echo buildLink('3', 'nodeId=' . fv('nodeId') . '&', itr(30));
    itf(66);
    echo buildLink('4', '', itr(31));
    itf(66);
    echo buildLink('11', '', itr(84));
}
//Breadcrumb navigation
$wvActionDispName = itr(qry('operation', 'operation_disp_name', 'operation_id', fv('a')));
$breadSeparator = itr(1135);
if(!isset($nodeRevId)) {
	$nodeRevId = null;
}
if(!isset($disambigStr)) {
	$disambigStr = null;
}
$nodeBCTitle = $nodeId . itr(1150) . c(shorten(itr(qry('node_revision', 'node_revision_title', 'node_revision_id', $nodeRevId))) . $disambigStr);
if (!strlen(fv('nodeId')) > 0) {
    $nodeNameTag = "";
} else {
    $nodeNameTag = itr(1136) . buildLink(6, '&nodeId=' . fv('nodeId') . '&', $nodeBCTitle);
}
if ($wvActionId == 'nodeView') {
    $actionlinkid = '19';
} else {
    $actionlinkid = fv('a');
}
e(str_replace('&a=6&locale', '&a=19&locale', itr(1139) . buildLink(1, '', itr(1137)) . itr(1158) . $breadSeparator . itr(1158) . buildLink($actionlinkid, '', $wvActionDispName) . $nodeNameTag));
if(!isset($pageMenu)) {
	$pageMenu = null;
}
echo $pageMenu;
itf(33);
echo $pageTitle;
itf(34);
echo $pageBody;
e(res('4.d2'));
//Execute script
//echo 'passed test';
/* END PAGE */
?>
