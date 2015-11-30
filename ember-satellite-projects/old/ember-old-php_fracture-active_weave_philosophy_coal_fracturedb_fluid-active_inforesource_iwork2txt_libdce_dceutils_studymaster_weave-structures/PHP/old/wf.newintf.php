<?php
function newintf($newIntfContent)
{
    global $newIntfId;
    $$itfidn = new intf(0, $wvLocaleString, $HttpsWPUrl, $newIntfContent);
    $$itfidn->to_db();
    $newIntfId = $$itfidn->id;
}
?>
