<?php
//This is a normal webpage
if(strlen(res(str_replace('//','/','p/' . $pageClass . '/' . $pageAction . '.s#p3')))!==0)
{
//Version 3 processor
global $dataDirectory;
include($dataDirectory.str_replace('//','/','p/' . $pageClass . '/' . $pageAction . '.s#p3'));
}
else {
//This handles pre-version 3 pages
global $pageVersion;
if ($pageClass) {
    $pageLoc = 'p/' . $pageClass . '/' . $pageAction . '.p2';
} else {
    $pageLoc = 'p/' . $pageAction . '.p2';
}
$pageVersion = '2';
if (strlen($pageLoc) == 0) {
    if ($pageClass) {
        $pageLoc = 'p/' . $pageClass . '/' . $pageAction . '.p';
    } else {
        $pageLoc = 'p/' . $pageAction . '.p';
    }
    $pageVersion = '';
}
e(res($pageLoc),$pageVersion);
}
?>
