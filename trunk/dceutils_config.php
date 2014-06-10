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
class baggage_claime
{
    public $serverprefix;
    public $dceutilsversion;
    function check_luggage($variable, $new_content)
    {
        $this->$variable = $new_content;
    }
    function claim_luggage($variable)
    {
        return $this->$variable;
    }
}
global $baggage_claim;
$baggage_claim = new baggage_claime;
global $serverprefix;
global $dceutilsversion;
$baggage_claim->check_luggage('serverprefix', $serverprefix);
$baggage_claim->check_luggage('dceutilsversion', $dceutilsversion);
?>
