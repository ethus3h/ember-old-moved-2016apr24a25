<?php
//Get a parameter regardless of method
function fv($var)
{
    global $$var;
    if(isset($_SESSION[$var])) {
		if ($_SESSION[$var]) {
			$$var = $_SESSION[$var];
		} else {
			$$var = $_REQUEST[$var];
		}
	}
	else {
	    if(isset($_REQUEST[$var])) {
			$$var = $_REQUEST[$var];
		}
		else {
			$$var = null;
		}
	}
    return $$var;
}
?>
