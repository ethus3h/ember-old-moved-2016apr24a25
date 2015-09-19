<?php
echo '<!DOCTYPE html><html><head><title>"' . $_REQUEST['s'] . '" — Search Results</title></head><body style="height:100%;">';
echo '<div style="height:100%;">';
echo '<iframe src="' . 'http://goosegoosego.com/search?q=' . $_REQUEST['s'] . '" style="height:70%;width:100%;"></iframe>';
echo '<iframe src="' . 'https://www.bing.com/search?q=' . $_REQUEST['s'] . '" style="height:30%;width:100%;"></iframe>';
echo '</div></body></html>';
?>