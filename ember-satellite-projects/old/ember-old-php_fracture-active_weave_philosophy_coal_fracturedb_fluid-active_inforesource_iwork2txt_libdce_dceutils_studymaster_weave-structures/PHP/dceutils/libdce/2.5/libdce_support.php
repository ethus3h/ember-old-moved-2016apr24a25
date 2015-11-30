<?php
/*Initial support code.*/
//Global variables and settings
require ('libdce_explode_escaped.php');
require ('libdce_data.php');
include ('libdce_legacy_functions.php');
require ('libdce_x_to_dc.php');
require ('libdce_dc_to_x.php');
include ('libdce_tests.php');
$date = date('Y.m.d-H.i.s.u.Z-I');
function help()
{
    global $assistance;
    echo '<pre>' . $assistance . '</pre>';
}
if ($help) {
    help();
}
if ($debug) {
} else {
    ini_set("display_errors", 0);
}
//Get script directories (not written by me)
$script_directory = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/'));
//End Get script directories
$html_opening = '<html><head><title></title></head><body>';
$html_closing = '</body></html>';
$onestep = false;
$final = '';
$log = '';
$error_list = '';
$error_happened = false;
$log_file_name = 'libdce_log_' . date('Y.m.d-H.i.s.u.Z-I') . '.log';
$log_file_path = $script_directory . '/logs/' . $log_file_name;
$log_filepath_relative = '/logs/' . $log_file_name;
$test_results = '';
function log_add($data)
{
    global $log;
    global $debug;
    if ($debug) {
        $log = $log . "<br>\n" . $data;
    }
}
$error_counter = 0;
function error_add($data)
{
    global $error_counter;
    $error_counter++;
    if (strpos($data, 'rrors were encountered during processing') !== false) {
        $error_counter_final = $error_counter - 1;
        log_add('<br><font color="red"><strong>' . $error_counter_final . ' errors were encountered during processing! Review the following list of error messages and/or the log for more information.</strong></font><br> Note that if you are running the tests, some errors are normal.');
    } else {
        log_add($data);
    }
    global $error_list;
    global $errors;
    global $debug;
    global $error_happened;
    $error_happened = true;
    if ($errors || $debug) {
        if (strpos($data, 'rrors were encountered during processing') !== false) {
            $error_list = $error_list . "<br>\n" . $data;
        } else {
            $error_list = $error_list . "<br>\n<strong>Error " . $error_counter . ':</strong> ' . $data;
        }
    }
}
$testfail = '';
function test_add($data)
{
    global $test_results;
    global $tests;
    if ($tests) {
        $test_results = $test_results . $data;
    }
}
function get_dce_version($data)
{
    $hex = strtolower(bin2hex($data));
    if (substr($hex, 0, 12) !== '444345650201') {
        return 'This document does not appear to be stored using DCE.';
        break;
    }
    switch (substr($hex, 12, 2)) {
    case 01:
        //This is a DCE 3.0a file
        return '3_0a';
        break;

    case 02:
        //This is a DCE 3.01a file
        return '3_01a';
        break;

    default:
        return 'This document is not stored using a supported version of DCE.';
    }
}
?>
