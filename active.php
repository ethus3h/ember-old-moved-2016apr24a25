<?php
#Futuramerlin Active Scripting Library. Version 0.9, 20 March 2014.
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

include('config.php');

/*************
GENERAL-USE CLASSES
*************/

//FluidActive is the class implementing the Fluid//Active web UI toolkit
include('fluid.php');

//FractureDB is the class that implements the FractureDB data management system.
include('fracturedb.php');

/*************
GENERAL-USE FUNCTIONS
*************/

include('functions.php');

/*************
ADDITIONAL CLASSES
*************/

include('classes.extra.php');

/*************
APPLICATION-SPECIFIC FUNCTIONS
*************/

include('functions.extra.php');

/*************
REQUEST RESPONDERS
*************/

include('responders.php');

/*************
WEB INTERFACE RESPONDERS
*************/

include('responders.www.php');

/*************
REQUEST ROUTING CODE
*************/

include('routing.php');

?> 