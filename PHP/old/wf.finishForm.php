<?php
//Complete a form
function finishForm($caption)
{
    return itr(286) . session_id() . itr(45) . $caption . itr(60);
}
?>
