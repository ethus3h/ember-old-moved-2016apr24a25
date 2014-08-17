<?php
#<s>This is now part of Fracture//Active.</s>
function res($name)
{
    global $dataDirectory;
    if(file_exists($dataDirectory . $name)) {
    	return file_get_contents($dataDirectory . $name);
    }
    else {
    	return null;
    }
}
?>
