<?php
/*libdce: convert Dc to x. Version 2.2, 1 January 2013.
*/
function convert_dc_to_dc_output($data)
{
    if ((substr($data, 0, 3) != '114') && ((int)substr($data, 0, 1) != 0) && ((int)substr($data, strlen($data) - 1, 1) != 0)) {
        $dc = '114,' . $data . ',115';
    } else {
if ((substr($data, 0, 3) != '114') && ((int)substr($data, 0, 1) != 0)) {$dc = '114,' . $data;} else {
        $dc = $data;}
    }
    return $dc;
}

