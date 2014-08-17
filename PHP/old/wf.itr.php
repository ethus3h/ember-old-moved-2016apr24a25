<?php
//Return a specified localised string from the database.
function itr($itfid)
{
    global $wvActionId;
    global $wvLocaleString;
    global $HttpsWPUrl;
    $itfidn = 'interface-' . $itfid;
    $$itfidn = new intf($itfid, $wvLocaleString, $HttpsWPUrl, '');
    $$itfidn->id = $itfid;
    $$itfidn->locale = $wvLocaleString;
    $$itfidn->url = $HttpsWPUrl;
    return($$itfidn->from_db($$itfidn->id, $$itfidn->locale, $$itfidn->url));
}
?>
