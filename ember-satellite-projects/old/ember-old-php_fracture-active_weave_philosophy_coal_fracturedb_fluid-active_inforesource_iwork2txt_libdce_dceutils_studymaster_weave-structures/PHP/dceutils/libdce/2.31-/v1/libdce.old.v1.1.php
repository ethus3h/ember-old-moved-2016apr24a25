<?php
//libdce. Version 1.1, 26 December 2012 (after midnight of 25 December 2012). This currently provides DCE 3.0a read support only.
//#####################################
//Translation data tables
$dce_versions = array('', '3_0a', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',);
//VERSION 3.0a
$dce3_0a_core = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ' ', '!', '"', '#', '$', '%', '&', '\'', '(', ')', '*', '+', ',', '‐', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':', ';', '<', '=', '>', '?', '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '[', '\'', "]", "^", "_", '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'I', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '{', '|', '}', '~', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',);
//#####################################
//Functions (dce_convert and get_dce_version are the most useful ones)
function dce_convert($data, $input_format, $output_format)
{
    //Ideally how this works: Convert the input into an array of Dc IDs, and then write that out using the selected translator.
    /*
    Input formats: dce, unicode, cdce, legacy_cdce
    Output formats: dce, dce_3.0a, unicode, html, cdce, legacy_cdce
    */
    switch ($input_format) {
    case 'dce':
        $hex = strtolower(bin2hex($data));
        if (substr($hex, 0, 12) !== '444345650201') {
            return 'This document is not stored using a supported format.';
            break;
        }
        switch (get_dce_version($data)) {
        case '3_0a':
            //######################################################
            //This is a DCE 3.0a file. It doesn't need to be updated. This case will though!
            /*        global $dce3_0a_core;
            $counter = 14;
            $txt = '';
            while ($counter < strlen($hex)) {
            $txt = $txt . $dce3_0a_core[hexdec(substr($hex, $counter, 2)) ];
            if (substr($hex, $counter, 4) == 'fd03') {
                break;
            }
            $counter = $counter + 2;
            }
            r:
            return $txt;*/
            $data = $data;
            break;

        default:
            return 'This document is not stored using a supported version of DCE.';
        }
        break;

    case 'unicode':
        break;

    case 'cdce':
        break;

    case 'legacy_cdce':
        break;

    default:
        return 'Unknown input format. Available: dce, unicode';
    }
    switch ($output_format) {
    case 'dce':
        dce_convert($data, 'dce', 'dce_3.0a');
        break;

    case 'dce_3.0a':
        break;

    case 'unicode':
        return dce2txt($data);
        break;

    case 'html':
        break;

    case 'cdce':
        break;

    case 'legacy_cdce':
        break;

    default:
        return 'Unknown output format. Available: dce, dce_3.0a, unicode, html';
    }
}
function x2dce($data, $format)
{
}
function dce2x($data, $format)
{
    //$hex=bin2hex($data);
    switch ($format) {
    case 'txt':
        return dce2txt($data);
        break;
    }
}
function get_dce_version($data)
{
    $hex = strtolower(bin2hex($data));
    if (substr($hex, 0, 12) !== '444345650201') {
        return 'This document is not stored using a supported format.';
        break;
    }
    switch (substr($hex, 12, 2)) {
    case 01:
        //This is a DCE 3.0a file
        return '3_0a';
        break;

    default:
        return 'This document is not stored using a supported version of DCE.';
    }
}
function dce2txt($data)
{
    $hex = strtolower(bin2hex($data));
    //    echo '<br>';
    //    echo $hex;
    //    echo '<br>';
    //    echo substr($hex, 12, 2);
    //    echo substr($hex, 0, 12);
    if (substr($hex, 0, 12) !== '444345650201') {
        return 'This document is not stored using a supported format.';
        break;
    }
    switch (get_dce_version($data)) {
    case '3_0a':
        //This is a DCE 3.0a file
        global $dce3_0a_core;
        $counter = 14;
        $txt = '';
        while ($counter < strlen($hex)) {
            $txt = $txt . $dce3_0a_core[hexdec(substr($hex, $counter, 2)) ];
            if (substr($hex, $counter, 4) == 'fd03') {
                break;
            }
            $counter = $counter + 2;
        }
        r:
            return $txt;
            break;

        default:
            return 'This document is not stored using a supported version of DCE.';
        }
    }
    /*    if (substr($hex, 12, 2) == '01') {
        global $dce3_0a_core;
        //    print_r($dce3_0a_core);
        $counter = 14;
        $txt = '';
        while ($counter < strlen($hex)) {
            $txt = $txt . $dce3_0a_core[hexdec(substr($hex, $counter, 2)) ];
            if (substr($hex, $counter, 4) == 'fd03') {
                break;
            }
            //            echo '<br>';
            //            echo $txt;
            //            echo '<br>';
            //            echo substr($hex, $counter, 2);
            $counter = $counter + 2;
        }
        r:
            return $txt;
            //        $html = str_replace($needles, $haystacks);
            
        } else {
            return 'This document is not stored in a supported version of DCE.';
        }
    }*/
    /*function hex2dce($hex)
    {
    $bin=pack("H" . strlen($hex), $hex);
    return $bin;
    }*/
    /*
    function dce2hex($hex)
    {
    $bin=pack("h" . strlen($hex), $hex);
    return $bin;
    }*/
    //Hello World!
    //    echo hex2bin('44434565020101FD8048656C6C6F20576F726C642181FD03');
    //    echo dce_convert(hex2bin('44434565020101FD8048656C6C6F20576F726C642181FD03'), 'dce', 'unicode');
    
