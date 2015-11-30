<?php
/*Convert Dc to x.*/
function convert_dc_to_dc_output($data)
{
    if ((substr($data, 0, 3) != '114') && ((int)substr($data, 0, 1) != 0) && ((int)substr($data, strlen($data) - 1, 1) != 0)) {
        $dc = '114,' . $data . ',115';
    } else {
        if ((substr($data, 0, 3) != '114') && ((int)substr($data, 0, 1) != 0)) {
            $dc = '114,' . $data;
        } else {
            $dc = $data;
        }
    }
    return $dc;
}
function convert_dc_to_dce_output($data)
{
    //DEPENDS: all version-specific DCE write translators supported by get_dce_version
    return dce_convert($data, 'dc', '3_0a');
}
function convert_dc_to_utf8_base64_output($data)
{
    //DEPENDS: convert_dc_to_utf8_output
    return base64_encode(dce_convert($data, 'dc', 'utf8'));
}
function convert_dc_to_utf8_dc64_output($data)
{
    //DEPENDS: convert_dc_to_utf8_output, unicode_to_base64_3_01a (provided by legacy function support)
    return unicode_to_base64_3_01a(dce_convert($data), 'dc', 'utf8');
}
function convert_dc_to_utf8_dc64_enc_output($data){
//DEPENDS: convert_dc_to_utf8_dc64_output
return '191,' . dce_convert($data,'dc','utf8_dc64') . ',192';
}
function convert_dc_to_utf8_dc64_bin_output($data)
{
    $dc64 = dce_convert($data, 'dc', 'utf8_dc64');
    return DcMapSendSimple($dc64);
}
function convert_dc_to_utf8_dc64_bin_enc_output($data){
//DEPENDS: convert_dc_to_utf8_dc64_bin_output
return hex2bin('c3' . bin2hex(dce_convert($data,'dc','utf8_dc64_bin')) . 'c4');
}
function convert_dc_to_hex_dce_output($data)
{
    return strtoupper(bin2hex(dce_convert($data, 'dc', 'dce')));
}
function convert_dc_to_hex_3_0a_output($data)
{
    //DEPENDS: convert_dc_to_3_0a_output
    return strtoupper(bin2hex(dce_convert($data, 'dc', '3_0a')));
}
function convert_dc_to_hex_3_01a_output($data)
{
    //DEPENDS: convert_dc_to_3_01a_output
    return strtoupper(bin2hex(dce_convert($data, 'dc', '3_01a')));
}
function convert_dc_to_3_0a_output($data)
{
    $dc_array = explode_escaped(',', $data);
    $dc_size = count($dc_array);
    global $DcMapSend_dce3_0a;
    $counter = 0;
    $txt = '44434565020101fd80';
    while ($counter < $dc_size) {
        $txt = $txt . strtolower($DcMapSend_dce3_0a[$dc_array[$counter]]);
        $counter++;
    }
    $txt = $txt . '81fd03';
    return hex2bin($txt);
}
function convert_dc_to_3_01a_output($data)
{
    //TODO
    //DEPENDS: DcMapSendSimple (provided by legacy function support)
    //THIS DOESN'T WORK!
    $Encoded = DcMapSendSimple($dc, '3_01a');
    $txt = '44434565020102fd' . $Encoded . 'fd03';
    return hex2bin($txt);
}
function convert_dc_to_utf8_output($data)
{
    //TODO
    //LOSSY! Provides no encapsulation support.
    $dc_array = explode_escaped(',', $data);
    $dc_size = count($dc_array);
    global $DcMapSend_Unicode;
    $counter = 0;
    $txt = '';
    while ($counter < $dc_size) {
        if (str_pad(strtolower($DcMapSend_Unicode[$dc_array[$counter]]), 8, "0", STR_PAD_LEFT) != '00000000') {
            $txt = $txt . str_pad(strtolower($DcMapSend_Unicode[$dc_array[$counter]]), 8, "0", STR_PAD_LEFT);
        } else {
            if (!(($dc_array[$counter] == '114') || ($dc_array[$counter] == '115'))) {
                $txt = $txt . '0000FFFD';
            }
        }
        $counter++;
    }
    return iconv('UTF-32BE', 'utf8', hex2bin($txt));
}
function convert_dc_to_utf32_output($data)
{
    //DEPENDS: convert_dc_to_utf8_output
    $unicode = bin2hex(iconv('UTF-8', 'UTF-32BE', dce_convert($data, 'dc', 'utf8')));
    return hex2bin($unicode);
}

