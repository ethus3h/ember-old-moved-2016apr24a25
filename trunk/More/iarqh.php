<?php
//Request handler
$rq = $_REQUEST['data'];
echo 'Search results for: $rq';
$db = new PDO('mysql:host=localhost;dbname=futuqiur_iaidx;charset=utf8', 'iaidx', 'Artemis!');
$results = $db->query('SELECT * FROM `data` WHERE `filename` LIKE \'%'.$rq.'%\' LIMIT 10');
$resultListEntryA= '<ul><li><a href="'; # url
$resultListEntryB= '"><b>'; #title
$resultListEntryC = '</b><br>'; # url
$resultListEntryD = '<i>SHA: '; # SHA
$resultListEntryE = '</i></a></li></ul>';
foreach($results as $result) {
	$res = $result.explode();
	echo $resultListEntryA;
	echo $res['url'];
	echo $resultListEntryB;
	echo substr($res['url'],strrchr($res['url'],'/'));
	echo $resultListEntryC;
	echo $res['url'];
	echo $resultListEntryD;
	echo $res['sha'];
	echo $resultListEntryE;
}
?>
