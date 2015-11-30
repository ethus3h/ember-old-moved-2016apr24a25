<?php
//Request handler
if (isset($_REQUEST['silenti'])) {
    if ($_REQUEST['hexadecimal'] == 1) {
        $result = hex2bin(dce_convert($_REQUEST['data'], $_REQUEST['source'], $_REQUEST['target']));
    } else {
        $result = dce_convert($_REQUEST['data'], $_REQUEST['source'], $_REQUEST['target']);
    }
    echo 'Result: ' . $result;
} else {
}
?>
