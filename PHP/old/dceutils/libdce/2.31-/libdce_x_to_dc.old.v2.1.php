<?php
/*libdce: Version 2.1, 1 January 2012 (after midnight of 31 December 2012)
*/
function convert_cdce_to_dc($data)
{
    return '';
}
function convert_legacy_cdce_to_dc($data, $strict = false)
{
    //This function relies on the lossy UTF8-to-Dc conversion provided by the libdce 1.43 version included with libdce 2.0. Once UTF8 to Dc conversion is implemented in libdce 2.0 this function can be updated.
    $hex = bin2hex(iconv('UTF-8', 'UTF-32BE', $data));
    global $DcMap_Unicode_Lossy;
    global $cdce_html_legacy;
    $counter = 0;
    $txt = '';
    while ($counter < strlen($hex)) {
        log_add('<br>');
        log_add('Bytes: ' . substr($hex, $counter, 48) . '…<br>');
        log_add('UTF-8: ' . hex2bin(ltrim(substr($hex, $counter, 192), '0')) . '<br>');
        log_add('Dc: ' . dce_convert_1_43(hex2bin(ltrim(substr($hex, $counter, 192), '0')), 'utf8', 'dc') . '<br>');
        if (substr($hex, $counter, 8) == '00000040') {
            log_add('<br><br><font color="green">1 character CDCE conditions: <br>');
            log_add(substr($hex, $counter + 0, 8) . '. Should be: 00000040<br>');
            log_add(substr($hex, $counter + 16, 8) . '. Should be: 00000040<br>');
            log_add(hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . '. Should be: greater than 0<br>');
            log_add(hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . '. Should be: less than 13<br><br>→');
            log_add(((int)substr($hex, $counter + 0, 8) == '00000040') + 0); //this works
            log_add(((int)substr($hex, $counter + 16, 8) == '00000040') + 0); //this works
            log_add(((int)hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) > '0') + 0);
            log_add(((int)hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) < '13') + 0);
            log_add('<br><br><br>2 character CDCE conditions: <br>');
            log_add(substr($hex, $counter + 0, 8) . '. Should be: 00000040<br>');
            log_add(substr($hex, $counter + 24, 8) . '. Should be: 00000040<br>');
            log_add(hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . hex2bin(ltrim(substr($hex, $counter + 16, 8), '0')) . '. Should be: greater than 0<br>');
            log_add(hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . hex2bin(ltrim(substr($hex, $counter + 16, 8), '0')) . '. Should be: less than 13<br>');
            log_add(((int)substr($hex, $counter + 0, 8) == '00000040') + 0);
            log_add(((int)substr($hex, $counter + 24, 8) == '00000040') + 0);
            log_add(((int)hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . hex2bin(ltrim(substr($hex, $counter + 16, 8), '0')) > '0') + 0);
            log_add(((int)hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . hex2bin(ltrim(substr($hex, $counter + 16, 8), '0')) < '13') + 0);
            log_add('<br><br></font>');
            if ((substr($hex, $counter + 0, 8) == '00000040') && (substr($hex, $counter + 16, 8) == '00000040') && (hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) > '0') && (hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) < '13')) {
                $append = hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . ',';
                $counter = $counter + 16;
                log_add('<br><font color="red">');
                log_add((($counter / 8) + 1) . '. 1-character Dc appended (' . hex2bin(ltrim(substr($hex, $counter - 8, 8), '0')) . '): ' . $append . ' → ' . $txt . $append);
                log_add('<br></font>');
            } else {
                if ((substr($hex, $counter + 0, 8) == '00000040') && (substr($hex, $counter + 24, 8) == '00000040') && ((hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . hex2bin(ltrim(substr($hex, $counter + 16, 8), '0'))) > 0) && ((hex2bin(ltrim(substr($hex, $counter + 8, 8), '0')) . hex2bin(ltrim(substr($hex, $counter + 16, 8), '0'))) < 13)) {
                    $append = hex2bin(ltrim(substr($hex, $counter + 8, 8), '0') . ltrim(substr($hex, $counter + 16, 8), '0')) . ',';
                    $counter = $counter + 24;
                    log_add('<br><font color="red">');
                    log_add((($counter / 8) + 1) . '. 2-character Dc appended (' . hex2bin(ltrim(substr($hex, $counter - 16, 16), '0')) . '): ' . $append . ' → ' . $txt . $append);
                    log_add('<br></font>');
                } else {
                    if ($strict) {
                        error_add('<br><font color="red">');
                        error_add((($counter / 8) + 1) . '. CDCE decoding error!' . ' → ' . $txt . $append);
                        error_add('<br></font>');
                        return str_replace(',,', ',0,', preg_replace('/,\Z/', '', $txt)) . '… CDCE decoding error!';
                    } else {
                        if (strlen($DcMap_Unicode_Lossy[strtoupper(ltrim(substr($hex, $counter, 8), '0')) ]) != 0) {
                            $append = $DcMap_Unicode_Lossy[strtoupper(ltrim(substr($hex, $counter, 8), '0')) ] . ',';
                            error_add('<br><font color="red">');
                            error_add((($counter / 8) + 1) . '. Unicode appended; attempting recovery of corrupted CDCE data: ' . $append . ' → ' . $txt . $append);
                            error_add('<br></font>');
                        } else {
                            $append = '';
                            error_add('<br><font color="red">');
                            error_add((($counter / 8) + 1) . '. Unicode not appended; attempting recovery of corrupted CDCE data' . ' → ' . $txt . $append);
                            error_add('<br></font>');
                        };
                    }
                }
            }
        } else {
            if (strlen($DcMap_Unicode_Lossy[strtoupper(ltrim(substr($hex, $counter, 8), '0')) ]) != 0) {
                $append = $DcMap_Unicode_Lossy[strtoupper(ltrim(substr($hex, $counter, 8), '0')) ] . ',';
                log_add('<br><font color="red">');
                log_add((($counter / 8) + 1) . '. Unicode appended: ' . $append . ' → ' . $txt . $append);
                log_add('<br></font>');
            } else {
                $append = '';
                log_add('<br><font color="red">');
                log_add((($counter / 8) + 1) . '. Unicode not appended' . ' → ' . $txt . $append);
                log_add('<br></font>');
            };
        }
        $txt = $txt . $append;
        $counter = $counter + 8;
    }
    $dc = $txt;
    return $dc;
}
function convert_cdce_lstrict_to_dc($data)
{
    //DEPENDS: convert_legacy_cdce_to_dc
    return convert_legacy_cdce_to_dc($data, true);
}
function convert_dc_to_dc($data)
{
    return $data;
}
function convert_dce_to_dc($data)
{
    //DEPENDS: all version-specific DCE translators supported by get_dce_version (currently supplied by dce_convert_1_43)
    $hex = strtolower(bin2hex($data));
    if (substr($hex, 0, 12) !== '444345650201') {
        error_add('<font color="red">Error! This document is not stored using a supported format.</font>');
        return 'This document is not stored using a supported format.';
    }
    if (function_exists('convert_' . get_dce_version($data) . '_to_dc')) {
        return dce_convert($data, get_dce_version($data), 'dc');
    } else {
        error_add('<font color="red">Error! This document does not appear to be stored using a supported version of DCE.</font>');
        return 'This document does not appear to be stored using a supported version of DCE.';
    }
}
function convert_3_0a_to_dc($data)
{
    $hex = bin2hex($data);
    if (substr($hex, 12, 2) !== '01') {
        error_add('<font color="red">Error! This document is not stored using the correct version of DCE.</font>');
        return 'This document is not stored using the correct version of DCE.';
    }
    //######################################################
    //This is a DCE 3.0a file.
    global $DcMap_dce3_0a_Core;
    $counter = 14;
    $txt = '';
    while ($counter < strlen($hex)) {
        $txt = $txt . $DcMap_dce3_0a_Core[strtoupper(substr($hex, $counter, 2)) ] . ',';
        if (substr($hex, $counter, 4) == 'fd03') {
            break;
        }
        $counter = $counter + 2;
    }
    $txt = substr($txt, 3, (strlen($txt) - 6));
    return $txt;
}
function convert_3_01a_to_dc($data)
{
//This function needs to be improved to fully support DCE 3.01a!
    $hex = bin2hex($data);
    if (substr($hex, 12, 2) !== '02') {
        error_add('<font color="red">Error! This document is not stored using the correct version of DCE.</font>');
        return 'This document is not stored using the correct version of DCE.';
    }
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
        log_add('<br><font color="red">' . ((($counter - 14) / 2) + 1) . ': </font><br>State: ' . $state . '<br>Hex position: ' . substr($hex, $counter, 2) . '<br>Dc ID: ' . $DcMap_dce3_01a_Core[strtoupper(substr($hex, $counter, 2)) ] . '<br>Appending: ');
        $data_array_name = $varAppend . $state;
        $data_array = $$data_array_name;
        switch ($state) {
        case 'Core':
            if (substr($DcMap_dce3_01a_Core[strtoupper(substr($hex, $counter, 2)) ], 0, 1) == '>') {
                //Switch states
                $state = substr($DcMap_dce3_01a_Core[strtoupper(substr($hex, $counter, 2)) ], 1, strlen($DcMap_dce3_01a_Core[strtoupper(substr($hex, $counter, 2)) ]) - 1);
                $append = '';
                $action = 'State switch out of Core) <br>';
                break;
            } else {
                $append = $DcMap_dce3_01a_Core[strtoupper(substr($hex, $counter, 2)) ] . ',';
                $action = 'Append from Core) <br>';
            }
            break;

        case 'Variant_Selectors':
            if ((substr($hex, $counter, 2) == 'fd') || (substr($hex, $counter, 2) == 'fe')) {
                $state = 'Core';
                $append = '';
                $action = 'State switch out of Variant_Selectors) <br>';
            } else {
                $append = $DcMap_dce3_01a_Variant_Selectors[strtoupper(substr($hex, $counter, 2)) ] . ',';
                $action = 'Append from Variant_Selectors) <br>';
            }
            break;

        case 'Semantic_Records':
            if ((substr($hex, $counter, 2) == 'fd') || (substr($hex, $counter, 2) == 'fe')) {
                $state = 'Core';
                $append = '';
                $action = 'State switch out of Semantic_Records) <br>';
            } else {
                $append = $DcMap_dce3_01a_Semantic_Records[strtoupper(substr($hex, $counter, 2)) ] . ',';
                $action = 'Append from Semantic_Records) <br>';
            }
            break;

        case 'Mathematics':
            if ((substr($hex, $counter, 2) == 'fd') || (substr($hex, $counter, 2) == 'fe')) {
                $state = 'Core';
                $append = '';
                $action = 'State switch out of Mathematics) <br>';
            } else {
                $append = $DcMap_dce3_01a_Mathematics[strtoupper(substr($hex, $counter, 2)) ] . ',';
                $action = 'Append from Mathematics) . <br>';
            }
            break;

        case 'Whitespace_and_Punctuation':
            if ((substr($hex, $counter, 2) == 'fd') || (substr($hex, $counter, 2) == 'fe')) {
                $state = 'Core';
                $append = '';
                $action = 'State switch out of Whitespace_and_Punctuation) <br>';
            } else {
                $append = $DcMap_dce3_01a_Whitespace_and_Punctuation[strtoupper(substr($hex, $counter, 2)) ] . ',';
                $action = 'Append from Whitespace_and_Punctuation) <br>';
            }
            break;
        }
        if (substr($hex, $counter, 4) == 'fd03') {
            $action = '<br>Halting. <br>';
            break;
        }
        $txt = $txt . $append;
        $counter = $counter + 2;
        log_add(rtrim($append, ',') . '<br>Action: ' . substr($action, 0, (strlen($action) - 6)) . '<br><font color="green">→ ' . $txt . '</font><br><br>');
    }
    log_add(rtrim($append, ',') . '<br>Action: ' . substr($action, 4, (strlen($action) - 5)) . '<br><br><br><br>');
    $txt = substr($txt, 3, (strlen($txt) - 4));
    return $txt;
}

