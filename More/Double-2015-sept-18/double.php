<?php
echo '<!DOCTYPE html><html style="height:100%;"><head><title>"' . $_REQUEST['s'] . '" — Search Results</title></head><body style="height:100%; margin: 0px;overflow-y:hidden;">';
echo '<div style="height:100%;">';
echo '<iframe src="' . 'http://goosegoosego.com/search?q=' . $_REQUEST['s'] . '" style="width:60%;height:100%;border:0px;"></iframe>';
echo '<iframe src="' . 'https://www.bing.com/search?q=' . $_REQUEST['s'] . '" style="width:40%;height:100%;border:0px;"></iframe>';
echo '</div></body></html>';
?>