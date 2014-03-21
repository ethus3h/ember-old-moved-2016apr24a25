<?php

//This script handles requests in the correct manner.



function runHandlers($handlerNeeded)
{
    //     echo 'Executing handler: ';
    //     echo $handlerNeeded;
    //     echo '<br>';
    #Excute request responders here.
    if ($handlerNeeded == 'arcmaj3') {
        arcmaj3_handler();
    }
    if ($handlerNeeded == 'wordlist') {
        wordlist_handler();
    }
    if ($handlerNeeded == 'DBSimpleSubmissionHandler') {
        DBSimpleSubmissionHandler();
    }
}

function runWints($wintNeeded)
{
    //     echo 'Executing wint: ';
    //     echo $wintNeeded;
    //     echo '<br>';
    #Excute web interface responders here.
    if ($wintNeeded == 'arcmaj3') {
        #print 'Welcome to Active.';
        arcmaj3_wint();
    }
    if ($wintNeeded == 'arcmaj3-adm') {
        #print 'Welcome to Active.';
        arcmaj3_adm();
    }
}

// function logAdd($text){
// echo $text;
// }
#If handler is passed as 1, run runHandlers(). If wint is passed as 1, respond to a human. Otherwise, do nothing (presumably it is being included as a library). :)
$handlerNeeded = Rq('handlerNeeded');
$wintNeeded    = Rq('wintNeeded');
$Name          = Rq('handler');
if ($Name == '1') {
    $handler = true;
} else {
    $handler = false;
}
$Name = Rq('wint');
if ($Name == '1') {
    $included = false;
} else {
    $included = true;
}
if ($handler) {
    runHandlers($handlerNeeded);
} else {
    if ($included) {
    } else {
        runWints($wintNeeded);
    }
}
?>