<?php
#Futuramerlin Active Scripting Library.
//from http://davidwalsh.name/increase-php-script-execution-time-limit-ini_set
ini_set('max_execution_time', 300);
global $emberVersion;
$activeVersion = $emberVersion;
#Some code based on StudyMaster; some based on the other d/r scripts.
#Useful SQL commands:
#
#Return all currently-out barrels to pool (follow this up with an expire-old-barrels from the management tool):
#
#UPDATE `am_barrels` SET dateAssigned=0 WHERE status=0
#
#See whatever's running on the database:
#
#SHOW FULL PROCESSLIST

/*************
GENERAL CONFIGURATION
*************/

include('active.config.php');

/*************
GENERAL-USE FUNCTIONS
*************/

include('active.functions.php');

/*************
GENERAL-USE CLASSES
*************/

//FluidActive is the class implementing the Fluid//Active web UI toolkit
include('active.fluid.php');


//FractureDB is the class that implements the FractureDB data management system.
include('active.fracturedb.php');

/*************
ADDITIONAL CLASSES
*************/

include('active.classes.extra.php');

/*************
APPLICATION-SPECIFIC FUNCTIONS
*************/

include('active.functions.extra.php');

/*************
REQUEST RESPONDERS
*************/

include('active.responders.php');

/*************
WEB INTERFACE RESPONDERS
*************/

include('active.responders.www.php');

/*************
REQUEST ROUTING CODE
*************/

include('active.routing.php');

?>