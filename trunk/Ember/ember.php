<?php

# 0. Header and setup
{
	# 2015mar20 and 2015mar20a21

	$emberVersion = "1.0.45";

	#based on the other ember.php, version 8-0.91.44
	error_reporting(E_ALL);
	ini_set("display_errors",1);
	
	$formats = array(
		"dc" => array("Ember Document Format ASCII Dc List","EDF Dc List","*.edc","No","No","This is Ember's native \"pivot\" format. XML"),
		"edf_latest" => array("Ember Document Format, latest version (updates). Currently an alias of edf_1_0_44.","Ember Document Format","*.edf","No","No","No notes at this time"),
		"ascii" => array("ASCII text","Legacy text encodings","*.txt","Partial","No","No notes at this time"),
		"asciilatin" => array("ASCII text, Latin letters subset","Legacy text encodings","*.txt","Partial","No","No notes at this time"),
		"data" => array("Uninterpreted binary data, in octets","Data","*","No","No","Raw binary data cannot be read or written that is not a multiple of 8 bytes"),
		"edf_1_0_43" => array("Ember Document Format, old, incompatible file format specified in <i>Ember</i> version 1.0.43","EDF 1.0.43 Legacy","*.edf","No","Partial","No notes at this time"),
		"edf_1_0_44" => array("Ember Document Format, current version specified in <i>Ember</i> version 1.0.44","Ember Document Format","*.edf","No","Partial","No notes at this time"),
		
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
		
		#String functions
		{
		
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
		
			function formatFilename($name,$format) {
				global $formats;
				if(array_key_exists($format,$formats)) {
					$formatData = $formats[$format];
					return str_replace($formatData[2],"*",$name);
				}
				return new Exception('Unknown format');
			}
		
			function html_sanitize($data) {
				return htmlspecialchars($data);
			}
		}
		
		#Database functions
		{
			class Db {
				#partly based on FractureDB
				function query($query)
				{
					#Make sure $this->databaseName is set
					$this->databaseName = $this->databaseName;
					$dbh = $this->db;
					$this->queryCount++;
					$result = $dbh->prepare($query);
					$result->execute();
					if (stripos($query, 'INSERT') === 0) {
						return 'Inserted';
					}
					if (stripos($query, 'UPDATE') === 0) {
						return 'Updated';
					}
					return $result->fetchAll(PDO::FETCH_ASSOC);
				}
				
				function getTable($tableName) {
					$query = 'SELECT * FROM ' . $tableName . ' ORDER BY id;';
					return $this->query($query);
				}
				
				function displayTable($tableName,$regionIdentifier) {
					$data = $this->getTable($tableName);
					echo '<a href="ember.php?action='.rq('action').'&table='.rq('table').'&editTable=true">Edit table</a>';
					foreach($data as $index=>$row) {
						echo "<tr>";
						foreach($row as $columnName=>$value) {
							echo '<td class="'.$regionIdentifier.'_'.$columnName.'">'.$value."</td>";
						}
						echo "</tr>";
					}
				}

				function editTable($tableName,$regionIdentifier) {
					#partly based on discosync
					$data = $this->getTable($tableName);
					echo '<a href="ember.php?action='.rq('action').'&table='.rq('table').'">Done editing</a> | <a href="ember.php?action=addRowsToTableAPI&db='.$this->databaseName.'&copyRows=true&numberOfRows=5&table='.rq('table').'">Add 5 rows (will be copied from last row)</a> | <a href="ember.php?action=addRowsToTableAPI&db='.$this->databaseName.'&copyRows=false&numberOfRows=5&table='.rq('table').'">Add 5 blank rows</a>';
					foreach($data as $index=>$row) {
						echo "<tr>";
						$i = 0;
						foreach($row as $columnName=>$value) {
							echo '<td class="'.$regionIdentifier.'_'.$columnName.'"><input type="text" id="'.$regionIdentifier.'_row'.$row['id'].'_'.$columnName.'" onkeypress="sync_'.$regionIdentifier.'_row'.$row['id'].'_'.$columnName.'.call(this,event);" value="'.html_sanitize($value).'"></td>';
							$nextSiblings = str_repeat('.nextSibling',$i*2);
							echo getSyncFunction($this->databaseName,$tableName,$row['id'],$columnName,$regionIdentifier.'_row'.$row['id'].'_'.$columnName,$nextSiblings);
							$i++;
						}
						echo "</tr>";
					}
				}
				
				function updateDatabaseField($table,$row,$column,$value) {
					#help from http://www.w3schools.com/sql/sql_update.asp
					echo $this->query('UPDATE '.$table.' SET '.$column.'='.$this->db->quote($value).' WHERE id=\''.$row.'\';');
				}
				
				function addRowsToTable($table,$copyRows,$numberOfRows) {
					$data = $this->getTable($table);
					$rowToCopy = end($data);
					$nextRowID = $rowToCopy['id']+1;
					$i = 0;
					while($i < $numberOfRows) {
						if($copyRows == "true") {
							$this->query("INSERT INTO ".$table." (id) VALUES ('".$nextRowID."');");
							foreach($rowToCopy as $column=>$value) {
								if($column !== "id") {
									$this->query('UPDATE '.$table.' SET '.$column.'='.$this->db->quote($value).' WHERE id=\''.$nextRowID.'\';');
								}
							}
						}
						if($copyRows == "false") {
							#help from http://stackoverflow.com/questions/13605208/how-to-insert-an-empty-line-to-sql-table
							#$this->query("INSERT INTO ".$table." DEFAULT VALUES;");
							$this->query("INSERT INTO ".$table." (id) VALUES ('".$nextRowID."');");
						}
						$nextRowID++;
						$i++;
					}				
					echo 'Done!';	
				}
				function close() {
					$this->db = null;
				}
			}
			class SqliteDb extends Db {
				#help from http://www.if-not-true-then-false.com/2012/php-pdo-sqlite3-example/?PageSpeed=noscript
				function __construct($name) {
					$this->db = new PDO('sqlite:'.$name);
					$this->databaseName = $name;
					$this->queryCount = 0;
					#Help from Sammitch in IRC 2015mar20
					$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				}
			}
			
			function getDbObjectByName($databaseName) {
				switch($databaseName) {
					case 'edf.sqlite':
						return new SqliteDb($databaseName);
						break;
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
		class Document {
			/*
			Hello_World!:

<?xml version="1.0" encoding="ASCII"?>
<dcStructure id="XX">
	<dc57/><dc86/><dc93/><dc93/><dc96/><dc80/><dc72/><dc96/><dc99/><dc93/><dc85/><dc19/>
</dcStructure id="YY">

			*/
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
			
			if($sourceFormat == 'asciilatin' && $targetFormat == 'dc') {
				return convert_asciilatin_to_dc($data);
			}

			return new Exception('Unknown data conversion pair');
		}
		
		#Parsers: X to Dc converters
		{
			function convert_asciilatin_to_dc($data) {
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
		function getTableStyle() {
			return '<style>table, th {border:1px solid;}input { width:100%; } tr, td {border:1px dotted;} .highlightedCell, .dcreference_name { background-color:#FFFFCC; }</style>';
		}
		function getSyncFunction($database,$table,$row,$column,$identifier,$nextSiblings) {
			#partly based on discosync
			#http://stackoverflow.com/questions/12407093/focus-the-next-input-with-down-arrow-key-as-with-the-tab-key			
			return '<script type="text/javascript">
				function sync_'.$identifier.'(e) {
					if (e.keyCode==40) {
						var node = this.parentNode.parentNode.nextSibling.firstChild'.$nextSiblings.'.firstChild;
						//         inpu td         tr         next tr     first td    next td         input
						node.focus();
						node.select();
					}
					if (e.keyCode==38) {
						var node = this.parentNode.parentNode.previousSibling.firstChild'.$nextSiblings.'.firstChild;
						//         inpu td         tr         prev tr         first td    next td         input
						node.focus();
						node.select();
					}
					setTimeout(function () {   var elementToSync = document.getElementById("'.$identifier.'").innerHTML;
					var elementToSync = document.getElementById("'.$identifier.'").value; var xmlhttp; if (window.XMLHttpRequest) { xmlhttp=new XMLHttpRequest(); } else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); } 
					//help from http://stackoverflow.com/questions/18251399/why-doesnt-encodeuricomponent-encode-sinlge-quotes-apostrophes
					var send="action=updateDatabaseFieldAPI&db='.$database.'&dataTargetTable='.$table.'&dataTargetRow='.$row.'&dataTargetColumn='.$column.'&dataValue="+encodeURIComponent(elementToSync).replace(/[!\'()*]/g, escape);
					xmlhttp.open("POST","ember.php",true); xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded"); xmlhttp.send(send); }, 100); 
            	} 
				</script>';
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
		function showWelcomePage() {
			createHtmlPage();
			echo '<center><h1>Welcome to Ember.</h1><br>
			<ul>
			<li><a href="ember.php?action=showDocumentation">Documentation</a></li>
			<li><a href="ember.php?action=showTests">Run and display tests</a></li>
			<li><a href="ember.php?action=showHelloWorld&format=edf_1_0_44">Generate Hello World! demo file</a></li>
			</ul></center>';
			endHtmlPage();
		}
		#Documentation
		{
			function showDocumentation() {
				createHtmlPage("Ember",getTableStyle());
				echo '<h1>Data formats</h1>
				<p>Most significant entries listed at the beginning of the table; other entries sorted by type and then alphabetically</p>
				<table>
				<tr><th>Format</th><th>Class</th><th class="highlightedCell">Format code</th><th>Filename Pattern</th><th>Read</th><th>Write</th><th>Notes</th></tr>';
				global $formats;
				foreach($formats as $format=>$traits) {
					echo "<tr>";
					echo "<td>".$traits[0]."</td>";
					echo "<td>".$traits[1]."</td>";
					echo "<td class=\"highlightedCell\">".$format."</td>";
					echo "<td>".$traits[2]."</td>";
					echo "<td>".$traits[3]."</td>";
					echo "<td>".$traits[4]."</td>";
					echo "<td>".$traits[5]."</td>";
					echo "</tr>";
				}
				echo '</table>';
				echo '<h1>Dc Reference</h1>
				<br>
				<ul>
				<li><a href="ember.php?action=showDceTable&table=dcs">List of Dcs</a></li>
				<li><a href="ember.php?action=showDceTable&table=encodings">List of encodings, with mappings to and from Dcs</a></li>
				</ul>';
				endHtmlPage();
			}
			function showDceTable() {
				$db = new SqliteDb('edf.sqlite');
				createHtmlPage("Ember",getTableStyle());
				switch(rq('table')) {
					case 'dcs':
						echo '<h1>Dc Reference</h1>';
						echo '<table id="dcreferenceTable">';
						echo '<tr><th>Dc ID</th><th>Glyph</th><th>U+</th><th class="highlightedCell" style="padding-left:50px !important;padding-right:50px !important;">Name</th><th style="padding-left:10px !important;padding-right:10px !important;">Type</th><th style="padding-left:10px !important;padding-right:10px !important;">Script</th><th><small><small>Sort following<br><small>(blank: previous)</small></small></small></th><th>Decomp.</th><th>Depr.</th><th>Description</th><th>Syntax</th><th>Other names</th></tr>';
						if(rq('editTable',true) == 'true') {
							echo $db->editTable("dcs","dcreference");
						}
						else {
							echo $db->displayTable("dcs","dcreference");
						}
						echo '</table>';
						break;
					case 'encodings':
						echo '<h1>Data for known encodings</h1>';
						break;
				}
				endHtmlPage();
				$db->close();
			}
		}
		#Testing
		{
			function showTests() {
				createHtmlPage("Ember: Tests");
				echo '<div style="white-space:nowrap;">';
				echo 'Test results:<br>';
				echo runTests();
				echo '</div>';
				endHtmlPage();
			}
			function showHelloWorld() {
				$format = rq('format',true);
				$helloWorld = getHelloWorld($format);
				#help from http://webdesign.about.com/od/php/ht/force_download.htm
				$filename = 'HelloWorld_'.$format.'_Generated'.date('c');
				$filename = formatFilename($filename,$format);
				$length = strlen($helloWorld);
				header("Content-disposition: attachment; filename=".$filename);
				header("Content-type: application/octet-stream");
				header("Content-length: ".$length);
				echo $helloWorld;
			}
		}
		#Utility
		{
			function updateDatabaseFieldAPI() {
				$db = getDbObjectByName(rq('db'));
				$db->updateDatabaseField(rq('dataTargetTable'),rq('dataTargetRow'),rq('dataTargetColumn'),rq('dataValue'));
			}
			function addRowsToTableAPI() {
				$db = getDbObjectByName(rq('db'));
				$db->addRowsToTable(rq('table'),rq('copyRows'),rq('numberOfRows'));
			}
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
			case "showIndex":
				showIndex();
				break;
			case "showWelcomePage":
				showWelcomePage();
				break;
			#Documentation
			case "showDocumentation":
				showDocumentation();
				break;
			case "showDceTable":
				showDceTable();
				break;
			#Testing
			case "showTests":
				showTests();
				break;
			case "showHelloWorld":
				showHelloWorld();
				break;
			#Utility
			case "updateDatabaseFieldAPI":
				updateDatabaseFieldAPI();
				break;
			case "addRowsToTableAPI":
				addRowsToTableAPI();
				break;
			default:
				resetEmber();
		}
	}
}
?>