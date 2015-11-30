<?php
/*libdce. Version 1.42, 28 December 2012.

Changes in this version:
* Converted some echo tests to commented log requests.
* Added this changelog.
* Continued work on DCE 3.01a support.
* Added wrappers for a number of old translators
* Added translators for hex-encoded DCE
* Renamed legacy CDCE HTML translators
* Added placeholder translators for standard HTML (whether or not I'll do anything with these remains to be seen)
* Added Base64-encoded UTF-8 translators and supporting functions

Data format support:

Format                                   | Format code    | Read  | Write
=========================================+================+=======+======
CDCE                                     | cdce           |       |      
CDCE (legacy)***                         | legacy_cdce    |  X    |      
CDCE (legacy, strict)*****               | cdce_lstrict   |  X    | N/A  
DCE*                                     | dce            |  X    | X    
DCE 3.0a**                               | 3_0a           | (X)   | X    
DCE 3.0a (old translator)******          | 3_0a_old       |  X    | N/A  
dce2txt*******                           | dce2txt        |  X    | N/A  
dce2hex*******                           | dce2hex        |  X    | N/A  
hex2dce*******                           | hex2dce        |  X    | N/A  
hex_dce                                  | hex_dce        |  X    | X    
DCE 3.0a (hex-encoded)**                 | hex_3_0a       | (X)   | X    
DCE 3.01a**;****                         | 3_01a          | (X)   |      
DCE 3.01a (hex-encoded)**;****           | hex_3_01a      |  X    | X    
Dc ID list (ASCII)***                    | dc             |  X    | X    
UTF-8***                                 | utf8           |  X    | X    
UTF-32***                                | utf32          |  X    | X    
UTF-8 (Base64-encoded)                   | utf8_base64    |  X    | X    
UTF-8 (Base64-encoded, ASCII Dc ID list) | utf8_dc64      |  X    | X    
HTML (legacy CDCE output)****            | html_l         |       | X    
HTML (snippet) (legacy CDCE output)****  | html_snippet_l |       | X    
HTML                                     | html           |       |      
HTML (snippet)                           | html_snippet   |       |      




*       …   Automatically detects DCE version when reading, and writes
            to the latest DCE version that is well supported.

**      …   Since this is a DCE format, simply enter DCE as the format
            when reading. Note that writing to old versions of DCE may
            not be lossless.

***     …   Reading/writing these formats may not be lossless with DCE
            3.0a.

****    …   It'll try  but it  probably won't work or won't work well.
            Note that the HTML  translators only work with legacy CDCE
            input and are very buggy (alpha quality).

*****   …   Note that  cdce_lstrict is not actually a  separate format
            from legacy_cdce;  rather, it is an  alias of  legacy_cdce
            that  instructs the  parser to halt upon  reaching  an in-
            correctly  structured  CDCE  sequence (by  default it will
            attempt to recover from the error).

******  …   This is an early version of the DCE 3.0a translator, not a
            separate format. Leave the  output format blank when using
            this  translator,  since the same code  handles both input
            and  output. This is not tested or  maintained, and may or
            may not work properly.

******* …   These are miscellaneous outdated translators, not separate
            formats. Leave the  output format  blank when  using them,
            since the same code  handles both input  and output. These
            are not  tested  or maintained,  and  may or may  not work
            properly.




This script contains code from Weave.
*/
ini_set("display_errors", 0);
include ('wf.explode_escaped.php');
include ('libdce_data.php');
//#####################################
//Functions (dce_convert and get_dce_version are the most useful ones)
$log = '';
function dce_convert($data, $input_format, $output_format = "none", $output_log = false)
{
    if ($input_format == "legacy_cdce") {
        $strict = false;
    } else {
        if ($input_format == "cdce_lstrict") {
            $strict = true;
        }
    }
    $html_opening = '<html><head><title></title></head><body>';
    $html_closing = '</body></html>';
    $onestep = false;
    $final = '';
    //echo $output_format;
    /*  if ($output_format == 'utf32') {
        header('Content-type: text/plain; charset=utf-32');
    } else {
        header('Content-type: text/plain');
    } */
    //Ideally how this works: Convert the input into an array of Dc IDs, and then write that out using the selected translator.
    switch ($input_format) {
    case 'dc':
        $dc = $data;
        break;

    case 'utf8_base64':
        $dc = dce_convert(base64_decode($data), 'utf8', 'dc');
        break;

    case 'utf8_dc64':
        $dc = dce_convert(base64_3_01a_to_unicode($data), 'utf8', 'dc');
        break;

    case 'hex_dce':
        $dc = dce_convert(hex2bin($data), 'dce', 'dc');
        break;

    case 'hex_3_0a':
        $dc = dce_convert(hex2bin($data), '3_0a', 'dc');
        break;

    case 'hex_3_01a':
        $dc = dce_convert(hex2bin($data), '3_01a', 'dc');
        break;

    case 'dce2txt':
        return dce2txt($data);
        break;

    case 'dce2hex':
        return dce2hex($data);
        break;

    case 'hex2dce':
        return hex2dce($data);
        break;

    case '3_0a_old':
        //This next block is the old decoder.
        //######################################################
        //This is a DCE 3.0a file. It doesn't need to be updated. This case will though!
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
            $data = $data;
            break;

        case 'dce':
            $hex = strtolower(bin2hex($data));
            if (substr($hex, 0, 12) !== '444345650201') {
                return 'This document is not stored using a supported format.';
                break;
            }
            switch (get_dce_version($data)) {
            case '3_0a':
                //######################################################
                //This is a DCE 3.0a file.
                global $DcMap_dce3_0a_Core;
                $counter = 14;
                $txt = '';
                while ($counter < strlen($hex)) {
                    $txt = $txt . $DcMap_dce3_0a_Core[strtoupper(substr($hex, $counter, 2)) ] . ',';
                    //echo $txt;
                    //$txt=$txt;
                    if (substr($hex, $counter, 4) == 'fd03') {
                        break;
                    }
                    $counter = $counter + 2;
                }
                //echo $txt;
                //echo '<br>';
                //echo '<br>';
                $txt = substr($txt, 3, (strlen($txt) - 6));
                //echo $txt;echo '<br>';echo '<br>';
                // return $txt;
                $dc = $txt;
                break;

            case '3_01a':
                //######################################################
                //This is a DCE 3.01a file.
                global $DcMap_dce3_01a_Core;
                global $DcMap_dce3_01a_Variant_Selectors;
                global $DcMap_dce3_01a_Semantic_Records;
                global $DcMap_dce3_01a_Mathematics;
                global $DcMap_dce3_01a_Whitespace_and_Punctuation;
                global $Dc_to_Base64;
                $counter = 14;
                $txt = '';
                $state = 'Core';
                $varAppend = 'DcMap_dce3_01a_';
                while ($counter < strlen($hex)) {
                    $action_last = $action;
                    $data_array_name = $varAppend . $state;
                    $data_array = $$data_array_name;
                    switch ($state) {
                    case 'Core':
                        if (substr($DcMap_dce3_01a_Core[strtoupper(substr($hex, $counter, 2)) ], 0, 1) == '>') {
                            //Switch states
                            $state = substr($DcMap_dce3_01a_Core[strtoupper(substr($hex, $counter, 2)) ], 1, strlen($DcMap_dce3_01a_Core[strtoupper(substr($hex, $counter, 2)) ]) - 1);
                            $action = 'State switch out of Core) <br>';
                            break;
                        } else {
                            ////////////////////////////////This is of no concern. Embedded Unicode should be preserved in its embedded state until being converted *out* of Dc. D'oh.
                            /*  if ($DcMap_dce3_01a_Core[strtoupper(substr($hex, $counter, 2)) ] == '191') { //Unicode embedded
                            while (substr($hex, $counter + 2, 2) != 'C4') {
                                $counter = $counter + 2;
                               // $unicode_decode = $unicode_decode . $Dc_to_Base64[$DcMap_dce3_01a_Core[substr($hex, $counter, 2) ]];
                            }
                            //  $unicode = base64_decode($unicode_decode);
                            // $txt = $txt . '￼' . $unicode . '￼';
                            } else */
                            $append = $DcMap_dce3_01a_Core[strtoupper(substr($hex, $counter, 2)) ] . ',';
                            $action = 'Append from Core) <br>';
                        }
                        break;

                    case 'Variant_Selectors':
                        if ((substr($hex, $counter, 2) == 'fd') || (substr($hex, $counter, 2) == 'fe')) {
                            $state = 'Core';
                            $action = 'State switch out of Variant_Selectors) <br>';
                        } else {
                            $DcMap_dce3_01a_Variant_Selectors[strtoupper(substr($hex, $counter, 2)) ] . ',';
                            $action = 'Append from Variant_Selectors) <br>';
                        }
                        break;

                    case 'Semantic_Records':
                        if ((substr($hex, $counter, 2) == 'fd') || (substr($hex, $counter, 2) == 'fe')) {
                            $state = 'Core';
                            $action = 'State switch out of Semantic_Records) <br>';
                        } else {
                            $append = $DcMap_dce3_01a_Semantic_Records[strtoupper(substr($hex, $counter, 2)) ] . ',';
                            $action = 'Append from Semantic_Records) <br>';
                        }
                        break;

                    case 'Mathematics':
                        if ((substr($hex, $counter, 2) == 'fd') || (substr($hex, $counter, 2) == 'fe')) {
                            $state = 'Core';
                            $action = 'State switch out of Mathematics) <br>';
                        } else {
                            $append = $DcMap_dce3_01a_Mathematics[strtoupper(substr($hex, $counter, 2)) ] . ',';
                            $action = 'Append from Mathematics) . <br>';
                        }
                        break;

                    case 'Whitespace_and_Punctuation':
                        if ((substr($hex, $counter, 2) == 'fd') || (substr($hex, $counter, 2) == 'fe')) {
                            $state = 'Core';
                            $action = 'State switch out of Whitespace_and_Punctuation) <br>';
                        } else {
                            $append = $DcMap_dce3_01a_Whitespace_and_Punctuation[strtoupper(substr($hex, $counter, 2)) ] . ',';
                            $action = 'Append from Whitespace_and_Punctuation) <br>';
                        }
                        break;
                    }
                    //echo $txt;
                    //$txt=$txt;
                    //echo '<br>' . substr($hex, $counter + 2, 4) . '<br>' . $txt . '<br>';
                    if (substr($hex, $counter + 2, 4) == 'fd03') {
                        $action = '<br>Halting. <br>';
                        break;
                    }
                    $txt = $txt . $append;
                    echo '<br><font color="red">' . ((($counter - 14) / 2) + 1) . ': ' . substr($hex, $counter, 2) . '</font> → ' . $txt . ' (Action: ' . $action;
                    $counter = $counter + 2;
                }
                //echo $txt;
                //echo '<br>';
                //echo '<br>';
                $txt = substr($txt, 3, (strlen($txt) - 4));
                //echo '<br>';echo $txt;echo '<br>';
                //echo $txt;echo '<br>';echo '<br>';
                // return $txt;
                $dc = $txt;
                break;

            default:
                return 'This document is not stored using a supported version of DCE.';
                break;
            }
            break;

        case 'utf32':
            $unicode = iconv('UTF-32BE', 'UTF-8', $data);
            return dce_convert($unicode, 'utf8', $output_format);
            break;

        case 'utf8':
            ini_set("display_errors", 0);
            $unicode = iconv('UTF-8', 'UTF-32BE', $data);
            $hex = bin2hex($unicode);
            //echo $hex;
            global $DcMap_Unicode_Lossy;
            $counter = 0;
            $txt = '';
            while ($counter < strlen($hex)) {
                // $byte=strtoupper(ltrim(substr($hex, $counter, 8),'0'));
                // echo'<br>';echo $byte;echo'<br>';
                //echo $txt;
                //echo '<br>';
                //echo $counter;
                if (strlen($DcMap_Unicode_Lossy[strtoupper(ltrim(substr($hex, $counter, 8), '0')) ]) != 0) {
                    $append = $DcMap_Unicode_Lossy[strtoupper(ltrim(substr($hex, $counter, 8), '0')) ] . ',';
                } else {
                    $append = '';
                };
                // $txt = $txt . $DcMap_Unicode_Lossy[strtoupper(ltrim(substr($hex, $counter, 8), '0')) ] . ',';
                $txt = $txt . $append;
                //echo $txt;
                //$txt=$txt;
                //if (substr($hex, $counter, 4) == 'fd03') {
                //  break;
                //   }
                $counter = $counter + 8;
            }
            //echo $txt;
            //echo '<br>';
            //echo '<br>';
            //$txt = substr($txt, 3, (strlen($txt) - 6));
            //echo $txt;echo '<br>';echo '<br>';
            // return $txt;
            $dc = $txt;
            break;

        case 'cdce':
            break;

        case 'legacy_cdce':
            // global $output_format;
            // global $onestep;
            // global $finished;
            //  global $html_opening;
            // global $html_closing;
            switch ($output_format) {
            case 'html_l':
                //  global $onestep;
                //  global $finished;
                $onestep = 1;
                $finished = $html_opening . legacy_cdce_to_html_snippet($data) . $html_closing;
                //echo $onestep;
                //echo $finished;
                //echo $html_opening;
                break;

            case 'html_snippet_l':
                //   global $onestep;
                //   global $finished;
                //   global $html_opening;
                //   global $html_closing;
                $onestep = 1;
                $finished = legacy_cdce_to_html_snippet($data);
                break;

            default:
                $dc = legacy_cdce_parse($data, $strict);
                break;
            }
        case 'cdce_lstrict':
            // global $output_format;
            // global $onestep;
            // global $finished;
            //  global $html_opening;
            // global $html_closing;
            switch ($output_format) {
            case 'html_l':
                //  global $onestep;
                //  global $finished;
                $onestep = 1;
                $finished = $html_opening . legacy_cdce_to_html_snippet($data) . $html_closing;
                return $finished;
                //echo $onestep;
                //echo $finished;
                //echo $html_opening;
                break;

            case 'html_snippet_l':
                //   global $onestep;
                //   global $finished;
                //   global $html_opening;
                //   global $html_closing;
                $onestep = 1;
                $finished = legacy_cdce_to_html_snippet($data);
                return $finished;
                break;

            default:
                $dc = legacy_cdce_parse($data, $strict);
                break;
            }
            break;

        default:
            $dc = '';
            //echo $dc;
            return 'Unknown input format: ' . $input_format . '. Available: dc, dce, 3_0a, 3_01a, legacy_cdce, cdce_lstrict, utf8, utf32';
            break;
        }
        //global $onestep;
        //$onestep="meuou";
        //echo $onestep;
        if ($onestep == "1") {
            return $finished;
            //echo 'test';
            //echo 'doom';
            
        } else {
            //  echo 'doom 2';
            $dc = preg_replace('/,\Z/', '', $dc);
            $dc = str_replace(',,', ',0,', $dc);
            //      echo '<br>';
            //      echo $dc;
            //      echo '<br>';
            $dc_array = explode_escaped(',', $dc);
            //    print_r($dc_array);
            //     echo '<br>';
            $dc_size = count($dc_array);
            //   echo $dc_size;
            //    echo '<br>';
            switch ($output_format) {
            case 'none':
                break;

            case 'utf8_base64':
                return base64_encode(dce_convert($dc, 'dc', 'utf8'));
                break;

            case 'utf8_dc64':
                if ($input_format == 'utf8') {
                    return unicode_to_base64_3_01a($data);
                } else {
                    if ($input_format == 'ucs32') {
                        return iconv('UTF-32BE', 'UTF-8', unicode_to_base64_3_01a($data));
                    } else {
                        return unicode_to_base64_3_01a(dce_convert($dc), 'dc', 'utf8');
                    }
                }
                break;

            case 'hex_dce':
                return bin2hex(dce_convert($dc, 'dc', 'dce'));
                break;

            case 'hex_3_0a':
                return bin2hex(dce_convert($dc, 'dc', '3_0a'));
                break;

            case 'hex_3_01a':
                return bin2hex(dce_convert($dc, 'dc', '3_01a'));
                break;

            case 'dc':
                return $dc;
                break;

            case 'dce':
                return dce_convert($dc, 'dc', '3_0a');
                break;

            case '3_0a':
                //echo $dc;
                global $DcMapSend_dce3_0a;
                $counter = 0;
                $txt = '44434565020101fd';
                //    echo '<br>';
                while ($counter < $dc_size) {
                    $txt = $txt . strtolower($DcMapSend_dce3_0a[$dc_array[$counter]]);
                    //    echo $counter;
                    //    echo ': ';
                    //    echo $txt;
                    //$txt=$txt;
                    //    echo '<br>';
                    $counter++;
                }
                $txt = $txt . 'fd03';
                //echo $txt;
                //$txt = substr($txt, 0, (strlen($txt) - 1));
                return hex2bin($txt);
                //    $dc = $txt;
                break;

            case '3_01a':
                /*   global $DcMapSend_dce3_01a;
                $counter = 0;
                $txt = '44434565020102fd';
                //    echo '<br>';
                while ($counter < $dc_size) {
                $txt = $txt . strtolower($DcMapSend_dce3_01a[$dc_array[$counter]]);
                //    echo $counter;
                //    echo ': ';
                //    echo $txt;
                //$txt=$txt;
                //    echo '<br>';
                $counter++;
                }
                $txt = $txt . 'fd03';
                //$txt = substr($txt, 0, (strlen($txt) - 1));
                
                //    $dc = $txt;*/
                return hex2bin($txt);
                break;

            case 'utf8':
                global $DcMapSend_Unicode;
                $counter = 0;
                $txt = '';
                // echo '<br>';
                while ($counter < $dc_size) {
                    $txt = $txt . str_pad(strtolower($DcMapSend_Unicode[$dc_array[$counter]]), 8, "0", STR_PAD_LEFT);
                    //echo '<br>';
                    //echo $dc_array[$counter] . ': ' . $txt;
                    //echo '<br>';
                    //    echo $counter;
                    //    echo ': ';
                    //    echo $txt;
                    //$txt=$txt;
                    //    echo '<br>';
                    $counter++;
                }
                // $txt = $txt . 'fd03';
                //$txt = substr($txt, 0, (strlen($txt) - 1));
                return iconv('UTF-32BE', 'utf8', hex2bin($txt));
                //    $dc = $txt;
                //  break;
                // return dce2txt($data);
                break;

            case 'utf32':
                $unicode = bin2hex(iconv('UTF-8', 'UTF-32BE', dce_convert($dc, 'dc', 'utf8')));
                //echo dce_convert($dc, 'dc', 'utf8');
                //echo $unicode;
                return hex2bin($unicode);
                break;

            case 'html_l':
                break;

            case 'cdce':
                break;

            case 'legacy_cdce':
                break;

                break;

            default:
                return 'Unknown output format: ' . $output_format . '. Available: dc, dce, 3_0a, 3_01a, utf8, html_l, html_snippet_l';
            }
        }
        if ($output_log) {
            global $log;
            echo $log;
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

        case 02:
            //This is a DCE 3.01a file
            return '3_01a';
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
                break;
            }
        }
        function legacy_cdce_to_html_snippet($content)
        {
            global $cdce_html_legacy;
            $retval = $content;
            //echo $retval;
            $pattern = '@(\d+)@';
            $matches = array();
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                //echo $match[1];
                $match_html = $cdce_html_legacy[$match[1]];
                $retval = str_replace('@' . $match[1] . '@', $match_html, $retval);
            }
            return $retval;
        }
        function legacy_cdce_parse($content, $strict)
        {
            //$dc=$txt;
            $hex = bin2hex(iconv('UTF-8', 'UTF-32BE', $content));
            //echo '<br>';
            //echo '<br>';
            //echo $data;
            //echo '<br>';
            //echo '<br>';
            //echo $hex;
            //echo '<br>';
            //echo '<br>';
            //echo dce_convert(hex2bin($hex), 'utf32', 'dc');
            //echo '<br>';
            //echo '<br>';
            global $DcMap_Unicode_Lossy;
            global $cdce_html_legacy;
            $counter = 0;
            $txt = '';
            while ($counter < strlen($hex)) {
                //log_add('<br>');
                //log_add('Bytes: ' . substr($hex, $counter, 48) . '…<br>');
                //log_add('UTF-8: ' . hex2bin(ltrim(substr($hex, $counter, 192), '0')) . '<br>');
                //log_add('Dc: ' . dce_convert(hex2bin(ltrim(substr($hex, $counter, 192), '0')), 'utf8', 'dc') . '<br>');
                if (substr($hex, $counter, 8) == '00000040') {
                    //Conditions:
                    //1char
                    //log_add('<br><br><font color="green">1 character CDCE conditions: <br>');
                    //echo ((int) substr($hex, $counter + 16, 8) == '00000040')+0;
                    //echo ((int) substr($hex, $counter + 32, 8) == '00000040')+0; //fails incorrectly
                    //log_add(substr($hex, $counter + 0, 8) . '. Should be: 00000040<br>');
                    //log_add(substr($hex, $counter + 16, 8) . '. Should be: 00000040<br>');
                    //log_add(hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . '. Should be: greater than 0<br>');
                    //log_add(hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . '. Should be: less than 13<br><br>→');
                    //log_add(((int)substr($hex, $counter + 0, 8) == '00000040') + 0); //this works
                    //log_add(((int)substr($hex, $counter + 16, 8) == '00000040') + 0); //this works
                    //log_add(((int)hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) > '0') + 0);
                    //log_add(((int)hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) < '13') + 0);
                    //log_add('<br><br><br>2 character CDCE conditions: <br>');
                    //2char
                    //log_add(substr($hex, $counter + 0, 8) . '. Should be: 00000040<br>');
                    //log_add(substr($hex, $counter + 24, 8) . '. Should be: 00000040<br>');
                    //log_add(hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . hex2bin(ltrim(substr($hex, $counter + 16, 8), '0')) . '. Should be: greater than 0<br>');
                    //log_add(hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . hex2bin(ltrim(substr($hex, $counter + 16, 8), '0')) . '. Should be: less than 13<br>');
                    //echo hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . '. Should be: greater than 0<br>';
                    //echo hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . '. Should be: less than 13<br>';
                    //echo hex2bin(ltrim(substr($hex, $counter + 16, 8), '0')) . '. Should be: greater than 0<br>';
                    //echo hex2bin(ltrim(substr($hex, $counter + 16, 8), '0')) . '. Should be: less than 13<br><br>→';
                    //log_add(((int)substr($hex, $counter + 0, 8) == '00000040') + 0);
                    //log_add(((int)substr($hex, $counter + 24, 8) == '00000040') + 0);
                    //log_add(((int)hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . hex2bin(ltrim(substr($hex, $counter + 16, 8), '0')) > '0') + 0);
                    //log_add(((int)hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . hex2bin(ltrim(substr($hex, $counter + 16, 8), '0')) < '13') + 0);
                    //echo ((int) hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) > '0')+0;
                    //echo ((int) hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) < '13')+0;
                    //echo ((int) hex2bin(ltrim(substr($hex, $counter + 16, 8), '0')) > '0')+0;
                    //echo ((int) hex2bin(ltrim(substr($hex, $counter + 16, 8), '0')) < '13')+0;
                    //log_add('<br><br></font>');
                    if ((substr($hex, $counter + 0, 8) == '00000040') && (substr($hex, $counter + 16, 8) == '00000040') && (hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) > '0') && (hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) < '13')) {
                        $append = hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . ',';
                        $counter = $counter + 16;
                        //log_add('<br><font color="red">');
                        //log_add((($counter / 8) + 1) . '. 1-character Dc appended (' . hex2bin(ltrim(substr($hex, $counter - 8, 8), '0')) . '): ' . $append . ' → ' . $txt . $append);
                        //log_add('<br></font>');
                        
                    } else {
                        if ((substr($hex, $counter + 0, 8) == '00000040') && (substr($hex, $counter + 24, 8) == '00000040') && ((hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . hex2bin(ltrim(substr($hex, $counter + 16, 8), '0'))) > 0) && ((hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . hex2bin(ltrim(substr($hex, $counter + 16, 8), '0'))) < 13)) {
                            $append = hex2bin(ltrim(substr($hex, $counter + 8, 8), '0') . ltrim(substr($hex, $counter + 16, 8), '0')) . ',';
                            $counter = $counter + 24;
                            //log_add('<br><font color="red">');
                            //log_add((($counter / 8) + 1) . '. 2-character Dc appended (' . hex2bin(ltrim(substr($hex, $counter - 16, 16), '0')) . '): ' . $append . ' → ' . $txt . $append);
                            //log_add('<br></font>');
                            
                        } else {
                            if ($strict) {
                                //log_add('<br><font color="red">');
                                //log_add((($counter / 8) + 1) . '. CDCE decoding error!' . ' → ' . $txt . $append);
                                //log_add('<br></font>');
                                return str_replace(',,', ',0,', preg_replace('/,\Z/', '', $txt)) . '… CDCE decoding error!';
                            } else {
                                if (strlen($DcMap_Unicode_Lossy[strtoupper(ltrim(substr($hex, $counter, 8), '0')) ]) != 0) {
                                    $append = $DcMap_Unicode_Lossy[strtoupper(ltrim(substr($hex, $counter, 8), '0')) ] . ',';
                                    //log_add('<br><font color="red">');
                                    //log_add((($counter / 8) + 1) . '. Unicode appended; attempting recovery of corrupted CDCE data: ' . $append . ' → ' . $txt . $append);
                                    //log_add('<br></font>');
                                    
                                } else {
                                    $append = '';
                                    //log_add('<br><font color="red">');
                                    //log_add((($counter / 8) + 1) . '. Unicode not appended; attempting recovery of corrupted CDCE data' . ' → ' . $txt . $append);
                                    //log_add('<br></font>');
                                    
                                };
                            }
                        }
                    }
                } else {
                    if (strlen($DcMap_Unicode_Lossy[strtoupper(ltrim(substr($hex, $counter, 8), '0')) ]) != 0) {
                        $append = $DcMap_Unicode_Lossy[strtoupper(ltrim(substr($hex, $counter, 8), '0')) ] . ',';
                        //log_add('<br><font color="red">');
                        //log_add((($counter / 8) + 1) . '. Unicode appended: ' . $append . ' → ' . $txt . $append);
                        //log_add('<br></font>');
                        
                    } else {
                        $append = '';
                        //log_add('<br><font color="red">');
                        //log_add((($counter / 8) + 1) . '. Unicode not appended' . ' → ' . $txt . $append);
                        //log_add('<br></font>');
                        
                    };
                }
                // $txt = $txt . $DcMap_Unicode_Lossy[strtoupper(ltrim(substr($hex, $counter, 8), '0')) ] . ',';
                $txt = $txt . $append;
                //$txt=$txt;
                $counter = $counter + 8;
            }
            //echo $txt;
            $dc = $txt;
            return $dc;
        }
        function log_add($data)
        {
            global $log;
            //echo $log;
            //$log = $log . "<br>\n" . $data;
            //echo $log;
            
        }
        function c($content)
        {
            $retval = $content;
            $pattern = '@(\d+)@';
            $matches = array();
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $match_html = qry('character', 'character_html', 'character_id', $match[1]);
                $retval = str_replace('@' . $match[1] . '@', $match_html, $retval);
            }
            return $retval;
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
        function unicode_to_base64_3_01a($data)
        {
            global $Base64_to_Dc;
            $base64 = base64_encode($data);
            $counter = 0;
            $dcb64 = '';
            while ($counter < strlen($base64)) {
                $dcb64 = $dcb64 . ',' . $Base64_to_Dc[substr($base64, $counter, 1) ];
                $counter++;
            }
            return substr($dcb64, 1, (strlen($dcb64) - 1));
        }
        function base64_3_01a_to_unicode($data)
        {
            global $Dc_to_Base64;
            $dcarray = explode_escaped(',', $data);
            $dcarray_size = count($dcarray);
            $counter = 0;
            $dcb64 = '';
            while ($counter < $dcarray_size) {
                $dcb64 = $dcb64 . $Dc_to_Base64[$dcarray[$counter]];
                $counter++;
            }
            return base64_decode($dcb64);
        }
        function hex2dce($hex)
        {
            $bin = pack("H" . strlen($hex), $hex);
            return $bin;
        }
        function dce2hex($hex)
        {
            $bin = pack("h" . strlen($hex), $hex);
            return $bin;
        }
        //Hello World!
        //    echo hex2bin('44434565020101FD8048656C6C6F20576F726C642181FD03');
        //echo dce_convert(hex2bin('44434565020101FD8048656C6C6F20576F726C642181FD03'), 'dce', 'dce');
        //echo dce_convert('Hello‐—-World !', 'utf8', 'utf8');
        //echo dce_convert('Hello‐—-World !', 'utf8', 'utf32');
        //echo dce_convert(hex2bin('00000048000000650000006c0000006c0000006f00002010000020140000002d000000570000006f000000720000006c000000640000002000000021'), 'utf32', 'utf32');
        //echo dce_convert('oauo@9@981g3ud @1@@2@1@3@@10@2012@2@2@3@10@2@2@3@7@2@2@3@15@2@2@3@@8@@2@2@3@@8@@2@2@3@@8@@4@@1@@2@1@3@@10@2012@2@2@3@11@2@2@3@@19@2@2@3@16@2@2@3@7@2@2@3@6@2@2@3@27@5@@1@@2@1@3@@10@2013@2@2@3@10@2@2@3@7@2@2@3@15@2@2@3@@8@@2@2@3@@8@@2@2@3@@8@ ', 'legacy_cdce', 'dc', false);
        //echo dce_convert('oauo@9@981g3ud @1@@2@1@3@@10@2012@2@2@3@10@2@2@3@7@2@2@3@15@2@2@3@@8@@2@2@3@@8@@2@2@3@@8@@4@@1@@2@1@3@@10@2012@2@2@3@11@2@2@3@@19@2@2@3@16@2@2@3@7@2@2@3@6@2@2@3@27@5@@1@@2@1@3@@10@2013@2@2@3@10@2@2@3@7@2@2@3@15@2@2@3@@8@@2@2@3@@8@@2@2@3@@8@ ', 'legacy_cdce', 'html_snippet_l', false);
        //echo dce_convert(hex2bin('00000048000000650000006c0000006c0000006f00002010000020140000002d000000570000006f000000720000006c000000640000002000000021'), 'utf32', '3_0a');
        //echo dce_convert(hex2bin('44434565020101FD8048656C6C6F20576F726C64552181FD03'), 'dce', 'dc') . '<br>';
        //echo dce_convert(hex2bin('44434565020102FD8048656C6C6F20576F726C64218181FDFDFD03'), 'dce', 'utf8');
        //echo dce_convert(hex2bin('44434565020102FDC5FD8048656C6C6F2F2C5CC5501010101FD0576F726C64218181FD03'), 'dce', 'utf8');
        //echo dce_convert(hex2bin('44434565020102FD80C501FD48656C6C6F20576F726C642181FD03'), 'dce', 'dc');
        //echo dce_convert('oauo@9@981g3ud @1@@2@1@3@@10@2012@2@2@3@10@2@2@3@7@2@2@3@15@2@2@3@@8@@2@2@3@@8@@2@2@3@@8@@4@@1@@2@1@3@@10@2012@2@2@3@11@2@2@3@@19@2@2@3@16@2@2@3@7@2@2@3@6@2@2@3@27@5@@1@@2@1@3@@10@2013@2@2@3@10@2@2@3@7@2@2@3@15@2@2@3@@8@@2@2@3@@8@@2@2@3@@8@ ', 'legacy_cdce', '3_01a', false);
        //echo dce_convert('Hello‐—-World !', 'utf8', '3_01a');
        //echo unicode_to_base64_3_01a('test');
        //echo base64_3_01a_to_unicode('156,133,148,178,156,127,195,195');
        
