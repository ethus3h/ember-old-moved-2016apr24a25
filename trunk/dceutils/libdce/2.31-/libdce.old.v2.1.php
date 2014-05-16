<?php
$assistance = <<<'ENDOFHELP'
================================================================================
    libdce. Version 2.1, 1 January 2012 (after midnight of 31 December 2012)    
================================================================================

This is the  main script  of libdce,  a library for  conversion to and
from DCE and related formats.

================================================================================

                               FOR THE IMPATIENT:      
                                                        
Call the following function (*):

  dce_convert(string $DataToConvert, string $InputFormat, string $OutputFormat)

See the table and notes below for the supported formats and additional
considerations.

* … Those aren't the real variable names…

================================================================================

Changes in this version:

* Redesigning program structure for better organization

* Improve help

================================================================================

To do:

* Write a better version of DcMapSendSimple (leave the old one there for DCE
  3.01a compatibility though), and update the data table DcMapSend format while
  I'm at it

* Think up a new architecture that will keep the various translators in
  separate files. This version is getting out of hand.

================================================================================
  
Format support and notes:

Format                                          | Format code    | Read  | Write
>===============================================+================+=======+=====<
 CDCE-based formats:                            |                |       |      
                                                |                |       |      
CDCE                                            | cdce           |       |      
CDCE (legacy)***                                | legacy_cdce    |  X    |      
CDCE (legacy, strict)*****                      | cdce_lstrict   |  X    | N/A  
------------------------------------------------+----------------+-------+------
 Generic DCE formats (automatic version         |                |       |      
     selection):                                |                |       |      
                                                |                |       |      
DCE*                                            | dce            |  X    | X    
DCE (hex-encoded)                               | hex_dce        |  X    | X    
DCE (raw hex-encoded)                           | hex_dce_raw    |       |      
------------------------------------------------+----------------+-------+------
 DCE 3.0a-based formats:                        |                |       |      
                                                |                |       |      
DCE 3.0a**                                      | 3_0a           | (X)   | X    
DCE 3.0a (raw)                                  | dce_3_0a_raw   |       |      
DCE 3.0a (hex-encoded)**                        | hex_3_0a       | (X)   | X    
DCE 3.0a (raw hex-encoded)                      | hex_3_0a_raw   |       |      
DCE 3.0a (old translator)******                 | 3_0a_old       |  X    | N/A  
dce2txt*******                                  | dce2txt        |  X    | N/A  
dce2hex*******                                  | dce2hex        |  X    | N/A  
hex2dce*******                                  | hex2dce        |  X    | N/A  
------------------------------------------------+----------------+-------+------
 DCE 3.01a-based formats:                       |                |       |      
                                                |                |       |      
DCE 3.01a**;****                                | 3_01a          | (X)   | X    
DCE 3.01a (raw)                                 | dce_3_01a_raw  |       |      
DCE 3.01a (hex-encoded)**;****                  | hex_3_01a      |  X    | X    
DCE 3.01a (raw hex-encoded)                     | hex_3_01a_raw  |       |      
------------------------------------------------+----------------+-------+------
 Miscellaneous DCE formats:                     |                |       |      
                                                |                |       |      
Dc ID list (ASCII)***                           | dc             |  X    | X    
------------------------------------------------+----------------+-------+------
 Unicode:                                       |                |       |      
                                                |                |       |      
UTF-8***                                        | utf8           |  X    | X    
UTF-32***                                       | utf32          |  X    | X    
UTF-8 (Base64-encoded)                          | utf8_base64    |  X    | X    
UTF-8 (Base64-encoded, ASCII Dc ID list)****    | utf8_dc64      |  X    | X    
UTF-8 (Base64-encoded, DCE 3.01a+ encoding)**** | utf8_dc64_bin  |       | X    
------------------------------------------------+----------------+-------+------
 HTML:                                          |                |       |      
                                                |                |       |      
HTML                                            | html           |       |      
HTML (snippet)                                  | html_snippet   |       |      
HTML (legacy CDCE output)****                   | html_l         |       | X    
HTML (snippet) (legacy CDCE output)****         | html_snippet_l |       | X    



*       …   Automatically detects DCE version when reading, and writes
            to the latest DCE version that is well supported.

**      …   Since this is a DCE format, simply enter DCE as the format
            when reading. Note that writing to old versions of DCE may
            not be lossless.

***     …   Reading/writing these formats may not be lossless with DCE
            3.0a.

****    …   It'll try  but it  probably won't work or won't work well.
            Note that the HTML  translators only work with legacy CDCE
            input and are very buggy (alpha quality).

*****   …   Note that  cdce_lstrict is not actually a  separate format
            from legacy_cdce;  rather, it is an  alias of  legacy_cdce
            that  instructs the  parser to halt upon  reaching  an in-
            correctly  structured  CDCE  sequence (by  default it will
            attempt to recover from the error).

******  …   This is an early version of the DCE 3.0a translator, not a
            separate format. Leave the  output format blank when using
            this  translator,  since the same code  handles both input
            and  output. This is not tested or  maintained, and may or
            may not work properly.

******* …   These are miscellaneous outdated translators, not separate
            formats. Leave the  output format  blank when  using them,
            since the same code  handles both input  and output. These
            are not  tested  or maintained,  and  may or may  not work
            properly.


Additionally, all of libdce 1.43's  functions are included under their
previous  names,  except  for  dce_convert(), which  is  available  as
dce_convert_1_43(),  log_add(), which is  an internal function and not
meant to be used independently, and get_dce_version(), which is avail-
able as  get_dce_version_1_43(). These are not  tested or  maintained,
and may or may not  work properly, but have  been updated slightly for
integration with version 2.0's log management.


This  script  contains  code  from  Weave.   Version 2.0  removes  the
requirement of being in the same directory as Weave's scripts.

ENDOFHELP;
//##############################################################################
//CONFIGURATION
//Show help?
$help = false;
//
//Show debug information (log, warnings, and/or error messages)?
$debug = false;
//
//Run and display tests? (Output in log.)
$tests = false;
//Show error messages? (Overridden by $debug.)
$errors = true;
/* Migration task table:
Format                                          | Format code    | Read  | Write | Migrated to 2.0?
================================================+================+=======+======
 CDCE-based formats:                            |                |       |      
                                                |                |       |      
CDCE                                            | cdce           |       |      
CDCE (legacy)***                                | legacy_cdce    |  X    |       Read, tested
CDCE (legacy, strict)*****                      | cdce_lstrict   |  X    | N/A   Read, tested
------------------------------------------------+----------------+-------+------
 Generic DCE formats (automatic version         |                |       |      
     selection):                                |                |       |      
                                                |                |       |      
DCE*                                            | dce            |  X    | X     Read, tested
DCE (hex-encoded)                               | hex_dce        |  X    | X    
DCE (raw hex-encoded)                           | hex_dce_raw    |       |      
------------------------------------------------+----------------+-------+------
 DCE 3.0a-based formats:                        |                |       |      
                                                |                |       |      
DCE 3.0a**                                      | 3_0a           | (X)   | X     Read, tested
DCE 3.0a (raw)                                  | dce_3_0a_raw   |       |      
DCE 3.0a (hex-encoded)**                        | hex_3_0a       | (X)   | X    
DCE 3.0a (raw hex-encoded)                      | hex_3_0a_raw   |       |      
DCE 3.0a (old translator)******                 | 3_0a_old       |  X    | N/A  
dce2txt*******                                  | dce2txt        |  X    | N/A  
dce2hex*******                                  | dce2hex        |  X    | N/A  
hex2dce*******                                  | hex2dce        |  X    | N/A  
------------------------------------------------+----------------+-------+------
 DCE 3.01a-based formats:                       |                |       |      
                                                |                |       |      
DCE 3.01a**;****                                | 3_01a          | (X)   | X     Read (partial), tested
DCE 3.01a (raw)                                 | dce_3_01a_raw  |       |      
DCE 3.01a (hex-encoded)**;****                  | hex_3_01a      |  X    | X    
DCE 3.01a (raw hex-encoded)                     | hex_3_01a_raw  |       |      
------------------------------------------------+----------------+-------+------
 Miscellaneous DCE formats:                     |                |       |      
                                                |                |       |      
Dc ID list (ASCII)***                           | dc             |  X    | X     Both; tested
------------------------------------------------+----------------+-------+------
 Unicode:                                       |                |       |      
                                                |                |       |      
UTF-8***                                        | utf8           |  X    | X    
UTF-32***                                       | utf32          |  X    | X    
UTF-8 (Base64-encoded)                          | utf8_base64    |  X    | X    
UTF-8 (Base64-encoded, ASCII Dc ID list)****    | utf8_dc64      |  X    | X    
UTF-8 (Base64-encoded, DCE 3.01a+ encoding)**** | utf8_dc64_bin  |       | X    
------------------------------------------------+----------------+-------+------
 HTML:                                          |                |       |      
                                                |                |       |      
HTML                                            | html           |       |      
HTML (snippet)                                  | html_snippet   |       |      
HTML (legacy CDCE output)****                   | html_l         |       | X    
HTML (snippet) (legacy CDCE output)****         | html_snippet_l |       | X    
*/
require ('libdce_support.php');
log_add('<span style="background-color:magenta;"><strong>Initialized run at ' . $date . '. Log file name: ' . $log_file_name . ' (Log saving disabled)<br><br>Beginning conversion.</strong></span><br>');
//#####################################
//Functions (dce_convert and get_dce_version are the most useful ones)
//dce_convert: convert given data from input_format to output_format
function dce_convert($data, $input_format, $output_format = "none")
{
    log_add('<br><span style="background-color:magenta;"><strong>Beginning first step: If the input format and the output format are the same, return the input data.</strong></span><br>');
    if ($input_format == $output_format) {
        return $data;
    } else {
    }
    log_add('<br><span style="background-color:magenta;"><strong>Beginning second step: Detect one-step conversions (translators that have not been updated to the new system) and redirect them.</strong></span><br>');
    $one_steps = array('3_0a_old' => 'none', 'dce2txt' => 'none', 'dce2hex' => 'none', 'hex2dce' => 'none', 'legacy_cdce' => 'html_snippet', 'legacy_cdce' => 'html',);
    if (array_key_exists($input_format, $one_steps)) {
        if ($one_steps[$input_format] == $output_format) {
            $onestep = true;
        } else {
            $onestep = false;
        }
    } else {
        $onestep = false;
    }
    log_add('<br><span style="background-color:magenta;"><strong>Beginning third step: If one-step conversions apply, perform them.</strong></span><br>');
    if ($onestep) {
        $onestep_function = 'onestep' . $input_format . '_to_' . $output_format;
        return $onestep_function($data);
    } else {
        log_add('<br><span style="background-color:magenta;"><strong>Beginning fourth step: Otherwise, convert the data to a Dc list.</strong></span><br>');
        $x_to_dc_function = 'convert_' . $input_format . '_to_dc';
        $dc = $x_to_dc_function($data);
        $dc = preg_replace('/,\Z/', '', $dc);
        $dc = str_replace(',,', ',0,', $dc);
        log_add('<br><span style="background-color:magenta;"><strong>Beginning fifth step: Convert the data to the chosen output format, and return a value.</strong></span><br>');
        $dc_to_x_function = 'convert_dc_to_' . $output_format . '_output';
        return $dc_to_x_function($dc);
        log_add('<br><span style="background-color:magenta;"><strong>Finished conversion.</strong></span><br>');
    }
}
//TESTS HAPPEN HERE
if ($tests) {
    libdce_tests();
    log_add($test_results);
}
//THIS SHOULD ALL BE AT THE END OF THE SCRIPT
if ($error_happened) {
    /*if (strpos($error_list, 'Errors were encountered during processing') !== false) {
     } else {*/
    $error_counter--;
    error_add('<br><font color="red"><strong>' . $error_counter . ' errors were encountered during processing! Review the above list of error messages and/or the log for more information.</strong></font> Note that if you are running the tests, some errors are normal.');
    //}
    
}
if ($errors) {
    if ($debug) {
        log_add('<h2>Error list:</h2>' . $error_list);
    } else {
        if (strlen($error_list) < 1) {
        } else {
            echo '<h2>Error list:</h2>' . $error_list;
        }
    }
} else {
    if ($debug) {
        log_add('<h2>Error list:</h2>' . $error_list);
    } else {
    }
}
log_add('<br><span style="background-color:magenta;"><strong>Finished run at ' . date('Y.m.d-H.i.s.u.Z-I') . '.</strong></span><br><br><br><br>');
if ($debug) {
    //print_r((int)file_put_contents($log_file_path,$html_opening.$log.$html_closing) );
    echo '<br><br><br><br><h2>Log output:</h2><br>' . $log;
}

