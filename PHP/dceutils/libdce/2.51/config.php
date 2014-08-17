<?php
//##############################################################################
//CONFIGURATION
if(isset($_REQUEST['silenti'])|isset($_REQUEST['tools'])){$help=false;$debug=false;}else{
//Show help?
$help = true;
//
//Show debug information?
$debug = false;
//
//Run and display tests?
$tests = false;
//Show error messages? (Overridden by $debug.)
$errors = true;}
//Server prefix
$serverprefix='http://127.0.0.1/dceutils/';

//Dceutils version
$dceutilsversion='2.51';
//##############################################################################
global $baggage_claim;
global $serverprefix;
global $dceutilsversion;
$baggage_claim->check_luggage('serverprefix', $serverprefix);
$baggage_claim->check_luggage('dceutilsversion', $dceutilsversion);
?>
