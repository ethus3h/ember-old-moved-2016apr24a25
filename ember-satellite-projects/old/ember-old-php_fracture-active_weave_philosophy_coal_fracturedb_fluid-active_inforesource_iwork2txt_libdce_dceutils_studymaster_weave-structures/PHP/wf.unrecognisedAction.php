<?php
//Handle 'unrecognised action' errors
function unrecognisedAction()
{
    echo itr(46) . $_POST['action'] . itr(47);
}
?>
