<?php
if(substr($_REQUEST['s'],0,1) == '!') {
	header("Location: http://goosegoosego.com/search?q=" . htmlentities($_REQUEST['s']));
	die();
}
echo '<!DOCTYPE html><html style="height:100%;"><head><title>"' . htmlentities($_REQUEST['s']) . '" — Search Results</title></head><body style="height:100%; margin: 0px;overflow-y:hidden;">';
echo '<div style="height:100%;">';
echo '<iframe src="' . 'http://goosegoosego.com/search?q=' . htmlentities($_REQUEST['s']) . '" style="width:60%;height:100%;border:0px;"></iframe>';
echo '<iframe src="' . 'https://www.bing.com/search?q=' . htmlentities($_REQUEST['s']) . '" style="width:40%;height:100%;border:0px;"></iframe>';
echo '</div></body></html>';
?>