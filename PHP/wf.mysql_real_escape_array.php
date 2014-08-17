<?php
//Return an escaped array (not written by me)
function mysql_real_escape_array($t)
{
    return array_map("mysql_real_escape_string", $t);
}
?>
