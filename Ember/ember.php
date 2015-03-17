<?php

# 0. Header and setup
{
	# 2015mar16 and 2015mar16a17

	$emberVersion = "1.0.44";

	#based on the other ember.php, version 8-0.91.44
	error_reporting(E_ALL);
	ini_set("display_errors",1);
	
	$formats = array(
		"ascii" => array("ASCII text","*.txt","Y (pending)","Y (pending)","No notes at this time"),
		
	);
}

# 1. Set up utilitarian functions that I need.
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

		#Baggage Claim class from Fracture//Active
		{
			//baggage_claim is a hacky utility class for transferring data from one part of a script to another without worrying about variable scope
			class baggage_claim
			{
				public $temp_temp_table;
				public $tableid;
				public $table;
				public $next;
				function check_luggage($variable, $new_content)
				{
					$this->$variable = $new_content;
				}
				function claim_luggage($variable)
				{
					return $this->$variable;
				}
			}
			global $baggage_claim;
			$baggage_claim = new baggage_claim;
		}
		
		#String functions based on Weave: str_trunc, strrposlimit, shorten
		{
			function str_trunc($str, $max, $strict = TRUE, $trunc = '')
			{
				// Returns a trunctated version of $str up to $max chars, excluding $trunc.
				//Not written by me.
				// $strict = FALSE will allow longer strings to fit the last word.
				if (strlen($str) <= $max) {
					return $str;
				} else {
					if ($strict) {
						return substr($str, 0, strrposlimit($str, ' ', 0, $max + 1)) . $trunc;
					} else {
						$strloc = strpos($str, ' ', $max);
						if (strlen($strloc) != 0) {
							return substr($str, 0, $strloc) . $trunc;
						} else {
							return $str;
						}
					}
				}
			}
			function strrposlimit($haystack, $needle, $offset = 0, $limit = NULL)
			{
				// Works like strrpos, but allows a limit
				//Not written by me.
				if ($limit === NULL) {
					return strrpos($haystack, $needle, $offset);
				} else {
					$search = substr($haystack, $offset, $limit);
					return strrpos($search, $needle, 0);
				}
			}
			function shorten($content)
			{
				//Shorten a string
				if (strlen($content) > 32) {
					//trim to 64 but round by words
					$shortenedstring = str_trunc($content, 32) . "…";
					global $baggage_claim;
					$baggage_claim->check_luggage('Shortened', 'true');
					if (strlen($shortenedstring) > 40) {
						//trim to 64
						$shortenedstring = substr($content, 0, 32) . "…";
					}
				} else {
					$shortenedstring = $content;
					global $baggage_claim;
					$baggage_claim->check_luggage('Shortened', 'false');
				}
				if(strlen($shortenedstring)>38) {
					return $shortenedstring;
				}
				else {
					$shortenedstring = substr($content,0,64);
					if(strlen($shortenedstring)<strlen($content)) {
						$shortenedstring = $shortenedstring.'…';
					}
					return $shortenedstring;
				}
			}
		}
	}
	#Values
	{
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
				case 'latinascii':
					return '.txt';
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
			
			if($sourceFormat == 'latinascii' && $targetFormat == 'dc') {
				return convert_latinascii_to_dc($data);
			}

			return new Exception('Unknown data conversion pair');
		}
		
		#Parsers: X to Dc converters
		{
			function convert_latinascii_to_dc($data) {
			}
		}

		#Writers: Dc to X converters
		{
		}

		#Standalone converters
		{
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
				$creationMetadata = str_pad("",20)."|Creation time:".date('+00Y-m-d H.i.s.u ').getPaddedTimezone()." ".str_pad(" (PHP date() result, with two extra 00s and extra + at the beginning of the year)",32);
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
}

# 2. Set up procedures I'll use.
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
			$t = new Tester();
			$t->test("ASCII to ASCII",convert("Hello World!",'ascii','ascii'),"Hello World!");
			$t->test("ASCII to EDF 1.0.43 and back",convert(convert("Hello World!",'ascii','edf_1_0_43'),'edf_1_0_43','ascii'),"Hello World!");
			$t->testLcMatches("ASCII to EDF 1.0.43",bin2hex(convert("Hello World!",'ascii','edf_1_0_43')),"89454446650D0A1A0AFEFF7C687474703A2F2F6675747572616D65726C696E2E636F6D2F7C466F726D61742076657273696F6E3A31.+");
			return $t->results();
		}
		class Tester
		{
			#based on dceutils 2.6
			protected $results = '';
			public function test($name, $result, $desiredValue)
			{
				if ($result == $desiredValue) {
					$test_passfail = '<font color="green">PASS</font>';
				} else {
					$test_passfail = '<font color="red">FAIL</font>';
				}
				$this->results = $this->results.'<b>'.$name.'</b> → ' . $test_passfail . ': <font color="blue">' . shorten($result) . '</font>. Should be: "' . $desiredValue . '". → ' . $test_passfail . '<br>';
			}
			public function testMatches($name, $result, $pattern)
			{
				if (preg_match('/'.$pattern.'/',$result)) {
					$test_passfail = '<font color="green">PASS</font>';
				} else {
					$test_passfail = '<font color="red">FAIL</font>';
				}
				$this->results = $this->results.'<b>'.$name.'</b> → ' . $test_passfail . ': <font color="blue">' . shorten($result) . '</font>. Should match pattern: "' . $pattern . '". → ' . $test_passfail . '<br>';
			}
			public function testLcMatches($name, $result, $pattern)
			{
				if (preg_match('/'.$pattern.'/i',$result)) {
					$test_passfail = '<font color="green">PASS</font>';
				} else {
					$test_passfail = '<font color="red">FAIL</font>';
				}
				$this->results = $this->results.'<b>'.$name.'</b> → ' . $test_passfail . ': <font color="blue">' . shorten($result) . '</font>. Should match pattern: "' . $pattern . '". → ' . $test_passfail . '<br>';
			}
			public function results()
			{
				return $this->results;
			}
		}
	}
	#Main actions
	{
		function showDocumentation() {
			createHtmlPage("Ember","<style>table, th {border:1px solid;}tr, td {border:1px dotted;}</style>");
			echo '<h1>Data formats</h1>
			<table>
			<tr><th>Format</th><th>Format code</th><th>Filename Pattern</th><th>Read</th><th>Write</th><th>Notes</th></tr>';
			global $formats;
			foreach($formats as $format=>$traits) {
				echo "<tr>";
				echo "<td>".$traits[0]."</td>";
				echo "<td>".$format."</td>";
				echo "<td>".$traits[1]."</td>";
				echo "<td>".$traits[2]."</td>";
				echo "<td>".$traits[3]."</td>";
				echo "<td>".$traits[4]."</td>";
				echo "</tr>";
			}
			echo '</table>';
			endHtmlPage();
		}
		function showHelloWorld() {
			$format = rq('format',true);
			$helloWorld = getHelloWorld($format);
			#help from http://webdesign.about.com/od/php/ht/force_download.htm
			$filename = 'HelloWorld_'.$format.'_Generated'.date('c');
			$extension = getExtension($format);
			$length = strlen($helloWorld);
			header("Content-disposition: attachment; filename=".$filename.$extension);
			header("Content-type: application/octet-stream");
			header("Content-length: ".$length);
			echo $helloWorld;
		}
		function showTests() {
			createHtmlPage("Ember: Tests");
			echo '<div style="white-space:nowrap;">';
			echo 'Test results:<br>';
			echo runTests();
			echo '</div>';
			endHtmlPage();
		}
		function showWelcomePage() {
			createHtmlPage();
			echo 'Welcome to Ember.<br>';
			endHtmlPage();
		}
	}
}

# 3. Determine what I'm supposed to do
{
	$action = rq('action');
	if($action instanceof Exception) {
		showWelcomePage();
	}
	else {
		switch($action) {
			case "showDocumentation":
				showDocumentation();
				break;
			case "showHelloWorld":
				showHelloWorld();
				break;
			case "showIndex":
				showIndex();
				break;
			case "showTests":
				showTests();
				break;
			case "showWelcomePage":
				showWelcomePage();
				break;
			default:
				resetEmber();
		}
	}
}
?>