<?php
$assistance = <<<'ENDOFHELP'
================================================================================
                     libdce. Version 2.4, 3 January 2013                        
================================================================================

This is the  main script  of libdce,  a library for  conversion to and
from DCE  and related formats. It  is released under the  terms of the
Affero GPL.

================================================================================
                                                                                
                               FOR THE IMPATIENT:                               
                                                                                
                                                                                
Call the following function (*):

  dce_convert(string $DataToConvert, string $InputFormat, string $OutputFormat)

See the table and notes below for the supported formats and additional
considerations.

* … Those aren't the real variable names…


NOTE:

 When you're done reading this, edit the file 'libdce.php' to change this:

   //Show help?
   $help = true;
  
 to this:

   //Show help?
   $help = false;
  
 (just change the word "true" to "false"). That will disable display of this
 help information in the script's output (you can still find this help
 information inside the script itself).

================================================================================

Changes in this version:

* Update help for the 2.x versions of libdce

* Unify file header structure, and unify file version numbering

* libdce now is stored in a separate folder for each version

* Streamline contents of version 1 folder (obtain the version "2.31-" folder
  for the contents of all the pre-2.4 versions of libdce)
  
* Add licensing note

================================================================================

To do:

* Figure out why the unknown input/output error messages are not added to the
  error list, and resolve the issue
  
* Rewrite the DCE 3.01a output translator

* Update the DcMapSend data table

* Implement Unicode encapsulation

* Implement remaining translators

* Check that the tests are organized in the same manner as the list of
  translators in the documentation, and update if needed

* Possibly, update code structure to reflect the order of the translators
  listed in the documentation

* Disable tests of translators that are currently not working well

* Disable debug mode and testing for release

================================================================================
  
Format support and notes:

Format                                          | Format code    | Read  | Write — Notes
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
DCE*                                            | dce            |  X    |  X   
DCE (hex-encoded)                               | hex_dce        |  X    |  X   
------------------------------------------------+----------------+-------+------
 DCE 3.0a-based formats:                        |                |       |      
                                                |                |       |      
DCE 3.0a**                                      | 3_0a           |  X    |  X   
DCE 3.0a (raw)                                  | dce_3_0a_raw   |  X    |      
DCE 3.0a (hex-encoded)**                        | hex_3_0a       |  X    |  X   
DCE 3.0a (raw hex-encoded)                      | hex_3_0a_raw   |  X    |      
DCE 3.0a (old translator)******                 | 3_0a_old       |  X    | N/A     No tests
dce2txt*******                                  | dce2txt        |  X    | N/A     No tests
dce2hex*******                                  | dce2hex        |  X    | N/A     No tests
hex2dce*******                                  | hex2dce        |  X    | N/A     No tests
------------------------------------------------+----------------+-------+------
 DCE 3.01a-based formats:                       |                |       |      
                                                |                |       |      
DCE 3.01a**;****                                | 3_01a          | (X)   | (X)  
DCE 3.01a (raw)                                 | dce_3_01a_raw  | (X)   |      
DCE 3.01a (hex-encoded)**;****                  | hex_3_01a      | (X)   | (X)  
DCE 3.01a (raw hex-encoded)                     | hex_3_01a_raw  | (X)   |      
------------------------------------------------+----------------+-------+------
 Miscellaneous DCE formats:                     |                |       |      
                                                |                |       |      
Dc ID list (ASCII)***                           | dc             |  X    |  X    
------------------------------------------------+----------------+-------+------
 Unicode:                                       |                |       |      
                                                |                |       |      
UTF-8***                                        | utf8           |  X    |  X   
UTF-32***                                       | utf32          |  X    |  X   
UTF-8 (Base64-encoded)                          | utf8_base64    |  X    |  X   
UTF-8 (Base64-encoded, ASCII Dc ID list)****    | utf8_dc64      |  X    |  X   
UTF-8 (Base64-encoded, ASCII Dc ID list;        | …              | …     | …    
    with headers)****                           | utf8_dc64_enc  |  X    |  X   
UTF-8 (Base64-encoded, DCE 3.01a+ encoding)**** | utf8_dc64_bin  |       | (X)  
UTF-8 (Base64-encoded, DCE 3.01a+ encoding;     | utf8_dc64_bin_ | …     | …
    hex-encoded)****                            |  hex           |       |      
UTF-8 (Base64-encoded, DCE 3.01a+ encoding;     | utf8_dc64_bin_ | …     | …    
    with headers)****                           |  enc           |       | (X)  
UTF-8 (Base64-encoded, DCE 3.01a+ encoding;     | utf8_dc64_bin_ | …     | …    
    with headers; hex-encoded)****              |  enc_hex       |       |      
------------------------------------------------+----------------+-------+------
 HTML:                                          |                |       |      
                                                |                |       |      
HTML                                            | html           |       |      
HTML (snippet)                                  | html_snippet   |       |      
HTML (legacy CDCE output)****                   | html_l         |       | (X)  
HTML (snippet) (legacy CDCE output)****         | html_snippet_l |       | (X)  
>===============================================^================^=======^=====<



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

Note that none of the translators have been updated to support or pro-
vide  infrastructure for the support  of DCE 3.01a's Unicode encapsul-
ation capability.

Additionally, all of libdce 1.43's  functions are included under their
previous  names,  except  for  dce_convert(), which  is  available  as
dce_convert_1_43(),  log_add(), which is  an internal function and not
meant to be used independently, and get_dce_version(), which is avail-
able as  get_dce_version_1_43(). These are not  tested or  maintained,
and may or may not  work properly, but have  been updated slightly for
integration with version 2.0's log management.

If  you need  functionality from  older  versions of DCE,  obtain  the
"2.31-" folder for the contents of all the pre-2.4 versions of libdce.
Note that those versions probably do not work as well as version 2.4…


This  script  contains  code  from  Weave.   Version 2.0  removes  the
requirement of being in the same directory as Weave's scripts.

ENDOFHELP;
//##############################################################################
//CONFIGURATION
//Show help?
$help = true;
//
//Show debug information (log, warnings, and/or error messages)?
$debug = false;
//
//Run and display tests? (Output in log.)
$tests = false;
//Show error messages? (Overridden by $debug.)
$errors = true;
require ('libdce_support.php');
log_add('<span style="background-color:magenta;"><strong>Initialized run at ' . $date . '. Log file name: ' . $log_file_name . ' (Log saving disabled)<br></strong></span><br>');
//#####################################
//FUNCTIONS (dce_convert and get_dce_version are the most useful ones)
//dce_convert: convert given data from input_format to output_format
function dce_convert($data, $input_format, $output_format = "none")
{log_add('<br><strong><span style="background-color:magenta;">Beginning conversion.</span><br><br>State:</strong><br><br><span style="background-color:skyblue;">Input format: ' . $input_format . '<br>Output format: ' . $output_format . '<br>Data: ' . $data . '</span><br>');
    log_add('<br><span style="background-color:magenta;"><strong>Beginning first step: If the input format and the output format are the same, return the input data.</strong></span><br>');
    if (($input_format == $output_format) && $input_format != 'dc') {
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
        if (!function_exists($x_to_dc_function)) {
            return 'Unknown input format.';
            error_add('<font color="red">Error! Unknown input format.</font>');
        }
        $dc = $x_to_dc_function($data);
        $dc = preg_replace('/,\Z/', '', $dc);
        $dc = str_replace(',,', ',0,', $dc);
        log_add('<br><span style="background-color:magenta;"><strong>Beginning fifth step: Convert the data to the chosen output format, and return a value.</strong></span><br>');
        $dc_to_x_function = 'convert_dc_to_' . $output_format . '_output';
        if (!function_exists($dc_to_x_function)) {
            return 'Unknown output format.';
            error_add('<font color="red">Error! Unknown output format.</font>');
        }
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
    error_add('<br><font color="red"><strong>' . $error_counter . ' errors were encountered during processing! Review the above list of error messages and/or the log for more information.</strong></font><br> Note that if you are running the tests, some errors are normal.');
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

