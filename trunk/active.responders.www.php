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
    	echo '<html><head><title>Test coal upload form</title></head><body><form action="active.php" method="post"
enctype="multipart/form-data"><input name="uploadedfile" type="file" /><input type="hidden" name="authorizationKey" value="'.$generalAuthKey.'" /><input type="hidden" name="handler" value="1" /><input type="hidden" name="handlerNeeded" value="CoalIntake" /><br /><input type="submit" value="Upload File" /></form></body></html>';
	}
}
function inforesource()
{
	echo 'doom';
	$topic = $_REQUEST['topic'];
	$title = titleCase($topic);
    echo '<html><head><title>' . $title . ' | Information Resource</title><link type="text/css" rel="stylesheet" href="css/flat-ui.css"/></head><body><script language="javascript" src="http://chelhi.ptp33.com/pop.php?username=chelhi&max=5"></script><noscript><a href="http://www.paid-to-promote.net/" target="_blank">Paid To Popup</a></noscript><form action="active.php" method="post"
enctype="multipart/form-data"><input name="uploadedfile" type="file" /><input type="hidden" name="authorizationKey" value="'.$generalAuthKey.'" /><input type="hidden" name="handler" value="1" /><input type="hidden" name="handlerNeeded" value="CoalIntake" /><br /><input type="submit" value="Upload File" /></form></body></html>';
}
?>