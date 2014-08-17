<?php
//DEFINE FUNCTIONS
//Utility functions
include('d/r/wfs.utility.php');
//Database abstraction layer
include('d/r/wfs.dba.php');
//Get a parameter regardless of method
include('d/r/wf.fv.php');
//CDCE parser
include('d/r/wfs.dce.php');
//Define variables
$wvActionId = qry('operation', 'operation_name', 'operation_id', fv('a'));
$wvLocaleString = qry('locale', 'locale_suffix', 'locale_id', fv('locale'));
//Page renderer functions
include('d/r/wfs.render.php');
//Error handling
include('d/r/wfs.errorhandling.php');
//Weave         abstraction layer
//   structures
include('d/r/wfs.Weave_structures.php');
/* END FUNCTION DEFS */
?>
