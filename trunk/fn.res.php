<?php
#<s>This is now part of Fracture//Active.</s>
function res($name)
{
    global $dataDirectory;
    return file_get_contents($dataDirectory . $name);
}
?>
