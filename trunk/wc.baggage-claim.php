<?php
class baggage_claim
{

# REGISTERED LUGGAGE:
# InterfaceTextObject to wc.intf.php
# Shortened to wf.shorten.php

    public $InterfaceTextObject;
    public $Shortened;
    function check_luggage($variable,$new_content)
    {
        $this->$variable = $new_content;
    }
    function claim_luggage($variable)
    {
        return $this->$variable;
    }
}
global $baggage_claim;
$baggage_claim=new baggage_claim;
?>
