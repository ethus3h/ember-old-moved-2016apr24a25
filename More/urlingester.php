<?php
//Url ingester
error_reporting(E_ALL);
ini_set("display_errors", true);
$rq = $_REQUEST['urls'];
#echo 'Ingesting urls: '.$rq;
$db = new PDO('mysql:host=localhost;dbname=futuqiur_iaidx;charset=utf8', 'futuqiur_iaidx', 'Artemis!');
#next line from http://stackoverflow.com/questions/1462720/iterate-over-each-line-in-a-string-in-php
foreach(preg_split("/((\r?\n)|(\r\n?))/", $rq) as $line){
#echo 'Line: '.$line;
$la = explode(';',$line);
$url = $la[0];
$filename = $la[1];
$sha = $la[2];
$date = $la[3];
$urlsha = hash('sha512',$la[0].$la[3].$sha);
$urly = str_replace('-',' ',str_replace('.',' ',str_replace('/', ' ', $filename)));
$query = 'INSERT INTO `data` (`id`, `url`, `text`, `sha`, `filename`, `urlsha`, `date`, `urly`) VALUES (NULL, \''.$url.'\', \'\', \''.$sha.'\', \''.$filename.'\', \''.$urlsha.'\', \''.$date.'\', \''.$urly.'\');';
#echo "\n".'Running query:'.$query;
$db->query($query);

#echo 'Finished query';
}
echo 'Done ingesting urls.';
?>
