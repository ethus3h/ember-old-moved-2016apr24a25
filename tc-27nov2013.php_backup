<?php
#Testcase 27 Nov. 2013.
function get_url($url)
{
        echo "\n\n".$url."\n\n";
        $crl = curl_init();
        $timeout = 30;
        curl_setopt ($crl, CURLOPT_URL,$url);
        curl_setopt ($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($crl, CURLOPT_BINARYTRANSFER, true);
        curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ret = curl_exec($crl);
        curl_close($crl);
        return $ret;
}
echo "Hello World!\n";
echo get_url('test');
#echo gzdecode(get_url('https://archive.org/download/AMJ_BarrelData_5809_3428631d-9e0c-497c-9622-6d2125bfe318.2013-11-27-22-03-17-540989-_E/URLs.lst'));
?>