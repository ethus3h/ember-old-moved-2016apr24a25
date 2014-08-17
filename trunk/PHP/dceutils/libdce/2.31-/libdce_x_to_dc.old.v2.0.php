<?php
/*libdce: convert x to Dc. Version 2.0, 31 December 2012 (first independent version).
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
    return dce_convert_1_43($data, get_dce_version($data), 'dc');
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

