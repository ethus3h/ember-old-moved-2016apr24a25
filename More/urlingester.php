<?php
//Url ingester
error_reporting(E_ALL);
ini_set("display_errors", true);
$rq = $_REQUEST['urls'];
echo 'Ingesting urls: '.$rq;
$db = new PDO('mysql:host=localhost;dbname=futuqiur_iaidx;charset=utf8', 'futuqiur_iaidx', 'Artemis!');
#next line from http://stackoverflow.com/questions/1462720/iterate-over-each-line-in-a-string-in-php
foreach(preg_split("/((\r?\n)|(\r\n?))/", $rq) as $line){
echo 'Line: '.$line;
$la = explode(';',$line);
$url = $la[0];
$filename = $la[1];
$sha = $la[2];
$query = 'INSERT INTO `data` (`id`, `url`, `text`, `urlp`, `textp`, `sha`, `filename`) VALUES (NULL, \''.$url.'\', \'\', \'\', \'\', \''.$sha.'\', \''.$filename.'\');';
echo 'Running query:'.$query;
$db->query($query);

echo 'Finished query';
}
echo 'Done ingesting urls.';
?>
