<?php
//Tools
if (isset($_REQUEST['tools'])) {
    global $baggage_claim;
    $serverprefix = $baggage_claim->claim_luggage('serverprefix');
    $dceutilsversion = $baggage_claim->claim_luggage('dceutilsversion');
    switch ($_REQUEST['action']) {
    case 'async_converter':
        $newwhy = '';
        global $serverprefix;
        global $dceutilsversion;
        echo '<a href="dceutils.php">←Return to main DCE script…</a><br><br><hr><br><br>Data:<br><br><form><input type="text" id="data" onKeyPress="sync();">&nbsp;Hexadecimal?<input type="checkbox" value="1" id="hex_enabled" onClick="sync();"><br><br>Source type?<input type="text" id="source" onKeyPress="sync();">&nbsp;Target type?<input type="text" id="target" onKeyPress="sync();"></form><br><br><p id="result"></p> <script type="text/javascript">';
        echo "function sync() {  
     setTimeout(function () {   
     if(document.getElementById('hex_enabled').checked)
     {document.getElementById('hex_enabled').value=1;}else{document.getElementById('hex_enabled').value=0;}
        var xmlhttp;
        if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
  }
  xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
document.getElementById('result').innerHTML=xmlhttp.responseText;
    }
  }
 xmlhttp.open('GET','dceutils.php?silenti=1&action=convert&data=' + document.getElementById('data').value + '&source=' + document.getElementById('source').value + '&target=' + document.getElementById('target').value + '&hexadecimal=' + document.getElementById('hex_enabled').value,true);
xmlhttp.send();



}, 100);
}";
        echo "</script>";
        break;
    }
} else {
}

