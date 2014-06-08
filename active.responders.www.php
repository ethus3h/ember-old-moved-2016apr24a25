<?php


#Arcmaj3
function arcmaj3_wint()
{
    $main_console = new FluidActive('Arcmaj3 statistics web console');
    $db           = new FractureDB('futuqiur_arcmaj3');
    #$main_console->DBTextEntry($db, 'am_urls', 'location', 0);
    #$main_console->DBRowEntry($db, 'am_urls', 1);
    #$main_console->DBTableEntry($db, 'am_urls');
    #$main_console->DBRowEntry($db, 'am_urls', 1);
    #$main_console->getQueryCount($db);
    $allProjects  = $db->getTable('am_projects');
    $main_console->append('<small>');
    $main_console->append('<big><b>Overall</b></big><br>');
    $total     = $db->countTable('am_urls');
    $total     = $total[0];
    $total     = $total['COUNT(*)'];
    #print_r($total);
    $crawled   = $db->countRows('am_urls', ' WHERE barrel != \'0\'');
    $crawled   = $crawled[0];
    $crawled   = $crawled['COUNT(*)'];
    #print_r($crawled);
    $remaining = $total - $crawled;
    if ($total == 0) {
        $pcCr = 0;
        $pcRm = 0;
    } else {
        $pcCr = round(($crawled / $total) * 100, 2);
        $pcRm = round(($remaining / $total) * 100, 2);
    }
    $main_console->append('Total URLs: ' . $total . '<br>');
    $main_console->append('Remaining URLs: ' . $remaining . ' (' . $pcRm . '%)<br>');
    $main_console->append('Crawled URLs: ' . $crawled . ' <b>(' . $pcCr . '%)</b><br>');
    foreach ($allProjects as $index => $data) {
        $main_console->append('<br><br><big><b>Project: ' . $data['urlPattern'] . ' (ID ' . $data['id'] . ')</b></big><br>');
        $total     = $db->countTable('am_urls', 'project', $data['id']);
        $total     = $total[0];
        $total     = $total['COUNT(*)'];
        #print_r($total);
        $crawled   = $db->countTable('am_urls', 'project', $data['id'], ' AND barrel != \'0\'');
        $crawled   = $crawled[0];
        $crawled   = $crawled['COUNT(*)'];
        #print_r($crawled);
        $remaining = $total - $crawled;
        if ($total == 0) {
            $pcCr = 0;
            $pcRm = 0;
        } else {
            $pcCr = round(($crawled / $total) * 100, 2);
            $pcRm = round(($remaining / $total) * 100, 2);
        }
        $main_console->append('Total URLs: ' . $total . '<br>');
        $main_console->append('Remaining URLs: ' . $remaining . ' (' . $pcRm . '%)<br>');
        $main_console->append('Crawled URLs: ' . $crawled . ' <b>(' . $pcCr . '%)</b><br>');
    }
    $main_console->append('</small>');
    $main_console->close();
    $db->close();
}
function arcmaj3_adm()
{
    $main_console = new FluidActive('Arcmaj3 management web console');
    #handler=1&handlerNeeded=arcmaj3&amtask=expireBarrel&verd=2&barrelId=117
    $main_console->append('Expire old barrels (>1wk out): <form action="active.php"><input type="hidden" name="verd" value="2"><input type="hidden" name="amtask" value="expireOldBarrels"><input type="hidden" name="handlerNeeded" value="arcmaj3"><input type="hidden" name="handler" value="1"><input type="submit"></form><br><br>
Expire a barrel:<br><form action="active.php"><input type="hidden" name="verd" value="2"><input type="hidden" name="amtask" value="expireBarrel"><input type="hidden" name="handlerNeeded" value="arcmaj3"><input type="hidden" name="handler" value="1">Barrel ID:<input type="text" name="barrelId"><input type="submit"></form><br><br>
Add a URL:<br><form action="active.php"><input type="hidden" name="verd" value="2"><input type="hidden" name="amtask" value="addUrl"><input type="hidden" name="handlerNeeded" value="arcmaj3"><input type="hidden" name="handler" value="1">New URL:<input type="text" name="amNewUrl"><input type="submit"></form><br><br>
Add a new project:<br><form action="active.php"><input type="hidden" name="verd" value="2"><input type="hidden" name="amtask" value="addProject"><input type="hidden" name="handlerNeeded" value="arcmaj3"><input type="hidden" name="handler" value="1">Filter pattern:<input type="text" name="amFilterPattern">Seed URL:<input type="text" name="amSeedUrl"><input type="submit"></form>');
    $main_console->close();
}
function fluid_demo()
{
    $main = new FluidActive('Fluid//Active Demo Page. Background image: 4278145217_f6f7e5f871_o.jpg: by Casey Yee. CC Attribution Share-Alike 2.0.');
    $main->close();
}
function ember()
{

    $main = new FluidActive('Ember');
	$main->write(file_get_contents("ember.fluidScriptedUI"));
    $main->close();

}
function ember_dev()
{

    $main = new FluidActive('Ember (development version)');
	$main->write(file_get_contents("ember.dev.fluidScriptedUI"));
    $main->close();

}
function calendarsync()
{
	global $classesAuthKey;
	if ((!isset($_SERVER['PHP_AUTH_USER'])) || ($_SERVER['PHP_AUTH_PW'] != $classesAuthKey)) {
		header('WWW-Authenticate: Basic realm="Schedule"');
		header('HTTP/1.0 401 Unauthorized');
		echo 'DOOM';
		exit;
    }
    else {
		$main = new FluidActive('calendarsync','Schedule');
		$main->append('<div style="z-index:2001;left:10px;position:fixed;top:10px;background:#F954A2;"><b>what</b>: What the event/project is. <b>begin</b>: When it begins. <b>end</b>: When it ends or is due. <b>location</b>: Where it happens. <b>notes</b>: e.g. assignment details.</div>');
		//print('what: What the event/project is. begin: When it begins. end: When it ends or is due. location: Where it happens. notes: e.g. assignment details.');
		$db = new FractureDB('futuqiur_calendarsync');
		$table  = $db->getTable('data');

		//$main->append('</script>');
		$main->DBTableEntry($db, 'data');
		//$main->append('<script type="text/javascript">');
		$main->close();
	}
}
function discosync()
{
	global $classesAuthKey;
	if ((!isset($_SERVER['PHP_AUTH_USER'])) || ($_SERVER['PHP_AUTH_PW'] != $classesAuthKey)) {
		header('WWW-Authenticate: Basic realm="DOOM"');
		header('HTTP/1.0 401 Unauthorized');
		echo 'DOOM';
		exit;
    }
    else {
		$main = new FluidActive('discosync','Discography');
      $main->append('<div style="background-color:#A8FFEC;left:50px;top:50px;bottom:50px;right:50px;position:fixed;overflow-x:scroll;overflow-y:scroll;z-index:1998;text-align:center;"> ');
		$main->append('<!-- <div style="z-index:2001;left:10px;position:fixed;top:10px;background:#F954A2;"><b>what</b>: What the event/project is. <b>begin</b>: When it begins. <b>end</b>: When it ends or is due. <b>location</b>: Where it happens. <b>notes</b>: e.g. assignment details.</div> -->');
		//print('what: What the event/project is. begin: When it begins. end: When it ends or is due. location: Where it happens. notes: e.g. assignment details.');
		$db = new FractureDB('futuqiur_calendarsync');
		//$music  = $db->getTable('music');

		//$main->append('</script>');
		$main->DBTableEntry($db, 'releases');
		$main->DBTableEntry($db, 'works');
		$main->DBTableEntry($db, 'versions');
		$main->DBTableEntry($db, 'artist');
		$main->DBTableEntry($db, 'label');
		$main->DBTableEntry($db, 'medium');
		//$main->append('<script type="text/javascript">');
		$main->close();
	}
}
function discography()
{
	$main = new FluidActive('discography','Releases');
  $main->append('<div style="background-color:#FFFFFF;text-align:left;left:0px;top:0px;bottom:0px;right:0px;padding-left:8px;padding-right:8px;padding-top:8px;padding-bottom:8px;position:fixed;overflow-x:scroll;overflow-y:scroll;z-index:1998;"> ');
	//$main->append('<!-- <div style="z-index:2001;left:10px;position:fixed;top:10px;background:#F954A2;"><b>what</b>: What the event/project is. <b>begin</b>: When it begins. <b>end</b>: When it ends or is due. <b>location</b>: Where it happens. <b>notes</b>: e.g. assignment details.</div> -->');
	//print('what: What the event/project is. begin: When it begins. end: When it ends or is due. location: Where it happens. notes: e.g. assignment details.');
	$db = new FractureDB('futuqiur_calendarsync');
	if($_REQUEST["action"] == "releases") {
		$intro = <<<'EOD'
<h1>Releases</h1>

Notes:<br>

<ol>

  <li>The horizontal lines between blocks of releases indicate major
changes in style/ability etc.</li>
  <li>Sometimes, italicization of an attribute of an item (<span style="font-style: italic;">i. e.</span>, one of the comments
regarding a list entry) has been used to distinguish items within the
list in which italicization holds some form of semantic meaning, or in
which for some reason italicization would be undesirable.</li>
  <li>This list uses Unicode to display its content, and assumes that
support for its characters is fairly complete in your computer.</li>
  <li>This list assumes the presence of the following specialty
typefaces in your computer for displaying text in Anoé (<small><span style="font-family: AnoeyFuturamerlincom;">anoé</span></small>/<small><small><small><span style="font-family: AnoeyTuinelanFuturamerlincom;">anoé</span></small></small></small>)
and Japanese, and for displaying other unusual characters:</li>
  <ul>
    <li><a href="http://futuramerlin.com/d/s/typefaces/AnoeyFuturamerlincom2.61.ttf">AnoeyFuturamerlincom</a>
(version 2.61 or later)<br>
    </li>
    <li><a href="http://futuramerlin.com/d/s/typefaces/AnoeyTuinelanFuturamerlincom1.1.ttf">AnoeyTuinelanFuturamerlincom</a>
(version 1.1 or later)</li>
  </ul>
  <li>In addition, the following Wreathe typeface family might be
useful, with the&nbsp; useful components in bold (the rest are provided
for completeness) — using these typefaces will provide rudimentary
support for all of Unicode with the exceptions of Kaithi, Sora Sompeng,
Chakma, Sharada, Miao, and mathematical symbols for Arabic:</li>
  <ul>
    <li><a style="font-weight: bold;" href="http://futuramerlin.com/d/s/typefaces/WreatheR.ttf">Wreathe</a>
(version 2.6F provided) (the main typeface) and its variants:<br>
    </li>
    <ul>
      <li><a style="font-weight: bold;" href="http://futuramerlin.com/d/s/typefaces/WreatheI.ttf">Wreathe
Italic</a> (version 2.0G provided) (Italic)</li>
    </ul>
    <ul>
      <li><a style="font-weight: bold;" href="http://futuramerlin.com/d/s/typefaces/WreatheBoldItal.ttf">Wreathe
Bold Italic</a> (version 1 provided) (Bold Italic)</li>
    </ul>
    <ul>
      <li><a href="http://futuramerlin.com/d/s/typefaces/WreatheM.ttf">WreatheM</a>
(version 1 provided) (A variant of the Roman style)</li>
    </ul>
    <ul>
      <li><a href="http://futuramerlin.com/d/s/typefaces/WreatheDecorative1.11.ttf">Wreathe
Decorative</a> (version 1.11 provided) (A decorative version)</li>
    </ul>
    <ul>
      <li><a href="http://futuramerlin.com/d/s/typefaces/WreatheSC.ttf">Wreathe
SC</a> (version 1 provided) (Small capitals)</li>
    </ul>
    <li><a style="font-weight: bold;" href="http://futuramerlin.com/d/s/typefaces/WreatheUnicode3.14.2.1.ttf">Wreathe
Unicode</a> (version 3.14.2.1 provided) (a pan-Unicode typeface)</li>
    <li><a style="font-weight: bold;" href="http://futuramerlin.com/d/s/typefaces/WreatheHanAJP1.0.ttf">Wreathe
Han A JP</a> (version 1.0 provided) (a Han Unicode typeface)</li>
    <li><a style="font-weight: bold;" href="http://futuramerlin.com/d/s/typefaces/WreatheHanBJP1.0.ttf">Wreathe
Han B JP</a> (version 1.0 provided) (a Han Unicode typeface)</li>
  </ul>
  <li>Many of the computer files provided herein are in proprietary
formats that
may prove challenging to open. At some point I plan to provide all the
files reëncoded using the Free Lossless Audio Codec, but I have not yet
done that.</li>
  <li>The years of composition on some of the track listings shown in
the artwork are often approximate.</li>
  <li>Unless otherwise noted, the first image in the album artwork is
the cover.</li>
  <li>Unless otherwise noted, each piece was composed, performed, and
produced by Elliot Chandler Wallace.</li>
  <li>How catalogue numbers work: The "Original series" catalogue
numbers are the numbers assigned by the data HTML file corresponding to
the release. The "New series" catalogue numbers are numbers assigned by
artist, in the format <span style="font-style: italic;">x</span>-<span style="font-style: italic;">n</span>, where <span style="font-style: italic;">x</span> is the artist ID, and <span style="font-style: italic;">n</span> is the
release ID (a rough estimate of the chronological order of the releases
was used to establish the ordering of release IDs). For example,
Futuramerlin.com is artist 6, so the first release under that artist
name, the 17-track edition of Sylvan Fantasy, has the new series number
6-1. The "ID" is the new (assignments begun 8 October 2012) release
numbering system, designed to not conflict with either earlier system. <br>
  </li>
  <li>Key:</li>
  <ol>
    <li>Availability:</li>
    <ol>
      <li>✓ = This release is now available</li>
    </ol>
    <li>Audio:</li>
    <ol>
      <li>(👂) = lossy, low quality, or partial CD/DVD/cassette
rips or audio files provided<br>
      </li>
      <li>👂 = complete lossless audio or archival quality
CD/DVD/cassette rips
provided</li>
    </ol>
    <li>Source audio files:</li>
    <ol>
      <li>(🎧) = low quality or partial source audio files provided</li>
      <li>🎧 = complete source audio files provided<br>
      </li>
    </ol>
    <li>Artwork:</li>
    <ol>
      <li>(👁) =
lossy, poor-quality, or partial artwork provided</li>
      <li> 👁 =
complete lossless artwork provided (including, for digital media, any
artwork embedded in the release data)<br>
      </li>
    </ol>
    <li>Artwork scans:</li>
    <ol>
      <li>(📷) = lossy, low quality, or partial artwork scans provided</li>
      <li>📷 = complete archival quality artwork scans provided</li>
    </ol>
    <li>Source artwork files:</li>
    <ol>
      <li>(📄) = low quality or partial source artwork files provided</li>
      <li>📄 = complete source artwork files (including media artwork
source files) provided</li>
    </ol>
    <li>Media images:</li>
    <ol>
      <li>(💿) = lossy, low quality, or partial media images (scans or
photographs) provided</li>
      <li>💿 = complete archival quality media images (scans or
photographs) provided</li>
    </ol>
    <li>Video:</li>
    <ol>
      <li>(🎥) = lossy, low quality, or partial video files provided</li>
      <li>🎥 = complete archival quality video files provided</li>
    </ol>
    <li>Source video files:</li>
    <ol>
      <li>(🎬) = low quality or partial source video files provided</li>
      <li>🎬 = complete archival quality source video files provided</li>
    </ol>
    <li>ZIP files</li>
    <ol>
      <li>ZIP = ZIP files provided (only for digital releases
originally released as ZIP files)</li>
    </ol>
    <li>CD images</li>
    <ol>
      <li>DD = CD or other media images (binary copies) provided<br>
      </li>
    </ol>
  </ol>
</ol>

<br>

͠
<br>
EOD;
		$main->append($intro);

	$releases = $db->getOrderedRows('releases', 'indexdate DESC, fineindex DESC');
	print_r($releases);
	$main->append('<ul>');
	
	        foreach ($releases as $key => $value) {
	        	if(strlen($value['title'])>0){
	        		$title=hex2bin($value['title']);
	        	}
	        	else {
	        		$title="(link)";
	        	}
	        	if(strlen($value['annotation'])>0){
	        		$annotation=' ('.hex2bin($value['annotation']).') ';
	        	}
	        	else {
	        		$annotation="";
	        	}
	        	if(strlen($value['date'])>0){
	        		$date=' ('.hex2bin($value['date']).') ';
	        	}
	        	else {
	        		$date="";
	        	}
	        	if($value['medium']>0){
	        		$medium=' ('.hex2bin($db->getField('medium', 'name', $value['medium']));
	        		if($value['mediumquestioned'] == 1) {
	        			$medium = $medium.'?) ';
	        		}
	        		else {
	        			$medium = $medium.') ';
	        		}
	        	}
	        	else {
	        		$medium="";
	        	}
	        	if($value['va'] == 1) {
	        		$artist = 'multiple primary artists credited';
	        	}
	        	else {
	        		if(strlen($value['artist']>0)){
	        			$artist = '<i>as </i><a href="/d/r/active.php?wint=1&wintNeeded=discography&action=artist&artistid='.$value['artist'].'">'.hex2bin($db->getField('artist', 'name', $value['artist'])).'</a>';
					}
					else {
						if($value['uncredited'] == 1) {
							$artist = '<i>no primary artist credited</i>';
						}
						else {
							$artist="<i>artist credit unknown</i>";
						}
					}
	        	}
	        	$main->append('<li><a href="/d/r/active.php?wint=1&wintNeeded=discography&action=release&relid='.$value['id'].'">'.$title.'</a>'.$annotation.$date.$medium.'<i>(</i>'.$artist.'<i>)</i></li>');
	        	if($value['innovation'] == 1) {
	        		$main->append('<hr>');
	        	}
	        }
	$main->append('</ul>');

	}
	//$music  = $db->getTable('music');
	//$main->append('<!-- <div style="z-index:2001;left:10px;position:fixed;top:10px;background:#F954A2;"><b>what</b>: What the event/project is. <b>begin</b>: When it begins. <b>end</b>: When it ends or is due. <b>location</b>: Where it happens. <b>notes</b>: e.g. assignment details.</div> -->');

	//$main->append('</script>');
// 	$main->DBTableEntry($db, 'releases');
// 	$main->DBTableEntry($db, 'works');
// 	$main->DBTableEntry($db, 'versions');
// 	$main->DBTableEntry($db, 'artist');
// 	$main->DBTableEntry($db, 'label');
	//$main->append('<script type="text/javascript">');
	$main->close();
	
}
function coaltestupload()
{
    $authorizationKey = $_REQUEST['authorizationKey'];
    global $generalAuthKey;
    if($authorizationKey == $generalAuthKey) {
		//based partly on http://www.tizag.com/phpT/fileupload.php
// 		global $l;
// 		$l = new llog;
// 		lstore('English',0);
    	echo '<html><head><title>Test coal upload form</title></head><body><form action="active.php" method="post"
enctype="multipart/form-data"><input name="uploadedfile" type="file" /><input type="hidden" name="authorizationKey" value="'.$generalAuthKey.'" /><input type="hidden" name="handler" value="1" /><input type="hidden" name="coalVerbose" value="1" /><input type="hidden" name="handlerNeeded" value="CoalIntake" /><br /><input type="submit" value="Upload File" /></form></body></html>';
	}
}
function coalplistupload()
{
    $authorizationKey = $_REQUEST['authorizationKey'];
    global $generalAuthKey;
    if($authorizationKey == $generalAuthKey) {
		//based partly on http://www.tizag.com/phpT/fileupload.php
    	echo '<html><head><title>Smallify</title></head><body><center><h1>Smallify</h1>Select file:<br><form action="active.php" method="post"
enctype="multipart/form-data"><input name="uploadedfile" type="file" /><input type="hidden" name="authorizationKey" value="'.$generalAuthKey.'" /><input type="hidden" name="handler" value="1" /><input type="hidden" name="outputwebloc" value="1" /><input type="hidden" name="handlerNeeded" value="CoalIntake" /><br /><input type="submit" value="Smallify!" /></form><br>When you want, double-click your Smallified file to expand it.</center></body></html>';
	}
}
function coaltestdownload()
{
    $authorizationKey = $_REQUEST['authorizationKey'];
    global $generalAuthKey;
    if($authorizationKey == $generalAuthKey) {
		//based partly on http://www.tizag.com/phpT/fileupload.php
    	echo '<html><head><title>Test coal download form</title></head><body><form action="active.php" method="get"><input name="coalId" type="text" /><input type="hidden" name="authorizationKey" value="'.$generalAuthKey.'" /><input type="hidden" name="handler" value="1" /><input type="hidden" name="coalVerbose" value="1" /><input type="hidden" name="handlerNeeded" value="CoalRetrieve" /><br /><input type="submit" value="Download File" /></form></body></html>';
	}
}
function inforesource()
{
	$type='unknown';
	if(isset($_REQUEST['type'])) {
		$type = $_REQUEST['type'];
	}
	$tab='overview';
	if(isset($_REQUEST['tab'])) {
		$tab = $_REQUEST['tab'];
	}
	if(isset($_REQUEST['topic'])) {
		$topic = $_REQUEST['topic'];
	}
	else {
		if(isset($_REQUEST['r'])) {
			$topic = str_rot13(base64_decode($_REQUEST['r']));
		}
		else {
			$topic = '';
		}
	}
	$title = titleCase($topic);
	$displayType=titleCase($type) . ' at ';
	if($type == 'unknown') {
		$displayType = '';
	}
	$results = get_info($topic, $type);
	$resultsjs = 'var results = { ';
	//help from http://stackoverflow.com/questions/4329092/multi-dimensional-associative-arrays-in-javascript, http://stackoverflow.com/questions/22815511/foreach-loop-over-multidimensional-associative-array, and http://stackoverflow.com/questions/9833481/key-names-of-associative-arrays
	foreach($results as $key => $value) {
		$resultsjs = $resultsjs . titleCase($key) . ': { ';
		foreach($value as $vkey => $vvalue) {
			$resultsjs = $resultsjs . titleCase($vkey) . ': \'' . base64_encode($vvalue) . '\', ';
		}
		$resultsjs = $resultsjs . '}, ';
	}
	$resultsjs = $resultsjs . '};';
	//print_r($results);
	$title = $title . ' | ' . $displayType . 'Information Resource';
	$main = new FluidActive('inforesource',$title);
	$main->write(file_get_contents("inforesource.fluidScriptedUI"));
	$main->write("\n\n" . $resultsjs . ' 
var initialType = \''.titleCase($type).'\';
var initialTab = \''.titleCase($tab).'\';

//based on http://stackoverflow.com/questions/921789/how-to-loop-through-javascript-object-literal-with-objects-as-members
firsttabcreated = false;
prevkey = \'\';
//Make tabs work like Futuramerlin.com music page?
for (var key in results) {
	if(key.length > 0) {
		//IIRC (which I\'m pretty darn sure I do), "sttodo" meant "string to do", and "sttodp" was the successor of that alphabetically
		var obj = results[key];
		//alert(key);
		var set = new Object();
		if(firsttabcreated == false) {
			set["container"] = this.fub.id;
			alert(\'first tab created\');
			firsttabcreated = true;
		}
		else {
			var sttodo = "set[\"container\"] = this.tab"+prevkey+"Box.id;";
			console.debug(sttodo);
			eval(sttodo);
		}
		set["vpos"] = 3;
		set["vposunit"] = "rem";
		var sttodp = "var tab"+key+"Box = new Box(set);";
		console.debug(sttodp);
		eval(sttodp);
		set=null;
		eval("this.tab"+key+"Box.show(\"none\");");
		for (var prop in obj) {
			// important check that this is objects own property 
			// not from prototype prop inherited
			if(obj.hasOwnProperty(prop)){
				//alert(prop + " = " + obj[prop]);
			}
		}
		prevkey = key;
	}
}



var set = new Object();
set["container"] = this.contentPanel.id;
set["css"] = "overflow-x: scroll; overflow-y: scroll;";
this.pagecontents = new Box(set);
set=null;
this.pagecontents.show("none");

//help from http://stackoverflow.com/questions/2820249/base64-encoding-and-decoding-in-client-side-javascript / https://developer.mozilla.org/en-US/docs/Web/JavaScript/Base64_encoding_and_decoding
this.pagecontents.contents = atob(results[initialType][initialTab]);
console.debug(results[initialType][initialTab]);
this.pagecontents.compute();
console.debug(this.pagecontents);

var set = new Object();
set["container"] = this.Ember.id;
//based on http://stackoverflow.com/questions/3151974/highlight-entire-text-inside-text-field-with-single-click and http://stackoverflow.com/questions/2984311/delete-default-value-of-an-input-text-on-click
set["contents"] = "<span style=\"font-size:1rem;color:#fff;line-height:1rem;font-weight:bold;\"><form><input type=\"hidden\" name=\"wint\" value=\"1\" /><input type=\"hidden\" name=\"wintNeeded\" value=\"inforesource\" /><input type=\"text\" list=\"prefilled\" onblur=\"if (this.value == \'\') {this.value = \''.str_replace('\'','\\\'',$topic).'\';}\" onfocus=\"if (this.value == \''.str_replace('\'','\\\'',$topic).'\') {this.value = \'\';}\" style=\"background-color: rgba(0,0,0,0); border-style: none; text-align:center;font-size:1rem;color:#fff;line-height:1rem;font-weight:bold;\" name=\"topic\" value=\"'.$topic.'\"><datalist id=\"prefilled\"><option value=\"'.$topic.'\"></datalist></form></span>";
set["heighth"] = 3;
set["hunit"] = "rem";
set["vpos"] = 0.75;
set["vposunit"] = "rem";
this.sbform = new Box(set);
set=null;
this.sbform.show("none");

$(window).load(function() {
	Ember.show("zoomhalffade");
});');

    $main->close();
    //Pasting this next block here for archival purposes, lol, nothing to do with this project but I don't really have anywhere better to put it. This was a conversation via join.me (https://join.me/762-915-672 ) hosted by SupaYoshi who asked a question in Freenode's #css. I'm kyan / You@All :)
    /*
    Host@All: So it has a little distance
Host@All: :D
Viewer 1@All: looks better without
Host@All: yes
Host@All: So how do I make it without :)
Host@All: Cus it is width now	
Viewer 1@All: negative margin
Host@All: O.o how to do tha
Viewer 1@All: 1 sec let me load the page
Host@All: sure tyt :O
You@All: Kyan here watching the project :)
Host@All: Awesomeeee :o
Viewer 1@All: Im Blaster btw the guy who's skills pale in comparison to kyan
You@All: I'm not really sure what to do now though, I'm pretty much at the limits of my abilites
You@All: ummm no I'm not that good
You@All: :P thanks though
Host@All: lmao
Viewer 1@All: .input-prepend.input-append .add-on:first-child, .input-prepend.input-append .btn:first-child { margin-right:-4px!important }
Host@All: eh nuh?
Viewer 1@All: is that what you wanted?
Host@All: idk what changed?
Viewer 1@All: little space between input addon
Host@All: eh no that was fine already :D
Host@All: heheh
Host@All: XD
Host@All: i mean this
Viewer 1@All: oh sorry
You@All: Ohhhh!
You@All: I was confused too
Host@All: hehe np :P
Host@All: sorry!
You@All: trying to figure what was going on, LOL
You@All: it's ok :D
Viewer 1@All: #form-login-remember label { margin-left:8px!important; }

    */
	//some js from http://stackoverflow.com/questions/4742746/jquery-open-new-window-on-page-load, http://www.4guysfromrolla.com/demos/OnBeforeUnloadDemo1.htm, http://kbeezie.com/cross-browser-exit-pop/, and http://forums.devarticles.com/javascript-development-22/how-to-stop-browser-from-closing-using-javascript-8458.html
	//some css from http://stackoverflow.com/questions/1150163/stretch-and-scale-a-css-image-in-the-background-with-css-only and http://stackoverflow.com/questions/13367403/background-image-doesnt-cover-entire-screen
	//some code from from active.fluid.php
	//help from http://www.w3schools.com/cssref/css3_pr_background-size.asp
//     echo '<html><head><title>' . $ptitle . '</title><script src="/d/jquery-2.1.0.min.js" type="text/javascript"></script><script language="JavaScript">
//   //window.onbeforeunload = confirmExit;
//   function confirmExit()
//   {
//   	//document.write("<iframe width=\'100%\' height=\'100%\' frameborder=\'0\' src=\'http://futuramerlin.com/d/r/active.php?wint=1&wintNeeded=bnner\' marginwidth=\'0\' marginheight=\'0\' vspace=\'0\' hspace=\'0\' allowtransparency=\'true\' scrolling=\'no\'></iframe>"); 
//     return "Are you sure you want to exit this page?";
//     
//   }
// //from http://stackoverflow.com/questions/7064998/how-to-make-a-link-open-multiple-pages-when-clicked
// $(\'a\').click(function(e) {
// 	//e.preventDefault();
//     window.open(\'http://futuramerlin.com/d/r/active.php?wint=1&wintNeeded=bnner\');
//     
// });
// </script><link type="text/css" rel="stylesheet" href="css/flat-ui.css"/><style type="text/css"> body {background: url(\'4278136735_20329c6cb7_o.jpg\')  no-repeat center center fixed; background-size:cover;} #content { position: fixed; left: 1em; top:1em; right: 1em; bottom: 1em; background-color: rgba(255,255,255,0); overflow-x: scroll; overflow-y:scroll; } </style></head><body><script language="javascript" src="http://chelhi.ptp33.com/pop.php?username=chelhi&max=1"></script><noscript><a href="http://www.paid-to-promote.net/" target="_blank">Paid To Popup</a></noscript><div id="content">
// <div id="header" style=" position: fixed; left: 1em; top:1em; right: 1em; bottom: 1em; background-color: rgba(255,255,255,0.6); "></div>
// <h1>Information on '.$topic.'</h1>'.$results.'<h1>Website attribution</h1><ul><li>Theme: <a href="http://designmodo.github.io/Flat-UI/">Flat UI</a> from <a href="http://designmodo.com">Designmodo</a></li><li>Background image: <a href="http://www.flickr.com/photos/caseyyee/4278136735/">"4278136735_20329c6cb7_o.jpg": by Casey Yee</a>. CC Attribution Share-Alike 2.0.</li><li><a href="http://www.paid-to-promote.net/member/signup.php?r=chelhi" target="_blank"><img src="http://www.paid-to-promote.net/images/ptp.gif" alt="Get Paid To Promote, Get Paid To Popup, Get Paid Display Banner" width="468" height="60" border="0" longdesc="http://www.paid-to-promote.net/" /></a></li></ul></div></body></html>';
} 
function bnner()
{
    echo '<html><head><title></title><link type="text/css" rel="stylesheet" href="css/flat-ui.css"/></head><body><iframe width=\'728\' height=\'90\' frameborder=\'0\' src=\'http://chelhi.ptp33.com/seo.php?username=chelhi&format=728x90\' marginwidth=\'0\' marginheight=\'0\' vspace=\'0\' hspace=\'0\' allowtransparency=\'true\' scrolling=\'no\'></iframe><a href="http://www.paid-to-promote.net/member/signup.php?r=chelhi" target="_blank"><img src="http://www.paid-to-promote.net/images/ptp.gif" alt="Get Paid To Promote, Get Paid To Popup, Get Paid Display Banner" width="468" height="60" border="0" longdesc="http://www.paid-to-promote.net/" /></a></body></html>';
} 
function PhpinfoWint()
{
    $authorizationKey = $_REQUEST['authorizationKey'];
    global $generalAuthKey;
    if($authorizationKey == $generalAuthKey) {
		phpinfo();
	}
    else {
        header("HTTP/1.0 403 Forbidden");
    }
}
function emberPlainWint()
{
$pageTitle = '';
$pageBody = '';
$ember->ui('home',$pageTitle,$pageBody);
$page = new Document_F($pageBody,'',$pageTitle,'@NULL@','../../');
$page->display();

// 	echo '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><style type="text/css" media="all">@font-face{font-family:\'anoeyfuturamerlincommedium\';src:url(\'d/f/anoeyfuturamerlincom2.61.eot\');src:url(\'d/f/anoeyfuturamerlincom2.61.eot?#iefix\') format(\'embedded-opentype\'),url(\'d/f/anoeyfuturamerlincom2.61.woff\') format(\'woff\'),url(\'d/f/anoeyfuturamerlincom2.61.ttf\') format(\'truetype\'),url(\'d/f/anoeyfuturamerlincom2.61.svg#anoeyfuturamerlincommedium\') format(\'svg\');font-weight:normal;font-style:normal}@font-face{font-family:\'wreatherweb\';src:url(\'d/f/wreathe-r.eot\');src:url(\'d/f/wreathe-r.eot?#iefix\') format(\'embedded-opentype\'),url(\'d/f/wreathe-r.woff\') format(\'woff\'),url(\'d/f/wreathe-r.ttf\') format(\'truetype\'),url(\'d/f/wreathe-r.svg#wreatherweb\') format(\'svg\');font-weight:normal;font-style:normal}@font-face{font-family:\'wreatherweb\';src:url(\'d/f/wreathe-b.eot\');src:url(\'d/f/wreathe-b.eot?#iefix\') format(\'embedded-opentype\'),url(\'d/f/wreathe-b.woff\') format(\'woff\'),url(\'d/f/wreathe-b.ttf\') format(\'truetype\'),url(\'d/f/wreathe-b.svg#wreathebold\') format(\'svg\');font-weight:bold;font-style:normal}@font-face{font-family:\'wreatherweb\';src:url(\'d/f/wreathe-i.eot\');src:url(\'d/f/wreathe-i.eot?#iefix\') format(\'embedded-opentype\'),url(\'d/f/wreathe-i.woff\') format(\'woff\'),url(\'d/f/wreathe-i.ttf\') format(\'truetype\'),url(\'d/f/wreathe-i.svg#wreatheitalic\') format(\'svg\');font-weight:normal;font-style:italic}@font-face{font-family:\'wreatherweb\';src:url(\'d/f/wreathe-bi.eot\');src:url(\'d/f/wreathe-bi.eot?#iefix\') format(\'embedded-opentype\'),url(\'d/f/wreathe-bi.woff\') format(\'woff\'),url(\'d/f/wreathe-bi.ttf\') format(\'truetype\'),url(\'d/f/wreathe-bi.svg#wreathebold_italic\') format(\'svg\');font-weight:bold;font-style:italic}html{background:#d5e3cf;background:-webkit-gradient(radial,50% 0,0,50% 0,300,from(#FFF),to(#d5e3cf));background:-moz-radial-gradient(50% 0 90deg,circle farthest-side,#FFF,#d5e3cf,#d5e3cf 31.25em);background-repeat:no-repeat;background-color:#d5e3cf}body{text-align:justify;overflow-x:hidden;min-height:18.75em;}form{display:inline}pre,blockquote,li,table,tbody,tr,td,ul,ol{color:#22520f;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;table-layout:fixed}a{color:#520f22;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:none;-webkit-transition: all 0.8s ease-out;-moz-transition: all 0.8s ease-out;-o-transition: all 0.8s ease-out;transition: all 0.8s ease-out;}a:hover{color:#520f22;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:underline}p{color:#22520f;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;word-wrap:break-word;text-indent:30pt;text-align:justify;margin-top:0;margin-bottom:0}table{border-color:transparent}input{background-color:transparent}h1{color:#22520f;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;font-weight:normal;text-align:center}h2,h3,h4,h5,h6{color:#22520f;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;font-weight:normal;font-style:italic;text-align:center}.t{border:0;background-color:transparent;padding:0;overflow:visible;font-size:1em;color:#520f22;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif}.t:hover{border:0;background-color:transparent;padding:0;overflow:visible;font-size:1em;color:#520f22;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:underline;cursor:pointer;}div.floattb{position:fixed;bottom:0;left:0;width:100%;z-index:3;text-align:center}div.floatbg{left:25px;right:25px;position:fixed;bottom:0;height:25px;z-index:2;opacity:.85;background-color:#f0f0f0}a.floatlink{font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:none}a.floatlink:hover{font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:underline}div.generated-toc{text-align:left;list-style-type:none;position:fixed;bottom:25px;left:35px;width:25%;z-index:2;background-color:#efefef;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:none}div#hideshow{position:fixed;bottom:0;left:-12px;width:10%;z-index:4;text-align:left}div#generated-toc a{font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:none;overflow-y:scroll}div#generated-toc ul{text-indent:-10pt;list-style-type:none;font-size:x-small}a#generated_toc_d_toggle:hover{text-decoration:none}p#toggle-container{text-align:left}div.greenpage{position:absolute;top:0;min-height:18.75em;background-color:transparent;margin:8px;margin-right:8px;z-index:-1}div.fh{left:25px;right:25px;position:absolute;top:25px;height:25px;z-index:100;text-align:center;font-size:large}div.litem{padding:10px;text-align:center}div.smalllink{padding-top:20px;text-align:center;font-size:x-small}div.relative{position:relative;padding-top:0px}.reveal-modal-bg{position:fixed;height:100%;width:100%;background:#000;background:rgba(0,0,0,.8);z-index:100;display:none;top:0;left:0}.reveal-modal{visibility:hidden;top:75px;left:0;margin-left:-10px;width:90%;max-width:900px;background:#eee url(g.png) no-repeat -200px -80px;position:absolute;z-index:101;padding:0;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;-moz-box-shadow:0 0 10px rgba(0,0,0,.4);-webkit-box-shadow:0 0 10px rgba(0,0,0,.4);-box-shadow:0 0 10px rgba(0,0,0,.4)}.reveal-modal .close-reveal-modal{font-size:22px;line-height:.5;position:absolute;top:8px;right:11px;color:#aaa;font-weight:bold;cursor:pointer}div.logobox{margin:auto;display:inline-block;position:relative;height:20%;width:auto;padding-top:16px;padding-left:24px;float:left;padding-right:75px;}div.holder{-webkit-box-shadow: 0px 0px 10px 5px #FFF;box-shadow: 0px 0px 10px 0px #FFF;margin:auto;position:relative;left:25px;width:322px;display:inline-block;}div.caption{color:white;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:none}div.captionbg{position:absolute;bottom:0px;left:0px;width:100%;height:100%;background: rgb(0,0,0);background: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSI5MCUiIHN0b3AtY29sb3I9IiMwMDAwMDAiIHN0b3Atb3BhY2l0eT0iMSIvPgogICAgPHN0b3Agb2Zmc2V0PSI5MCUiIHN0b3AtY29sb3I9IiMwMDAwMDAiIHN0b3Atb3BhY2l0eT0iMSIvPgogICAgPHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSIjNjA2MDYwIiBzdG9wLW9wYWNpdHk9IjEiLz4KICAgIDxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzI2MjYyNiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgPC9saW5lYXJHcmFkaWVudD4KICA8cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIiBmaWxsPSJ1cmwoI2dyYWQtdWNnZy1nZW5lcmF0ZWQpIiAvPgo8L3N2Zz4=");background: -moz-linear-gradient(top, rgba(0,0,0,1) 90%, rgba(0,0,0,1) 90%, rgba(40,40,40,1) 100%, rgba(38,38,38,1) 100%);background: -webkit-gradient(linear, left top, left bottom, color-stop(90%,rgba(0,0,0,1)), color-stop(90%,rgba(0,0,0,1)), color-stop(100%,rgba(40,40,40,1)), color-stop(100%,rgba(38,38,38,1)));background: -webkit-linear-gradient(top, rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background: -o-linear-gradient(top, rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background: -ms-linear-gradient(top, rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background: linear-gradient(to bottom, rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#000000\', endColorstr=\'#282828\',GradientType=0 );opacity:.8;z-index:-1;}img.logo{border-right:1px solid #666;border-left:1px solid #666;border-top:1px solid #666;z-index:105;}div.caption:hover{color:white;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:underline}a.captionlink{color:white;}div#paddingbottom{height:16px;}div#content{position:static;padding-top:55px;padding-bottom:35px;}div#page{position:absolute;left:50%;width:640px;margin-left:-320px;}a:focus,a:active,button,input[type="reset"]::-moz-focus-inner,input[type="button"]::-moz-focus-inner,input[type="submit"]::-moz-focus-inner,select::-moz-focus-inner,input[type="file"] > input[type="button"]::-moz-focus-inner{outline: none !important;}.popuplink{color:white !important;}a#tl{font-size:.93em !important;}</style><title>';
// 	echo $pageTitle;
// 	echo ' — Ember</title></head><body><div class="greenpage"></div><div class="fh"><a href="/" id="tl"><i>futuramerlin</i></a></div><div id="content"><h1></h1><table border="0" cellpadding="24" width="100%"><tbody><tr><td><br><table border="0" width="100%">';
// 	echo $pageBody;
// 	echo '<div class="floatbg"></div><div class="floattb"><a href="/" class="floatlink">Home</a> | <a href="javascript:history.back();" class="floatlink">Previous page</a> | <a href="/r.php?c=news&amp;a=main" class="floatlink">News</a> | <a href="/r.php?c=events&amp;a=main" class="floatlink">Events</a> | <a href="/r.php?c=articles&amp;a=main" class="floatlink">Articles</a> | <div style="display:inline;height:25px;margin-bottom:-4px;"><div style="display:inline-block;z-index:200;" id="projecthoverdiv" onMouseOver="show();" onMouseOut="hide();"><img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="" onMouseOver="forcehide();" style="width:5px;height:30px;margin-left:-5px;margin-bottom:-5px;"><a class="floatlink" id="projectHoverLink" href="r.php?c=main&amp;a=projects">Projects</a><img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="" id="pngstretch" onMouseOver="forcehide();" style="width:5px;height:26px;margin-left:0px;margin-bottom:-5px;"></div></div><noscript><a href="r.php?c=main&amp;a=projects" class="floatlink">Projects</a></noscript></div><div id="projectsdisplaybg" style="position:fixed;bottom:25px;left:100px;width:250px;height:126px;background-color:#030007;opacity:.5;z-index:180;display:none;"><div onMouseOver="persistin();" id="projectsdisplaydiv" style="position:fixed;bottom:26px;left:101px;width:250px;height:126px;opacity:.8;color:white;z-index:200;border:1px dotted white;"><ul style="text-align:left;"><li style="color:white;"><a style="color:white;" class="popuplink" href="r.php?c=Wreathe&amp;a=main">Wreathe</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="r.php?c=Ember&amp;a=main">Ember</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="r.php?c=DCE&amp;a=main">DCE</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="r.php?c=main&amp;a=music">Music</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="r.php?c=main&amp;a=more-projects">More…</a></li></ul></div></div><div id="pt" style="position:fixed;bottom:22px;left:100px;width:250px;height:126px;z-index:180;" onMouseOver="persistin();"></div><div id="triggerout" style="position:fixed;bottom:25px;left:100px;width:282px;height:142px;z-index:102;" onMouseOut="forcehide();"></div><script type="text/javascript" src="d/d.js"></script><div style="position:fixed;bottom:35px;right:10px;z-index:115 !important;"><a href="https://twitter.com/Futuramerlin" class="twitter-follow-button" data-show-count="false" data-dnt="true">Follow @Futuramerlin</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script></div></body></html>';
}
function emberTestWint()
{
	global $l;
	$l = new llog;
	echo '<html><head><title>Ember test suite</title></head><body>';
	$ember = new emInterface();
// 	$pres = array( 'id' => '3', 'csum' => 'Tzo0OiJDc3VtIjo0OntzOjM6ImxlbiI7aTo0O3M6MzoibWQ1IjtzOjMyOiJiNGY5NDU0MzNlYTRjMzY5YzEyNzQxZjYyYTIzY2NjMCI7czozOiJzaGEiO3M6NDA6ImZlMDQ2YTQwODY4OWQwNzA2NmQ1N2VmOTU4YWQ5MGQ4YzMyZjcwMTMiO3M6NDoiczUxMiI7czoxMjg6Ijk0ZGNmOTVhZWNhODBmYmUzZDZmMzQxYzAyY2UzNzg5ZmNkNmNhOGVmNTBkZTliNWM2MTM4YjhmYjg5NTVkNjJhYWEyMjVhODAyODk2MzkwOTU5ZWQxNjg4MTQwMzdhYTEwYTNhMzYxYjVhNTg0NDgxZTI0N2E5MGZiNjIwZTg5Ijt9', 'status' => 0);
// 	test($ember->store('doom',new Csum('doom')),$pres,'Store');
// 	//print_r($ember->retrieve(3));
// 	$prer = array ( 'data' => 'doom', 'csum' => 'Tzo0OiJDc3VtIjo0OntzOjM6ImxlbiI7aTo0O3M6MzoibWQ1IjtzOjMyOiJiNGY5NDU0MzNlYTRjMzY5YzEyNzQxZjYyYTIzY2NjMCI7czozOiJzaGEiO3M6NDA6ImZlMDQ2YTQwODY4OWQwNzA2NmQ1N2VmOTU4YWQ5MGQ4YzMyZjcwMTMiO3M6NDoiczUxMiI7czoxMjg6Ijk0ZGNmOTVhZWNhODBmYmUzZDZmMzQxYzAyY2UzNzg5ZmNkNmNhOGVmNTBkZTliNWM2MTM4YjhmYjg5NTVkNjJhYWEyMjVhODAyODk2MzkwOTU5ZWQxNjg4MTQwMzdhYTEwYTNhMzYxYjVhNTg0NDgxZTI0N2E5MGZiNjIwZTg5Ijt9','filename'=>'coal_temp/5393f4ff63987.cstf','status' => 0 );
// 	test($ember->store('doom',null),null,'Store with null csum');
//  test($ember->retrieve(3),$prer,'Retrieve');
//  //print_r($ember->lstore('doom',new Csum('doom'),0));
//  $prel = array ( 'id' => '2', 'csum' => 'Tzo0OiJDc3VtIjo0OntzOjM6ImxlbiI7aTo0O3M6MzoibWQ1IjtzOjMyOiJiNGY5NDU0MzNlYTRjMzY5YzEyNzQxZjYyYTIzY2NjMCI7czozOiJzaGEiO3M6NDA6ImZlMDQ2YTQwODY4OWQwNzA2NmQ1N2VmOTU4YWQ5MGQ4YzMyZjcwMTMiO3M6NDoiczUxMiI7czoxMjg6Ijk0ZGNmOTVhZWNhODBmYmUzZDZmMzQxYzAyY2UzNzg5ZmNkNmNhOGVmNTBkZTliNWM2MTM4YjhmYjg5NTVkNjJhYWEyMjVhODAyODk2MzkwOTU5ZWQxNjg4MTQwMzdhYTEwYTNhMzYxYjVhNTg0NDgxZTI0N2E5MGZiNjIwZTg5Ijt9','status' => 0 );
//  test($ember->lstore('doom',new Csum('doom'),0),$prel,'Lstore');
//  test($ember->lstore('doom',null,0),null,'Lstore with null csum');
//  test($ember->lretrieve(2,0),$prer,'Lretrieve');
  	test($ember->adduser('test','fracture'),false,'Add user');
	echo '</body></html>';
}
?>