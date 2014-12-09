<?php
//Request handler
error_reporting(E_ALL);
ini_set("display_errors", true);
$rq = $_REQUEST['data'];
$limite = $_REQUEST['limit'];
$lar = explode(',',$limite);
$limit = $lar[0];
$searchtype = 0;
@$searchtype = $lar[1];
$db = new PDO('mysql:host=localhost;dbname=futuqiur_iaidx;charset=utf8', 'futuqiur_iaidx', 'Artemis!');
#$query = 'SELECT * FROM `data` WHERE `filename` LIKE \'%'.$rq.'%\' LIMIT 10';
$query = 'SELECT * FROM `data` WHERE `filename` LIKE \'%'.$rq.'%\' LIMIT '.$limit;
switch($searchtype) {
case 1:
	$query = 'SELECT * FROM `data` WHERE MATCH(`urly`) AGAINST(\''.$rq.'\') LIMIT '.$limit;
	break;
case 2:
	$query = 'SELECT * FROM `data` WHERE `urly` LIKE \'%'.$rq.'%\' LIMIT '.$limit;
	break;
case 3:
	$query = 'SELECT * FROM `data` WHERE MATCH(`filename`) AGAINST(\''.$rq.'\') LIMIT '.$limit;
	break;
}
if($searchtype == 1) {
}
echo 'Query:'.$query;
echo '<br>Search results for: '.$rq;
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
