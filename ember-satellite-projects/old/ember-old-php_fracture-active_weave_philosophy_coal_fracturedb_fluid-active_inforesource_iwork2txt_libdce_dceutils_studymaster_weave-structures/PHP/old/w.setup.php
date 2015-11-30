<?php
//Set output character set
header('Content-type: text/html; charset=utf-8');
//session_start();
if (isset($_REQUEST['wvSession'])) {
    session_id($_REQUEST['wvSession']);
} else {
}
global $wvUserName;
if(isset($_SESSION['wvUserName'])) {
	$wvUserName = $_SESSION['wvUserName'];
}
global $wvUserPassword;
if(isset($_SESSION['wvUserPassword'])) {
	$wvUserPassword = $_SESSION['wvUserPassword'];
}
if(isset($wvActionId)) {
	if ($wvActionId == 'wvLogInExecute') {
		$_SESSION['wvUserName'] = $_POST['wvUserName'];
		$_SESSION['wvUserPassword'] = $_POST['wvUserPassword'];
	} else {
	}
}
?>
