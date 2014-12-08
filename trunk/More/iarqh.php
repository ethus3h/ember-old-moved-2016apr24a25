<?php
//Request handler
error_reporting(E_ALL);
ini_set("display_errors", true);
$rq = $_REQUEST['data'];
echo 'Search results for: '.$rq;
$db = new PDO('mysql:host=localhost;dbname=futuqiur_iaidx;charset=utf8', 'futuqiur_iaidx', 'Artemis!');
$query = 'SELECT * FROM `data` WHERE `filename` LIKE \'%'.$rq.'%\' LIMIT 10';
#echo 'Running query:'.$query;
$results = $db->query($query);
$resultListEntryA= '<ul><li><a href="'; # url
$resultListEntryB= '"><b>'; #title
$resultListEntryC = '</b><br>'; # url
$resultListEntryD = '<br><i>SHA: '; # SHA
$resultListEntryE = '</i></a></li></ul>';
foreach($results as $res) {
	echo $resultListEntryA;
	echo $res['url'];
	echo $resultListEntryB;
	echo $res['filename'];
	echo $resultListEntryC;
	echo $res['url'];
	echo $resultListEntryD;
	echo $res['sha'];
	echo $resultListEntryE;
}
?>
