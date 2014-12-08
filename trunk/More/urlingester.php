<?php
//Url ingester
error_reporting(E_ALL);
ini_set("display_errors", true);
$rq = $_REQUEST['urls'];
echo 'Ingesting urls: '.$rq;
$db = new PDO('mysql:host=localhost;dbname=futuqiur_iaidx;charset=utf8', 'futuqiur_iaidx', 'Artemis!');
$rqn = str_replace($rq,"\r","\n");
$rqnt = str_replace($rq,"\n\n","\n");
foreach(explode("\n",$rqnt) as $line) {
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
