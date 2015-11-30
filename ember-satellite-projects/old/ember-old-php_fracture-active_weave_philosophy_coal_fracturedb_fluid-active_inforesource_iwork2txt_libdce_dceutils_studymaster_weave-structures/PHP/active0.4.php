 <?php
#Futuramerlin Active Scripting Library. Version 0.4, 4 November 2013.
#Some code based on StudyMaster.

class Document_F
{
    var $content;
    var $style;
    var $title;
    #Unless header is supplied explicitly, title will be used
    var $header;
    
    function __construct($content, $style, $title, $header = '@NULL@')
    {
        $this->content = $content;
        $this->style   = $style;
        $this->title   = $title;
        if ($header == '@NULL@') {
            $this->header = '';
        } elseif ($header !== $this->title) {
            $this->header = $header;
        } else {
            $this->header = $this->title;
        }
    }
    
    function parse_legacy($data)
    {
        global $websiteName;
        global $cssFile;
        global $serverUrl;
        global $pageClass;
        if ($pageClass == 'w') {
            $tbl = '';
        } else {
            $tbl = '<table border="0" cellpadding="24" width="100%"><tbody><tr><td><br><h1>';
        }
        $pageVersionDirect = '2';
        $univNeedles       = array(
            'href="' . $serverUrl,
            "\t",
            "\n",
            "\r",
            "  ",
            "  ",
            "  ",
            "> <",
            '@p1@',
            '@p2@',
            '@p3@',
            '@p4@',
            '@cssFile@',
            '@websiteName@',
            '@tbl@',
            '@l@',
            '@n@',
            'https://futuramerlincom.fatcow.com',
            '@greenhead@',
            '@sylfan2@',
            '@bonnou@',
            '@bonnousuper@',
            '@fullmoon@',
            '@yy@',
            '@sumquiaestis@',
            '@128@',
            '@flautrock@',
            '@taito@',
            '@itfaen@',
            '.css";</style></title>'
        );
        $univHaystacks     = array(
            'href="',
            " ",
            " ",
            " ",
            " ",
            " ",
            " ",
            '><',
            res('1.d' . $pageVersionDirect),
            res('2.d' . $pageVersionDirect),
            res('3.d' . $pageVersionDirect),
            res('4.d' . $pageVersionDirect),
            $cssFile,
            $websiteName,
            $tbl,
            '<a href="r.php?',
            '">',
            'http://localhost',
            '<div class="greenpage"></div><div class="fh"><a href="/" id="tl"><i>futuramerlin</i></a></div>',
            getmusic('sylfan2'),
            getmusic('bonnou'),
            getmusic('bonnousuper'),
            getmusic('fullmoon'),
            getmusic('yy'),
            getmusic('sumquiaestis'),
            getmusic('128'),
            getmusic('flautrock'),
            getmusic('taito'),
            getmusic('itfaen'),
            '.css";</style>'
        );
        return trim(str_replace("\n", ' ', str_replace($univNeedles, $univHaystacks, $data)));
    }
    
    
    function display()
    {
        global $websiteName;
        $content = $this->parse_legacy($this->content);
        $title   = $this->parse_legacy($this->title);
        $header  = $this->parse_legacy($this->header);
        $display = trim(str_replace("\n", ' ', '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><style type="text/css" media="all">@import "/d/s.css";</style>' . $this->style . '<title>' . $title . '</title></head><body><div class="greenpage"></div><div class="fh"><a href="/" id="tl"><i>' . $websiteName . '</i></a></div><div id="content"><h1>' . $header . '</h1>' . $content . res('4.d2')));
        echo $display;
    }
}

class Discography
{
    var $title;
    var $content;
    
    
    function __construct()
    {
        /*
        $this->id=$id;
        $this->content=$title;
        $this->title=$title;*/
    }
    
    
    function getMusic($id)
    {
        global $dataDirectory;
        $musicdata = file_get_contents($dataDirectory . '/s/music/r/' . $id . '.html');
        
        #reencode to utf8 if necessary
        
        if (strpos($musicdata, 'ISO-8859-1')) {
            $musicdata = str_replace("ISO-8859-1", "UTF-8", iconv("ISO-8859-1", "UTF-8", $musicdata));
        }
        
        #clean up html mess
        $musicdata = str_replace('<html>', '', $musicdata);
        $musicdata = str_replace('</html>', '', $musicdata);
        $musicdata = str_replace('<head>', '', $musicdata);
        $musicdata = str_replace('</head>', '', $musicdata);
        $musicdata = str_replace('<body>', '', $musicdata);
        $musicdata = str_replace('</body>', '', $musicdata);
        $musicdata = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">', '', $musicdata);
        $musicdata = str_replace('<meta content="text/html; charset=UTF-8" http-equiv="content-type">', '', $musicdata);
        $musicdata = preg_replace("/<title(.*)<\/title>/iUs", "", $musicdata);
        //$musicdata = preg_replace('/<a target="_blank" href="http:\/\/archive.org\/download\/(.*)"flac">&#128266;<\/a>/', '<a target="_blank" href="http://archive.org/download/' . '$1' . 'mp3">&#128266;</a>', $musicdata);
        
        $musicdata = preg_replace('/<a href="http:\/\/archive.org\/download\/(.*)\/(.*).flac">/', '<audio src="http://archive.org/download/$1/$2.mp3" preload="none" class="audioplayer">Could not start audio player. Does your browser support it?</audio><a href="http://archive.org/download/$1/$2.flac">', $musicdata);
        $musicdata = str_replace('&#128266;</a>', 'Lossless…</a>', $musicdata);
        
        
        
        $musicdata = str_replace('cellpadding="2" cellspacing="2"', '', $musicdata);
        
        $musicdata = str_replace('AnoeyFuturamerlincom', 'anoeyfuturamerlincommedium,AnoeyFuturamerlincom', str_replace('<img', ' <img', str_replace('src="a/', 'class="mlefti" src="d/s/music/r/a/', str_replace('href="a/', 'class="mlefti" href="d/s/music/r/a/', str_replace('href="../', 'href="d/s/music/', str_replace('href=', 'target="_blank" href=', str_replace('</h1>', '</h2>', str_replace('<h1>', '<h2>', $musicdata))))))));
        return $musicdata;
    }
    
    function addThumbnail($id, $title)
    {
        return '<img class="musicthumb" src="http://futuramerlin.com/d/s/music/r/a/' . $id . '/t1.png" alt="' . $title . '"  onClick="hideAll();document.getElementById(\'' . $id . '\').style.display=\'block\';">';
    }
    
    function addContent($id, $content = '')
    {
        //if(strlen($content)>0){$separator='';}else{$separator='<hr>';}
        $separator = '';
        
        return str_replace('<table style="text-align: left;" border="1" >', '<table style="text-align: left; width:100%;" border="1" >', str_replace('<div class="musicbig" id="itfaen">', '<div class="musicbig" id="itfaen" style="/*max-width:450px;*/">', '<div class="musicbig" id="' . $id . '">' . $content . $separator . getMusic($id) . '</div>'));
    }
}

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

class FluidActive
{
    function __construct($title)
    {
        $this->globalIdsCounter = 1;
        $this->targetLocation   = '/d/r/active.php';
        $this->page             = '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<style type="text/css" media="all">table,tr,td{border:1px dotted maroon;}</style>
<title>' . $title . '</title>
</head>
<body>';
    }
    function newId()
    {
        $this->globalIdsCounter++;
        return 'fluidFragment' . $this->globalIdsCounter;
    }
    function getId()
    {
        return 'fluidFragment' . $this->globalIdsCounter;
    }
    function DBRowEntry($db, $table, $id)
    {
        $this->append('doom');
        #print_r($db->getRows($table));
        #print_r($db->getRows($table)->fetch_assoc());
        $numberOfRows   = $db->getRows($table)->num_rows;
        #print $numberOfRows;
        $arrayOfRowData = array();
        for ($i = 0; $i < $numberOfRows; $i++) {
            #print_r($db->getRows($table)->fetch_array());
            $arrayNextId  = 'rowDataArraySum' . $i;
            $$arrayNextId = $db->getRows($table)->fetch_assoc();
        }
        $arrayrowlist = '';
        for ($i = 0; $i < $numberOfRows; $i++) {
            $arrayrowlist = $arrayrowlist . '$rowDataArraySum' . $i . ',';
        }
        #$mergeRowData='$arrayOfRowData=array_merge('.substr($arrayrowlist,0,-1).');';
        $mergeRowData = '$arrayOfRowData=array_push(' . substr($arrayrowlist, 0, -1) . ');';
        print $mergeRowData;
        eval($mergeRowData);
        print_r($arrayOfRowData);
        // for($i=0;$i<$numberOfRows;$i++){
        // $arrayNextId='rowDataArraySum'.$i;
        // #print_r($arrayNextId);
        // $arrayOfRowData=array_merge($arrayOfRowData,$$arrayNextId);
        // }
        
        #print_r($db->getRows($table)->fetch_all());
        #http://stackoverflow.com/questions/6694437/mysqli-fetch-all-not-a-valid-function
        //         $results_array = array();
        //         #$result = $mysqli->query($query);
        //         while ($row = $db->getRows($table)->fetch_assoc()) {
        //             $results_array[] = $row;
        //         }
        #http://stackoverflow.com/questions/6694437/mysqli-fetch-all-not-a-valid-function
        // while ($row = $db->getRows($table)->fetch_all()) {
        // print 'doom';
        // }
        #http://stackoverflow.com/questions/13664659/php-mysqli-only-returns-one-row
        // while ($request_list_row = $db->getRows($table)->fetch_array()) {
        //     print_r($request_list);
        // }
        #http://stackoverflow.com/questions/17288044/function-returns-only-first-array-value-with-mysqli-query-result
        // $data  = array();
        //   while ($row = $db->getRows($table)->fetch_assoc()) {
        //      $data[] = $row;
        //   }
        // return $data;
        // print_r($rows);
        #http://www.php.net/manual/en/mysqli-result.fetch-assoc.php#112924
        #for($set=array();$row=$db->getRows($table)->fetch_assoc();$set[]=$row);
    }
    function DBTextEntry($db, $table, $field, $id)
    #$db is a FractureDB instance
    {
        $this->newId();
        $this->append('
<form method="post"><input type="text" id="' . $this->getId() . '" onKeyPress="sync_' . $this->getId() . '();"></form>
    <script type="text/javascript">function bin2hex (s) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Onno Marsman
  // +   bugfixed by: Linuxworld
  // +   improved by: ntoniazzi (http://phpjs.org/functions/bin2hex:361#comment_177616)
  // *     example 1: bin2hex(\'Kev\');
  // *     returns 1: \'4b6576\'
  // *     example 2: bin2hex(String.fromCharCode(0x00));
  // *     returns 2: \'00\'

  var i, l, o = "", n;

  s += "";

  for (i = 0, l = s.length; i < l; i++) {
    n = s.charCodeAt(i).toString(16)
    o += n.length < 2 ? "0" + n : n;
  }

  return o;
}

function sync_' . $this->getId() . '() {
     setTimeout(function () {   var ' . $this->getId() . ' = document.getElementById(\'' . $this->getId() . '\').innerHTML;

          var ' . $this->getId() . ' = bin2hex(document.getElementById(\'' . $this->getId() . '\').value);


   var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  
  var send="handler=1&handlerNeeded=DBSimpleSubmissionHandler&authorizationKey=blablabla&db=' . $db->name . '&dataTargetTable=' . $table . '&dataTargetField=' . $field . '&dataValue="+' . $this->getId() . ';
xmlhttp.open("POST","' . $this->targetLocation . '",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

xmlhttp.send(send);
   }, 100);
}

</script>');
    }
    function close()
    {
        $this->append('</body></html>');
        $this->writeOut();
    }
    function append($data)
    {
        $this->page = $this->page . $data;
    }
    function writeOut()
    {
        echo $this->page;
    }
    
}

class FractureDB
{
    function __construct($name)
    {
        $this->name = $name;
        
        
    }
    function query($query)
    {
        #echo $query;
        $db_data  = array(
            'arcmaj3' => array(
                'arcmaj3',
                'Kuzmenkotaxservices5.'
            )
        );
        $username = $db_data[$this->name][0];
        $password = $db_data[$this->name][1];
        $mysqli   = new mysqli("localhost", "futuqiur_" . $username, $password, "futuqiur_" . $this->name);
        return $mysqli->query($query);
    }
    function getRows($table)
    {
        #explain extended select * from am_urls; show warnings;
        #return $this->query('SHOW columns FROM '.$table);
        #return $this->query('SELECT GROUP_CONCAT(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name=\''.$table.'\'');
        return $this->query('SELECT COLUMN_NAME,DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name=\'' . $table . '\'');
    }
    function getNextId($table)
    {
        $query = 'SHOW TABLE STATUS LIKE \'' . $table . '\';';
        return $this->query($query);
    }
    function getTable($table)
    {
        $query = 'SELECT * FROM ' . $table . ';';
        return $this->query($query);
    }
    function select($table, $field, $value)
    {
        $query = 'SELECT * FROM ' . $table . ' WHERE ' . $field . ' = ' . $value . ';';
        return $this->query($query);
    }
    function getField($table, $field)
    {
        $query = 'SELECT ' . $field . ' FROM ' . $table . ';';
        return $this->query($query);
    }
    function setField($table, $field, $value, $id = '')
    {
        $query = 'INSERT INTO ' . $table . ' (' . $field . ') VALUES (\'' . $value . '\') ON DUPLICATE KEY UPDATE ' . $field . ' = \'' . $value . '\';';
        $this->query($query);
    }
    
    
}

function Rq($name)
{
    if (isset($_REQUEST[$name])) {
        return $_REQUEST[$name];
    } else {
        return '';
    }
}

#INSERT REQUEST RESPONDERS BELOW THIS LINE
#Arcmaj3
function arcmaj3_handler()
{
    #do things...
}
function DBSimpleSubmissionHandler()
{
    $authorizationKey = $_REQUEST['authorizationKey'];
    $dbName           = $_REQUEST['db'];
    $dataTargetTable  = $_REQUEST['dataTargetTable'];
    $dataTargetField  = $_REQUEST['dataTargetField'];
    $dataValue        = $_REQUEST['dataValue'];
    $db               = new FractureDB($dbName);
    $db->setField($dataTargetTable, $dataTargetField, $dataValue);
}

#INSERT WEB INTERFACE RESPONDERS BELOW THIS LINE
#Arcmaj3
function arcmaj3_wint()
{
    $main_console = new FluidActive('Arcmaj3 management web console');
    $db           = new FractureDB('arcmaj3');
    $main_console->DBTextEntry($db, 'am_urls', 'location', 0);
    $main_console->DBRowEntry($db, 'am_urls', 0);
    $main_console->close();
}


function runHandlers($handlerNeeded)
{
    echo 'Executing handler: ';
    echo $handlerNeeded;
    echo '<br>';
    #Excute request responders here.
    if ($handlerNeeded == 'arcmaj3') {
        arcmaj3_handler();
    }
    if ($handlerNeeded == 'DBSimpleSubmissionHandler') {
        DBSimpleSubmissionHandler();
    }
}

function runWints($wintNeeded)
{
    echo 'Executing wint: ';
    echo $wintNeeded;
    echo '<br>';
    #Excute web interface responders here.
    if ($wintNeeded == 'arcmaj3') {
        #print 'Welcome to Active.';
        arcmaj3_wint();
    }
}


#If handler is passed as 1, run runHandlers(). If wint is passed as 1, respond to a human. Otherwise, do nothing (presumably it is being included as a library). :)
$handlerNeeded = Rq('handlerNeeded');
$wintNeeded    = Rq('wintNeeded');
$Name          = Rq('handler');
if ($Name == '1') {
    $handler = true;
} else {
    $handler = false;
}
$Name = Rq('wint');
if ($Name == '1') {
    $included = false;
} else {
    $included = true;
}
if ($handler) {
    runHandlers($handlerNeeded);
} else {
    if ($included) {
    } else {
        runWints($wintNeeded);
    }
}
?> 