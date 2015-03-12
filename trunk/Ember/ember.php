<?php

# 2015mar11, 1st version

# 0. Set up utilitarian functions that I need.
{
	function rq($name) {
		# Return a request variable
	
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
	if($action instanceof "Exception") {
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