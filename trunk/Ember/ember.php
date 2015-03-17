<?php

# Header and setup
{
# 2015mar16

$emberVersion = "1.0.44";

#based on the other ember.php, version 8-0.91.44
error_reporting(E_ALL);
ini_set("display_errors",1);
}

# 0. Set up utilitarian functions that I need.
{
	#Utilities
	{
		function rq($name,$returnEmptyIfUndefined = false) {
		# Return a request variable
		if(!isset($_REQUEST[$name])) {
			if($returnEmptyIfUndefined) {
				return '';
			}
			return new Exception('Unset variable');
		}
		else {
			return $_REQUEST[$name];
		}
	}
		function getPaddedTimezone() {
			$timezone = date('Z');
			if($timezone[0]=='-') {
				$timezone = str_pad($timezone,6);
			}
			else {
				$timezone = str_pad('+'.$timezone,6);
			}
			return $timezone;
		}
	}
	#Hash functions
	{
		function md5s($data) { 
			return md5($data);
		}
	
		function sha1s($data) {
			return sha1($data);
		}
	
		function sha512s($data) {
			return hash('sha512',$data);
		}
	}
	#Data converters
	{
	
		function getExtension($format) {
			switch($format) {
				case 'ascii':
					return '.txt';
					break;
				case 'edf_1_0_43':
					return '.edf';
					break;
				case 'edf_1_0_44':
					return '.edf';
					break;
				default:
					return new Exception('Unknown format');
			}
		}
		function convert($data,$sourceFormat,$targetFormat,$options=array()) {
			if($sourceFormat == $targetFormat) {
				return $data;
			}
			
			if($sourceFormat == 'ascii' && $targetFormat == 'edf_1_0_43') {
				if(array_key_exists('version',$options)) {
					$version = $options['version'];
				}
				else {
					$version = '';
				}
				return convert_ascii_to_edf_1_0_43($data,$version);
			}
			
			if($sourceFormat == 'ascii' && $targetFormat == 'edf_1_0_44') {
				if(array_key_exists('comments',$options)) {
					$version = $options['comments'];
				}
				else {
					$comments = '';
				}
				return convert_ascii_to_edf_1_0_44($data,$comments);
			}
			
			return new Exception('Unknown data conversion pair');
		}
		
		
		function convert_ascii_to_edf_1_0_43($content,$version='') {
			return convert_data_to_edf_1_0_43_wrapped_raw($content,$version);
		}
		function convert_ascii_to_edf_1_0_44($content,$comments = '') {
			return convert_data_to_edf_wrapped_raw($content,$comments);
		}
		function convert_data_to_edf_1_0_43_wrapped_raw($content,$version = '') {
			#Return an EDF document body $content as an EDF document.
			if(strlen($version)==0){
				$version = "1".str_repeat(" ",971);
			}
			#date from http://php.net/manual/en/function.date.php
			global $emberVersion;
			$authorIdentifier = str_pad("PUAI:ember.php ".$emberVersion."-generated ".date("F j, Y, g:i a"),566);
			$header = hex2bin("89")."EDFe".hex2bin("0D0A1A0AFEFF")."|http://futuramerlin.com/|Format version:";
			$header = $header.$version;
			$header = $header."|MD5:".md5s($content)."|SHA1:".sha1s($content)."|SHA512:".sha512s($content)."|Author Identifier:";
			$header = $header.$authorIdentifier;
			$header = $header."|MD5:".md5s($header)."|SHA1:".sha1s($header)."|SHA512:".sha512s($header);
			$header = $header.hex2bin("00");
			return $header.$content;
		}
		function convert_data_to_edf_wrapped_raw($content,$comments = '') {
			#Return an EDF document body $content as an EDF document.
			$version = "1_0_44".str_repeat(" ",966);
			$location = ' Ember PHP script';
			#date from http://php.net/manual/en/function.date.php
			global $emberVersion;
			$authorIdentifier = str_pad("PUAI:ember.php ".$emberVersion."-generated ".date("F j, Y, g:i a"),567);
			$creationMetadata = "|Creation time:".date('+00Y-m-d H.i.s.u ').getPaddedTimezone()." ".str_pad(" (PHP date() result, with two extra 00s at the beginning of the year)",32);
			$creationMetadata = str_pad($creationMetadata."|Creation location: ".$location,1517);
			$comments = str_pad($comments,501);
			$header = hex2bin("89")."EDFe".hex2bin("0D0A1AFEFF0A")."|http://futuramerlin.com/|Format version:";
			$header = $header.$version;
			$header = $header."|MD5:".md5s($content)."|SHA1:".sha1s($content)."|SHA512:".sha512s($content)."|Author Identifier:";
			$header = $header.$authorIdentifier;
			$header = $header."|Creation metadata:".$creationMetadata;
			$header = $header."|Comments:".$comments;
			$header = $header."|MD5:".md5s($header)."|SHA1:".sha1s($header)."|SHA512:".sha512s($header);
			$header = $header.hex2bin("00");
			return $header.$content;
		}
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
		function getHelloWorld($format = 'ascii') {
			if($format == '') { $format = 'ascii'; }
			return convert("Hello World!",'ascii',$format);
		}
	}
	#Tests
	{
		function runTests() {
			testCreateOldEdf();
		}
		
		function testCreateOldEdf() {
			
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
		function showHelloWorld() {
			$format = rq('format',true);
			$helloWorld = getHelloWorld($format);
			#help from http://webdesign.about.com/od/php/ht/force_download.htm
			$filename = 'HelloWorldGenerated'.date('c');
			$extension = getExtension($format);
			$length = strlen($helloWorld);
			header("Content-disposition: attachment; filename=".$filename.$extension);
			header("Content-type: application/octet-stream");
			header("Content-length: ".$length);
			echo $helloWorld;
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
			case "showHelloWorld":
				showHelloWorld();
				break;
			default:
				resetEmber();
		}
	}
}
?>