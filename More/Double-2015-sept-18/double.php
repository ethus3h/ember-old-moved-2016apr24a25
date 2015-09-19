<?php
echo '<!DOCTYPE html><html style="height:100%;"><head><title>"' . $_REQUEST['s'] . '" — Search Results</title></head><body style="height:100%; margin: 0px;">';
echo '<div style="height:100%;">';
echo '<iframe src="' . 'http://goosegoosego.com/search?q=' . $_REQUEST['s'] . '" style="height:60%;width:100%;border:0px;display:block;"></iframe>';
echo '<iframe src="' . 'https://www.bing.com/search?q=' . $_REQUEST['s'] . '" style="height:40%;width:100%;border:0px;display:block;"></iframe>';
echo '</div></body></html>';
?>