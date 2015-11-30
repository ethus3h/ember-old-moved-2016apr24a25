<?php
//FluidActive is the class implementing the Fluid//Active web UI toolkit
class FluidActive
{
    function __construct($script, $title = "")
    {
        $this->globalIdsCounter = 1;
        $this->tabled           = false;
        $this->targetLocation   = '/d/r/active.php';
        $this->FluidJS          = file_get_contents("fluid.js");
        global $activeVersion;
        $this->dynamicJsProperties = "var activeVersion = '" . $activeVersion . "';\n\n";
        global $fractureVersion;
        //help from http://stackoverflow.com/questions/20937475/remove-datalist-dropdown-arrow-in-chrome
        $this->page = '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title>' . $title . '</title><script src="/d/jquery-2.1.0.min.js" type="text/javascript"></script><script src="/d/r/jquery.transit.min.js" type="text/javascript"></script><style>@font-face {font-family:"Lato";font-style:normal;font-weight:100;src: local("Lato Hairline"),local("Lato-Hairline"),url(/d/f/lh.woff) format("woff");}@-webkit-keyframes spin{0%{-webkit-transform:rotate(0)}100%{-webkit-transform:rotate(360deg)}}@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}.loadingSpinnerContainer{margin-left:auto;margin-right:auto;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;-ms-box-sizing:border-box;box-sizing:border-box;display:block;width:100%;height:100%;margin:auto;border-width:0.1rem;border-style:solid;border-color:#444444 transparent transparent;border-radius:50%;-webkit-animation:spin 2.2s linear infinite;animation:spin 2.2s linear infinite}html{background-color:#b7b0b0;height:100%;font-size:100%;}body{display:flex;align-items:center;justify-content:center;margin:0;height:100%;width:100%;flex-flow:column;text-align:center}#loadingbox{font-size:3rem;font-family:"Lato",sans-serif;color:#444444;display:flex;align-items:center;flex-flow:column}#bgloading{margin-bottom:3rem;}input::-webkit-calendar-picker-indicator {display: none;}</style></head><body id="bodyContents"><div id="loadingbox"><div id="bgloading">Loading…</div><br><!-- <div class="loading"></div> --></div><div id="pageContents" style="width:100%;height:100%;top:0px;left:0px;position:fixed;"></div><!-- <div id="pageShield" style="left:0px;top:0px;width:100%;height:100%;z-index:2;position:fixed;"></div> --><script type="text/javascript">fractureVersion = "' . $fractureVersion . '";' . "\nvar self = this;\n\n" . $this->dynamicJsProperties . "\n\n" . $this->FluidJS;
        //$this->write(file_get_contents($script.".fluidScriptedUI"));
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
        if ($this->tabled == false) {
            $this->append('<div style="background-color:#A8FFEC;left:50px;top:50px;bottom:50px;right:50px;position:fixed;overflow-x:scroll;overflow-y:scroll;z-index:1998;text-align:center;"> ');
        }
        $this->tabled = true;
        //$main->append('<div style="z-index:2001;left:10px;position:fixed;top:10px;background:#F954A2;"><b>what</b>: What the event/project is. <b>begin</b>: When it begins. <b>end</b>: When it ends or is due. <b>location</b>: Where it happens. <b>notes</b>: e.g. assignment details.</div> -->');
        $this->append('<!-- <div style="background-color:#A8FFEC;left:50px;top:50px;bottom:50px;right:50px;position:fixed;overflow-x:scroll;overflow-y:scroll;z-index:2000;text-align:center;"> --><div style="background-color:#A8FFEC;left:50px;top:50px;bottom:50px;right:50px;position:relative;overflow-x:scroll;overflow-y:scroll;z-index:2000;text-align:center;"><table><thead><tr>');
        $this->append('<h3>' . $table . "</h3>");
        $this->append('<table><thead><tr></tr></thead><tbody><tr style="z-index:2001;left:10px;position:fixed;top:10px;background:#F954A2;" id="' . $this->newId() . '"><td id="' . $this->newId() . '"><b>Fields:</b>');
        $result = $db->getRow($table, 'id', '0');
        $this->append($result[0]['COLUMN_NAME']);
        $this->append("\n" . '<td id="' . $this->newId() . '">');
        $this->append("</td></tr></tbody></table>");
        $this->state = 0;
        $idList      = $db->listIds($table);
        if (strlen($idList) < 1) {
            //No available records; only create blank row entry form
        } else {
            $idData = explode_esc(',', $idList);
            foreach ($idData as $counter => $id) {
                //$this->append('Counter: ' . $counter . '<br>');
                //$this->append('Id: ' . $id . '<br>');
                $this->DBRowEntry($db, $table, $id);
                $this->state = $id;
            }
        }
        $this->append('</table>');
        $this->DBRowEntry($db, $table, ($this->state + 1));
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
        $this->append('</div>');
        $this->getQueryCount($db);
        
    }
    function DBRowEntry($db, $table, $id)
    {
        $result = $db->getRow($table, 'id', $id);
        #$result = $db->getTable($table);
        #$rowData=$result->fetchAll(PDO::FETCH_ASSOC);
        #print_r($result);
        #$rowDataArray=$result[0];
        $meta   = $db->getRowList($table);
        //  echo 'meta';
        //         print_r($meta);
        
        $this->append('<table><thead><tr></tr></thead><tbody><tr id="' . $this->newId() . '"><td id="' . $this->newId() . '"><b>Row ' . $id . ':</b>');
        //print_r($result);
        //         echo "\n\n\n\nresult array:\n";
        //print_r($result);
        //         echo "\n\n\n\n";
        $countl = 0;
        foreach ($result as $key => $value) {
            //                 echo "\n\n\n\nvalue array:\n";
            //         print_r($value);
            
            $countl++;
            $newkey    = false;
            $newkeyval = $key;
            // echo 'key:';
            //             print_r($key);
            //         echo "\n\n";
            if (is_array($value)) {
                //no data in field; create new blank field
                //$this->append("\n" . '<td id="' . $this->newId() . '">');
                //$this->DBTextEntry($db, $table, $value['COLUMN_NAME'], $id);
                $newkey    = true;
                $newkeyval = $value['COLUMN_NAME'];
                // echo 'Column name value:';
                //                 echo $newkeyval;
            }
            //exclude the primary key from the table
            if ($key != $db->getPrimaryKey($table)) {
                $thiskey = $key;
                if ($newkey) {
                    $thiskey = $newkeyval;
                    // echo 'thiskey: ';
                    //                     echo $thiskey;
                    $rowType = $meta[$countl - 1]['DATA_TYPE'];
                } else {
                    $rowType = $db->getRowType($table, $key);
                }
                // echo "\n\n";
                //                 echo "New key: ";
                //                 echo $key;
                //                 echo ". Thiskey: ";
                //                 echo $thiskey;
                //                 echo ". \n\n";
                $this->append("\n" . '<td id="' . $this->newId() . '">');
                #$this->DBTextEntry($db,$table,$key,$id));
                if ($rowType == 'tinyint') {
                    $this->append('<small><small>bool not really supported yet, 0 = false 1 = true, also this might not be a bool and just be a normal tinyint field too...</small></small> ');
                    $this->DBIntegerEntry($db, $table, $thiskey, $id);
                } else {
                    if (($rowType == 'text') || ($rowType == 'longtext') || ($rowType == 'varchar')) {
                        $this->DBTextEntry($db, $table, $thiskey, $id);
                    } else {
                        if ($rowType == 'date') {
                            $this->DBDateEntry($db, $table, $thiskey, $id);
                        } else {
                            if ($rowType == 'int') {
                                $this->DBIntegerEntry($db, $table, $thiskey, $id);
                            } else {
                                $this->append("\n\n" . 'unknown row type ' . $rowType . "\n\n");
                                
                            }
                        }
                    }
                }
            }
            $this->append("</td>\n");
            
        }
        $this->append('</tr></tbody></table>');
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
        global $generalAuthKey;
        $this->append("<b>" . $field . "</b>" . ': (type: Text). <form method="post"><input type="text" value="' . $fieldVal . '" id="' . $this->getId() . '" onKeyPress="sync_' . $this->getId() . '();"></form>
            <script type="text/javascript">function bin2hex(s){var i,l,o="",n;s+="";for(i=0,l=s.length;i<l;i++){n=s.charCodeAt(i).toString(16);o+=n.length<2?"0"+n:n}return o} function sync_' . $this->getId() . '() { setTimeout(function () {   var ' . $this->getId() . ' = document.getElementById(\'' . $this->getId() . '\').innerHTML; var ' . $this->getId() . ' = bin2hex(document.getElementById(\'' . $this->getId() . '\').value); console.log(' . $this->getId() . '); var xmlhttp;
                if (window.XMLHttpRequest) { xmlhttp=new XMLHttpRequest(); } else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); } var send="handler=1&handlerNeeded=DBSimpleSubmissionHandler&authorizationKey=' . $generalAuthKey . '&db=' . $db->name . '&dataTargetTable=' . $table . '&dataTargetRowId=' . $id . '&dataTargetField=' . $field . '&dataValue="+' . $this->getId() . ';
            xmlhttp.open("POST","' . $this->targetLocation . '",true); xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded"); xmlhttp.send(send); }, 100); } </script>');
    }
    function DBDateEntry($db, $table, $field, $id)
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
        $fieldVal = $db->getField($table, $field, $id);
        global $generalAuthKey;
        $this->append("<b>" . $field . "</b>" . ': (type: Date). Format YYYY-MM-DD. <form method="post"><input type="text" value="' . $fieldVal . '" id="' . $this->getId() . '" onKeyPress="sync_' . $this->getId() . '();"></form>
            <script type="text/javascript">function bin2hex(s){var i,l,o="",n;s+="";for(i=0,l=s.length;i<l;i++){n=s.charCodeAt(i).toString(16);o+=n.length<2?"0"+n:n}return o} function sync_' . $this->getId() . '() { setTimeout(function () {   var ' . $this->getId() . ' = document.getElementById(\'' . $this->getId() . '\').innerHTML; var ' . $this->getId() . ' = document.getElementById(\'' . $this->getId() . '\').value; console.log(' . $this->getId() . '); var xmlhttp;
                if (window.XMLHttpRequest) { xmlhttp=new XMLHttpRequest(); } else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); } var send="handler=1&handlerNeeded=DBSimpleSubmissionHandler&authorizationKey=' . $generalAuthKey . '&db=' . $db->name . '&dataTargetTable=' . $table . '&dataTargetRowId=' . $id . '&dataTargetField=' . $field . '&dataValue="+' . $this->getId() . ';
            xmlhttp.open("POST","' . $this->targetLocation . '",true); xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded"); xmlhttp.send(send); }, 100); } </script>');
    }
    function DBIntegerEntry($db, $table, $field, $id)
    #$db is a FractureDB instance
    {
        $this->newId();
        global $generalAuthKey;
        $this->append("<b>" . $field . "</b>" . ': (type: Integer). It only likes numbers. <form method="post"><input type="text" value="' . $db->getField($table, $field, $id) . '" id="' . $this->getId() . '" onKeyPress="sync_' . $this->getId() . '();"></form> <script type="text/javascript"> function sync_' . $this->getId() . '() { setTimeout(function () {   var ' . $this->getId() . ' = document.getElementById(\'' . $this->getId() . '\').innerHTML;
            var ' . $this->getId() . ' = document.getElementById(\'' . $this->getId() . '\').value; var xmlhttp; if (window.XMLHttpRequest) { xmlhttp=new XMLHttpRequest(); } else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); } 
            var send="handler=1&handlerNeeded=DBSimpleSubmissionHandler&authorizationKey=' . $generalAuthKey . '&db=' . $db->name . '&dataTargetTable=' . $table . '&dataTargetRowId=' . $id . '&dataTargetField=' . $field . '&dataValue="+' . $this->getId() . ';
            xmlhttp.open("POST","' . $this->targetLocation . '",true); xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded"); xmlhttp.send(send); }, 100); } </script>');
    }
    function filter_out($data)
    {
        $univNeedles   = array(
            "\t",
            "\n",
            "\r",
            "  ",
            "  ",
            "  ",
            "> <"
        );
        $univHaystacks = array(
            " ",
            " ",
            " ",
            " ",
            " ",
            " ",
            '><'
        );
        //return trim(preg_replace('/\s+/', ' ', str_replace("\n", ' ', str_replace($univNeedles, $univHaystacks, $data))));
        return $data;
    }
    function getQueryCount($db)
    {
        $this->append('<br><br>Page served using ' . $db->queryCount . ' queries.<br><br>');
    }
    function close()
    {
        $this->write('</script><script type="text/javascript">$(window).resize(function(){
        //don\'t remember what this next line was supposed to be...
        //$("#main-content")
        // repaint all Boxes
        RecomputeMetrics();
    });</script></body></html>');
        
        $this->writeOut();
    }
    function append($data)
    {
        $this->page = $this->page . "</script>" . $data;
    }
    function write($data)
    {
        $this->page = $this->page . $data;
    }
    function writeOut()
    {
        echo $this->filter_out($this->page);
    }
    
}
?>