<?php
//Parse a supplied CDCE string and return it as HTML
function c($content)
{
    $retval = $content;
    $pattern = '@(\d+)@';
    $matches = array();
    preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $match_html = qry('character', 'character_html', 'character_id', $match[1]);
        $retval = str_replace('@' . $match[1] . '@', $match_html, $retval);
    }
    return $retval;
}
?>
