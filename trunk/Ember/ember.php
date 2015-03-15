<?php

# Header and setup
{
# 2015mar14, version 2

#based on the other ember.php, version 8-0.91.44
error_reporting(E_ALL);
ini_set("display_errors",1);
}

# 0. Set up utilitarian functions that I need.
{
	function rq($name) {
		# Return a request variable
		if(!isset($_REQUEST[$name])) {
			return new Exception('Unset variable');
		}
	}
}

# 1. Set up procedures I'll use.
{
	#Code snippets
	{
		function createHtmlPage() {
			echo '<html><head><title>Ember</title></head><body>';
		}
		function endHtmlPage() {
			echo '</body></html>';
		}
	}
	#Main actions
	{
		function showWelcomePage() {
			createHtmlPage();
			echo 'Welcome to Ember.<br>';
			endHtmlPage();
		}
	}
}

# 2. Determine what I'm supposed to do
{
	$action = rq('action');
	if($action instanceof Exception) {
		showWelcomePage();
	}
	else {
		switch($action) {
			case 'showIndex':
				showIndex();
				break;
			default:
				resetEmber();
		}
	}
}
?>