<?php
//FluidActive is the class implementing the Fluid//Active web UI toolkit
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
    function DBTableEntry($db, $table)
    {
        $idList = $db->listIds($table);
        $idData = explode_esc(',', $idList);
        foreach ($idData as $counter => $id) {
            $this->append('Counter: ' . $counter . '<br>');
            $this->append('Id: ' . $id . '<br>');
            $this->DBRowEntry($db, $table, $id);
        }
        // $counter=0;
        // $id=1;
        // $this->append('Counter: '.$counter.'<br>');
        // $this->append('Id: '.$id.'<br>');
        // $this->DBRowEntry($db,$table,$id);
        // $counter=1;
        // $id=2;
        // $this->append('Counter: '.$counter.'<br>');
        // $this->append('Id: '.$id.'<br>');
        // $this->DBRowEntry($db,$table,$id);
        
    }
    function DBRowEntry($db, $table, $id)
    {
        $result = $db->getRow($table, 'id', $id);
        #$result = $db->getTable($table);
        #$rowData=$result->fetchAll(PDO::FETCH_ASSOC);
        #print_r($result);
        #$rowDataArray=$result[0];
        $meta   = $db->getRowList($table);
        #print_r($meta);
        $this->append('<table><thead><tr>');
        
        $this->append('</tr></thead><tbody><tr id="' . $this->newId() . '"><td id="' . $this->newId() . '"><b>Row ' . $id . ':</b>');
        foreach ($result as $key => $value) {
            $rowType = $db->getRowType($table, $key);
            $this->append("\n" . '<td id="' . $this->newId() . '">');
            #$this->DBTextEntry($db,$table,$key,$id));
            if ($rowType == 'bool') {
                $this->append('bool not supported');
            } else {
                if ($rowType == 'text') {
                    $this->DBTextEntry($db, $table, $key, $id);
                } else {
                    if ($rowType == 'int') {
                        $this->DBIntegerEntry($db, $table, $key, $id);
                    }
                }
            }
            $this->append("</td>\n");
            
        }
        $this->append('</tr></tbody></table');
    }
    function DBTextEntry($db, $table, $field, $id)
    #$db is a FractureDB instance
    {
        $this->newId();
        //   // http://kevin.vanzonneveld.net
        //   // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        //   // +   bugfixed by: Onno Marsman
        //   // +   bugfixed by: Linuxworld
        //   // +   improved by: ntoniazzi (http://phpjs.org/functions/bin2hex:361#comment_177616)
        //   // *     example 1: bin2hex(\'Kev\');
        //   // *     returns 1: \'4b6576\'
        //   // *     example 2: bin2hex(String.fromCharCode(0x00));
        //   // *     returns 2: \'00\'
        $fieldVal = hex2bin($db->getField($table, $field, $id));
        $this->append('Field type: Text. <form method="post"><input type="text" value="' . $fieldVal . '" id="' . $this->getId() . '" onKeyPress="sync_' . $this->getId() . '();"></form>
			<script type="text/javascript">function bin2hex(s){var i,l,o="",n;s+="";for(i=0,l=s.length;i<l;i++){n=s.charCodeAt(i).toString(16);o+=n.length<2?"0"+n:n}return o} function sync_' . $this->getId() . '() { setTimeout(function () {   var ' . $this->getId() . ' = document.getElementById(\'' . $this->getId() . '\').innerHTML; var ' . $this->getId() . ' = bin2hex(document.getElementById(\'' . $this->getId() . '\').value); alert(' . $this->getId() . '); var xmlhttp;
      	  	if (window.XMLHttpRequest) { xmlhttp=new XMLHttpRequest(); } else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); } var send="handler=1&handlerNeeded=DBSimpleSubmissionHandler&authorizationKey=blablabla&db=' . $db->name . '&dataTargetTable=' . $table . '&dataTargetRowId=' . $id . '&dataTargetField=' . $field . '&dataValue="+' . $this->getId() . ';
        	xmlhttp.open("POST","' . $this->targetLocation . '",true); xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded"); xmlhttp.send(send); }, 100); } </script>');
    }
    function DBIntegerEntry($db, $table, $field, $id)
    #$db is a FractureDB instance
    {
        $this->newId();
        $this->append('Field type: Integer. It only likes numbers. <form method="post"><input type="text" value="' . $db->getField($table, $field, $id) . '" id="' . $this->getId() . '" onKeyPress="sync_' . $this->getId() . '();"></form> <script type="text/javascript"> function sync_' . $this->getId() . '() { setTimeout(function () {   var ' . $this->getId() . ' = document.getElementById(\'' . $this->getId() . '\').innerHTML;
			var ' . $this->getId() . ' = document.getElementById(\'' . $this->getId() . '\').value; var xmlhttp; if (window.XMLHttpRequest) { xmlhttp=new XMLHttpRequest(); } else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); } 
			var send="handler=1&handlerNeeded=DBSimpleSubmissionHandler&authorizationKey=blablabla&db=' . $db->name . '&dataTargetTable=' . $table . '&dataTargetRowId=' . $id . '&dataTargetField=' . $field . '&dataValue="+' . $this->getId() . ';
			xmlhttp.open("POST","' . $this->targetLocation . '",true); xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded"); xmlhttp.send(send); }, 100); } </script>');
    }
    function filter_out($data)
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
            resl('1.d' . $pageVersionDirect),
            resl('2.d' . $pageVersionDirect),
            resl('3.d' . $pageVersionDirect),
            resl('4.d' . $pageVersionDirect),
            $cssFile,
            $websiteName,
            $tbl,
            '<a href="r.php?',
            '">',
            'http://localhost',
            '<div class="greenpage"></div><div class="fh"><a href="/" id="tl"><i>futuramerlin</i></a></div>',
            '.css";</style>'
        );
        return trim(preg_replace('/\s+/', ' ', str_replace("\n", ' ', str_replace($univNeedles, $univHaystacks, $data))));
    }
    function getQueryCount($db)
    {
        $this->append('<br><br>Page served using ' . $db->queryCount . ' queries.<br><br>');
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
        echo $this->filter_out($this->page);
    }
    
}
?>