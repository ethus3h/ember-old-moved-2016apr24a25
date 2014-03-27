<?php
error_reporting(E_ALL);
//ini_set("display_errors", 0);
$displayDebugMessages = True;
include('authData.php');
//Important variables
if ($_SERVER["HTTP_HOST"] == '127.0.0.1') {
    //HTTP server
    //default: futuramerlin.com
    //development: 127.0.0.1
    $serverHttp  = '127.0.0.1';
    //Secure server
    //default: futuramerlincom.fatcow.com
    //development: 127.0.0.1
    $serverHttps = '127.0.0.1';
    //Database server
    //default: futuramerlincom.fatcowmysql.com
    //development: 127.0.0.1
    $dbServer    = 'localhost';
    //Uncomment the next two lines for development
    $HttpsWPUrl  = 'http://127.0.0.1';
} else {
    //HTTP server
    //default: futuramerlin.com
    //development: 127.0.0.1
    $serverHttp  = 'futuramerlin.com';
    //Secure server
    //default: futuramerlincom.fatcow.com
    //development: 127.0.0.1
    $serverHttps = 'futuramerlincom.fatcow.com';
    //Database server
    //default: futuramerlincom.fatcowmysql.com
    //development: 127.0.0.1
    $dbServer    = 'localhost';
    $HttpsWPUrl  = 'http://futuramerlin.com';
}
//default: futuramerlin
// $websiteName = 'futuramerlin';
// global $pageClass;
// $pageClass = Rq('c');
// global $pageAction;
// $pageAction = Rq('a');
// global $dataDirectory;
$dataDirectory = 'd/';
//Server URL
if (isset($_SERVER['HTTPS'])) {
    //If it's secure HTTP
    //default: 'https://' . $serverHttps . '/'
    $serverUrl = 'http://' . $serverHttps . '/';
} else {
    //If it's normal HTTP
    //default: 'http://' . $serverHttp . '/'
    $serverUrl = 'http://' . $serverHttp . '/';
}
$dataUrl = '/' . $dataDirectory;
if (strpos($_SERVER['HTTP_USER_AGENT'], 'AppleWebKit')) {
    $cssFile = $dataUrl . 'f.css';
} else {
    $cssFile = $dataUrl . 's.css';
}
$pageVersion = '2';
?>