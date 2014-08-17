<?php
//CONFIG
//error_reporting(E_ALL);
ini_set("display_errors", 0);
ob_start("ob_gzhandler");
//IMPORTANT VARIABLES
if ($_SERVER["HTTP_HOST"] == '127.0.0.1') {
    //HTTP server
    //default: futuramerlin.com
    //development: 127.0.0.1
    global $serverHttp;
    $serverHttp = '127.0.0.1';
    //Secure server
    //default: futuramerlincom.fatcow.com
    //development: 127.0.0.1
    global $serverHttps;
    $serverHttps = '127.0.0.1';
    //Database server
    //default: futuramerlincom.fatcowmysql.com
    //development: 127.0.0.1
    global $dbServer;
    $dbServer = 'localhost';
    //Database name
    //default: weave_data
    //development: weave
    global $dbName;
    $dbName = 'weave';
    //Database login username
    //default: weave_data
    //development: root
    global $dbUsername;
    $dbUsername = 'root';
    //Database login password
    //default: Kuzmenkotaxservices5
    //development: elen-esar
    global $dbPassword;
    $dbPassword = 'elen-esar';
    //Uncomment the next two lines for development
    global $HttpsWPUrl;
    $HttpsWPUrl = 'http://127.0.0.1';
} else {
    //HTTP server
    //default: futuramerlin.com
    //development: 127.0.0.1
    global $serverHttp;
    $serverHttp = 'futuramerlin.com';
    //Secure server
    //default: futuramerlincom.fatcow.com
    //development: 127.0.0.1
    global $serverHttps;
    $serverHttps = 'futuramerlincom.fatcow.com';
    //Database server
    //default: futuramerlincom.fatcowmysql.com
    //development: 127.0.0.1
    global $dbServer;
    $dbServer = 'localhost';
    //Database name
    //default: weave_data
    //development: weave
    global $dbName;
    $dbName = 'futuqiur_weave_data_db_new';
    //Database login username
    //default: weave_data
    //development: root
    global $dbUsername;
    $dbUsername = 'futuqiur_weave';
    //Database login password
    //default: Kuzmenkotaxservices5
    //development: elen-esar
    global $dbPassword;
    $dbPassword = 'KuTax5.';
    global $HttpsWPUrl;
    $HttpsWPUrl = 'http://futuramerlin.com';
}
global $websiteName;
//default: futuramerlin
$websiteName = 'futuramerlin';
global $pageClass;
$pageClass = $_REQUEST['c'];
global $pageAction;
$pageAction = $_REQUEST['a'];
global $dataDirectory;
$dataDirectory = 'd/';
//Server URL
global $serverUrl;
if (isset($_SERVER['HTTPS'])) {
    //If it's secure HTTP
    //default: 'https://' . $serverHttps . '/'
    $serverUrl = 'http://' . $serverHttps . '/';
} else {
    //If it's normal HTTP
    //default: 'http://' . $serverHttp . '/'
    $serverUrl = 'http://' . $serverHttp . '/';
}
global $dataUrl;
$dataUrl = '/' . $dataDirectory;
global $cssFile;
if (strpos($_SERVER['HTTP_USER_AGENT'], 'AppleWebKit')) {
    $cssFile = $dataUrl . 'f.css';
} else {
    $cssFile = $dataUrl . 's.css';
}
global $pageVersion;
$pageVersion = '2';
include('active.php');
?>
