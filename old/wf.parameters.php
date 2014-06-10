<?php
//Get a parameter regardless of method
function fv($var)
{
    global $$var;
    if ($_SESSION[$var]) {
        $$var = $_SESSION[$var];
    } else {
        $$var = $_REQUEST[$var];
    }
    return $$var;
}
?>
