<?php
//##############################################################################
//CONFIGURATION
if(isset($_REQUEST['silenti'])|isset($_REQUEST['tools'])){$help=false;$debug=false;}else{
//BASIC SETTINGS:
//Show help?
$help = false;
//
//Show error messages? (Overridden by $debug.)
$errors = false;}
//
//TECHNICAL SETTINGS:
//
//Show debug information? (also, uncomment first line of dceutils.php for warnings/errors)
$debug = false;
//Show log in page?
$log_show_in_page = false;
//Run and display tests?
$tests = false;
//Server prefix
$serverprefix='http://127.0.0.1/dceutils/';

//Dceutils version
$dceutilsversion='EmberEmbedded';
//##############################################################################
// $baggage_claim = new baggage_claim();
global $baggage_claim;
global $serverprefix;
global $dceutilsversion;
$baggage_claim->check_luggage('serverprefix', $serverprefix);
$baggage_claim->check_luggage('dceutilsversion', $dceutilsversion);
?>
