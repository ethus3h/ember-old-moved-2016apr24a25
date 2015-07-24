<?php
echo bin2hex(mb_convert_encoding('Test Test Hello World!','UTF-8','UTF-32'));
echo "\nDoom\n";
echo bin2hex(iconv('Test Test Hello World!','UTF-8','UTF-32'));
?>