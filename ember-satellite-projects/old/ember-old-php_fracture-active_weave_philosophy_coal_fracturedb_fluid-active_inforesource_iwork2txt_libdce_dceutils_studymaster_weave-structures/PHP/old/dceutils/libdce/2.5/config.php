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
$serverprefix='http://127.0.0.1/libdce/';

//Libdce version
$libdceversion='2.5';
//##############################################################################
global $baggage_claim;
global $serverprefix;
global $libdceversion;
$baggage_claim->check_luggage('serverprefix', $serverprefix);
$baggage_claim->check_luggage('libdceversion', $libdceversion);
?>
