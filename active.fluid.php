<?php
//FluidActive is the class implementing the Fluid//Active web UI toolkit
class FluidActive
{
    function __construct($title)
    {
        $this->globalIdsCounter = 1;
        $this->targetLocation   = '/d/r/active.php';
        $this->FluidJS = <<<EOT
        /* JavaScript code for Fluid//Active */
        function Box(contents,bgcolor,blur,vpanchor,vpos,vposunit,hpanchor,
        hpos,hposunit,wanchor,width,wunit,hanchor,heighth,hunit,crop,zindex) {
        /* ~Explanations of parameters~
        contents: HTML contents of the box. Should be represented as a <svg> tag. This will be displayed on top of the bgcolor.
        bgcolor: Background color. Can be any CSS background-color, see https://developer.mozilla.org/en-US/docs/Web/CSS/background-color
        blur: Use a blur effect on whatever's behind this box. (This could actually create another box object below the current one with the content as the blurry SVG?) Result should be like this http://jsfiddle.net/3z6ns/ or this http://jsfiddle.net/YgHA8/1/
        vpanchor: ID of the box to which this box's vertical postion should be relative. ID 0 is the browser window.
        vpos: Vertical position of this box relative to the vpanchor box.
        vposunit: Units (%, or possibly rem?) of vpos
        hpanchor: ID of the box to which this box's horizontal postion should be relative. ID 0 is the browser window.
        hpos: Vertical position of this box relative to the hpanchor box.
        hposunit: Units (%, or possibly rem?) of hpos
        wanchor: ID of the box to which this box's horizontal postion should be relative. ID 0 is the browser window.
        width: Width of this box relative to the wanchor box.
        wunit: Units (%, or possibly rem?) of width
        hanchor: ID of the box to which this box's heighth should be relative. ID 0 is the browser window.
        heighth: Width of this box relative to the hanchor box.
        hunit: Units (%, or possibly rem?) of heighth
        crop: ID of the box to which this box should be cropped, if any. Default to 0 (the browser window) (basically that means no cropping).
        zindex: stacking order of this box. Should this parameter be used? It seems it would probably be better to just have whatever box comes later in the DOM go on top (by getting a dynamically specified z-index)
		*/
		this.contents = contents;
		this.bgcolor = bgcolor;
		this.blur = blur;
		this.vpanchor = vpanchor;
		this.vpos = vpos;
		this.vposunit = vposunit;
		this.hpanchor = hpanchor;
		this.hpos = hpos;
		this.hposunit = hposunit;
		this.wanchor = wanchor;
		this.width = width;
		this.wunit = wunit;
		this.hanchor = hanchor;
		this.heighth = heighth;
		this.hunit = hunit;
		this.crop = crop;
		this.zindex = zindex;
        alert("Box instantiated. Contents = "+this.contents+", bgcolor = "+this.bgcolor
        +", blur = "+this.blur+", vpanchor = "+this.vpanchor+", vpos = "+this.vpos
        +", vposunit = "+this.vposunit+", hpanchor = "+this.hpanchor+", hpos = "+this.hpos
        +", hposunit = "+this.hposunit+", wanchor = "+this.wanchor+", width = "+this.width
        +", wunit = "+this.wunit+", hanchor = "+this.hanchor+", heighth = "+this.heighth
        +", hunit = "+this.hunit+", crop = "+this.crop+", zindex = "+this.zindex);
         }
         
var Box1 = new Box(0,0,0,0,0,"%",0,0,"%",0,100,"%",0,100,"%",0,101);
var Box2 = new Box();

EOT;
        $this->page             = '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title>' . $title . '</title><style>@font-face {font-family:"Lato";font-style:normal;font-weight:100;src: local("Lato Hairline"),local("Lato-Hairline"),url(/d/f/lh.woff) format("woff");}@-webkit-keyframes spin{0%{-webkit-transform:rotate(0)}100%{-webkit-transform:rotate(360deg)}}@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}.loading{margin-left:auto;margin-right:auto;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;-ms-box-sizing:border-box;box-sizing:border-box;display:block;width:101px;height:101px;margin:auto;border-width:1px;border-style:solid;border-color:#444444 transparent transparent;border-radius:51px;-webkit-animation:spin 2.2s linear infinite;animation:spin 2.2s linear infinite}html{background-color:#b7b0b0;height:100%;font-size:100%;}body{display:flex;align-items:center;justify-content:center;margin:0;height:100%;width:100%;flex-flow:column;text-align:center}#loadingbox{font-size:3rem;font-family:"Lato",sans-serif;color:#444444;display:flex;align-items:center;flex-flow:column}#bgloading{margin-bottom:3rem;}</style></head><body><div id="loadingbox"><div id="bgloading">Loading…</div><br><div class="loading"></div></div><script type="text/javascript">'.$this->FluidJS;
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
    	$univNeedles       = array(
            "\t",
            "\n",
            "\r",
            "  ",
            "  ",
            "  ",
            "> <",
        );
        $univHaystacks     = array(
            " ",
            " ",
            " ",
            " ",
            " ",
            " ",
            '><',
        );
        return trim(preg_replace('/\s+/', ' ', str_replace("\n", ' ', str_replace($univNeedles, $univHaystacks, $data))));
    }
    function getQueryCount($db)
    {
        $this->append('<br><br>Page served using ' . $db->queryCount . ' queries.<br><br>');
    }
    function close()
    {
        $this->append('</script></body></html>');
        
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