<?php
//Request handler
error_reporting(E_ALL);
ini_set("display_errors", true);
$rq = $_REQUEST['data'];
$limit = $_REQUEST['limit'];
$db = new PDO('mysql:host=localhost;dbname=futuqiur_iaidx;charset=utf8', 'futuqiur_iaidx', 'Artemis!');
#$query = 'SELECT * FROM `data` WHERE `filename` LIKE \'%'.$rq.'%\' LIMIT 10';
#$query = 'SELECT * FROM `data` WHERE `filename` LIKE \'%'.$rq.'%\' LIMIT '.$limit;
$query = 'SELECT * FROM `data` WHERE MATCH(`filename`) AGAINST(\''.$rq.'\') LIMIT '.$limit;
echo 'Query:'.$query;
echo 'Search results for: '.$rq;
$results = $db->query($query);
$resultListEntryA= '<ul><li><a href="'; # url
$resultListEntryB= '"><b>'; #title
$resultListEntryC = '</b><br>'; # url
$resultListEntryD = '<br><i>SHA: '; # SHA
$resultListEntryE = '</i><br><i>Date: '; # SHA
$resultListEntryF = '</i></a></li></ul>';
foreach($results as $res) {
	echo $resultListEntryA;
	echo $res['url'];
	echo $resultListEntryB;
	echo substr($res['filename'],0,128).'...';
	echo $resultListEntryC;
	echo substr($res['url'],0,128).'...';
	echo $resultListEntryD;
	echo $res['sha'];
	echo $resultListEntryE;
	echo $res['date'];
	echo $resultListEntryF;
}
?>
