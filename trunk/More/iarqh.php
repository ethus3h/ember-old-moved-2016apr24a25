<?php
//Request handler
$result = $_REQUEST['data'];

$resultListEntryA= '<li><a href="'; # url
$resultListEntryB= '"><b>'; #title
$resultListEntryC = '</b><br>'; # url
$resultListEntryD = '<i>SHA: '; # SHA
$resultListEntryE = '</i></li></a>';
foreach($results as $result) {
	$res = $result.explode();
	echo $resultListEntryA;
	echo $res[0];
	echo $resultListEntryB;
	echo $res[0];
	echo $resultListEntryC;
	echo substr($res[0],strrchr($res[0],'/'));
	echo $resultListEntryD;
	echo $res[1];
	echo $resultListEntryE;
	echo 'ENDOFRESULTTHISSHOULDNEVEOCCURINRESULT';		}
?>
