<?php
error_reporting(E_ALL);
ini_set("display_errors", true);
$displayDebugMessages = True;
#Futuramerlin Active Scripting Library.
$activeVersion = 'Version 0.91.13, 28 April 2014.';
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
GENERAL-USE CLASSES
*************/

//FluidActive is the class implementing the Fluid//Active web UI toolkit
include('active.fluid.php');


//FractureDB is the class that implements the FractureDB data management system.
include('active.fracturedb.php');

/*************
GENERAL-USE FUNCTIONS
*************/

include('active.functions.php');

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