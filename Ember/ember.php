<?php

# Header and setup
{
# 2015mar14a15, version 3

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
		else {
			return $_REQUEST[$name];
		}
	}
	
	function md5s($data) { 
		return md5($data);
	}
	
	function sha1s($data) {
		return sha1($data);
	}
	
	function sha512s($data) {
		return hash('sha512',$data);
	}
	
	function edf($content,$version="") {
		#Return an EDF document body $content as an EDF document.
		if(strlen($version)==0){
			$version = "1".str_repeat(" ",971);
		}
		#date from http://php.net/manual/en/function.date.php
		$authorIdentifier = str_pad("PUAI:ember.php 3-generated ".date("F j, Y, g:i a"),566);
		$header = hex2bin("89")."EDFe".hex2bin("0D0A1A0AFEFF")."|http://futuramerlin.com/|Format version:";
		$header = $header.$version;
		$header = $header."|MD5:".md5s($content)."|SHA1:".sha1s($content)."|SHA512:".sha512s($content)."|Author Identifier:";
		$header = $header.$authorIdentifier;
		$header = $header."|MD5:".md5s($header)."|SHA1:".sha1s($header)."|SHA512:".sha512s($header);
		$header = $header.hex2bin("00");
		return $header.$content;
	}
}

# 1. Set up procedures I'll use.
{
	#Code snippets
	{
		function createHtmlPage($title="Ember",$head="",$doctype="<!DOCTYPE html>") {
			echo $doctype.'<html><head><title>'.$title.'</title>'.$head.'</head><body>';
		}
		function endHtmlPage() {
			echo '</body></html>';
		}
	}
	#Tests
	{
		function runTests() {
			testCreateEdf();
		}
		
		function testCreateEdf() {
			
		}
	}
	#Main actions
	{
		function showWelcomePage() {
			createHtmlPage();
			echo 'Welcome to Ember.<br>';
			endHtmlPage();
		}
		function showTests() {
			createHtmlPage("Ember: Tests");
			echo 'Test results:<br>';
			echo runTests();
			endHtmlPage();
		}
		function showTestEDFDocument() {
			echo edf("Hello World!");
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
			case "showIndex":
				showIndex();
				break;
			case "showTests":
				showTests();
				break;
			case "showTestEDFDocument":
				showTestEDFDocument();
				break;
			default:
				resetEmber();
		}
	}
}
?>