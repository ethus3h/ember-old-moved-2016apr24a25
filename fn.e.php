<?php
function getmusic($id){
    global $dataDirectory;
$musicdata= file_get_contents($dataDirectory . '/s/music/r/'.$id.'.html');

#reencode to utf8 if necessary

if(strpos($musicdata,'ISO-8859-1')){
$musicdata=str_replace("ISO-8859-1","UTF-8",iconv("ISO-8859-1","UTF-8",$musicdata));
}

#clean up html mess
$musicdata=str_replace('<html>','',$musicdata);
$musicdata=str_replace('</html>','',$musicdata);
$musicdata=str_replace('<head>','',$musicdata);
$musicdata=str_replace('</head>','',$musicdata);
$musicdata=str_replace('<body>','',$musicdata);
$musicdata=str_replace('</body>','',$musicdata);
$musicdata=str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">','',$musicdata);
$musicdata=str_replace('<meta content="text/html; charset=UTF-8" http-equiv="content-type">','',$musicdata);
$musicdata = preg_replace("/<title(.*)<\/title>/iUs", "", $musicdata);
//$musicdata = preg_replace('/<a target="_blank" href="http:\/\/archive.org\/download\/(.*)"flac">&#128266;<\/a>/', '<a target="_blank" href="http://archive.org/download/' . '$1' . 'mp3">&#128266;</a>', $musicdata);

$musicdata = preg_replace('/<a href="http:\/\/archive.org\/download\/(.*)\/(.*).flac">/', '<audio src="http://archive.org/download/$1/$2.mp3" preload="none" class="audioplayer">Could not start audio player. Does your browser support it?</audio><a href="http://archive.org/download/$1/$2.flac">', $musicdata);
$musicdata=str_replace('&#128266;</a>','Lossless…</a>',$musicdata);



$musicdata=str_replace('cellpadding="2" cellspacing="2"','',$musicdata);

$musicdata= str_replace('AnoeyFuturamerlincom','anoeyfuturamerlincommedium,AnoeyFuturamerlincom',str_replace('<img',' <img',str_replace('src="a/','class="mlefti" src="d/s/music/r/a/',str_replace('href="a/','class="mlefti" href="d/s/music/r/a/',str_replace('href="../','href="d/s/music/',str_replace('href=','target="_blank" href=',str_replace('</h1>','</h2>',str_replace('<h1>','<h2>',$musicdata))))))));
return $musicdata;
}
function e($content,$pageVersionDirect)
{
    global $websiteName;
    global $cssFile;
    global $serverUrl;
    global $pageClass;
    if ($pageClass == 'w') {
        $tbl = '';
    } else {
        $tbl = '<table border="0" cellpadding="24" width="100%"><tbody><tr><td><br><h1>';
    }
    $univNeedles = array('href="' . $serverUrl, "\t", "\n", "\r", "  ", "  ", "  ", "> <", '@p1@', '@p2@', '@p3@', '@p4@', '@cssFile@', '@websiteName@', '@tbl@', '@l@', '@n@', 'https://futuramerlincom.fatcow.com','@greenhead@','@sylfan2@','@bonnou@','@bonnousuper@','@fullmoon@','@yy@','@sumquiaestis@','@128@','@flautrock@','@taito@','@itfaen@','.css";</style></title>');



    $univHaystacks = array('href="', " ", " ", " ", " ", " ", " ", '><', res('1.d' . $pageVersionDirect), res('2.d' . $pageVersionDirect), res('3.d' . $pageVersionDirect), res('4.d' . $pageVersionDirect), $cssFile, $websiteName, $tbl, '<a href="r.php?', '">', 'http://localhost','<div class="greenpage"></div><div class="fh"><a href="/" id="tl"><i>futuramerlin</i></a></div>',getmusic('sylfan2'),getmusic('bonnou'),getmusic('bonnousuper'),getmusic('fullmoon'),getmusic('yy'),getmusic('sumquiaestis'),getmusic('128'),getmusic('flautrock'),getmusic('taito'),getmusic('itfaen'),'.css";</style>');


    echo trim(str_replace("\n",' ',str_replace($univNeedles, $univHaystacks, $content)));
}

?>
