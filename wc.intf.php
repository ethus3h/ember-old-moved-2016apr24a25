<?php
class intf
{
    var $id;
    var $locale;
    var $url;
    var $content;
    public $text_id;
    public function __construct($id, $locale, $url, $content)
    {
        $this->id = $id;
        $this->locale = $locale;
        $this->url = $url;
        $this->content = $content;
        $text_id=$this->id.'_text';
        $$text_id = new text($this->content, 'intfContent', 'Content');
        global $baggage_claim;
        $baggage_claim->check_luggage('InterfaceTextObject',$$text_id);
    }
    function set_content($new_content)
    {
        $this->content = $new_content;
    }
    function get_content()
    {
        return $this->content;
    }
    function to_db()
    {
        mysql_query('INSERT INTO  `interface` (	 `interface_id`, `interface_content` ) VALUES ( NULL ,	\'' . mysql_real_escape_string($this->content) . '\');');
        global $newIntfId;
        $newIntfId = mysql_insert_id();
        $this->id = $newIntfId;
    }
    function from_db()
    {
        $mysqlquery = "SELECT `interface_content" . $this->locale . "` FROM `interface` WHERE `interface_id`=" . $this->id;
        //echo $mysqlquery;
        $arrayir = mysql_fetch_array(mysql_query($mysqlquery));
        $toreturn = 'interface_content' . $this->locale;
        $wvLocaleStringtest = $arrayir[$toreturn];
        if ($wvLocaleStringtest == '') {
            $arraylc = mysql_fetch_array(mysql_query("SELECT `interface_content` FROM `interface` WHERE `interface_id`=" . $this->id));
            $toreturn = 'interface_content';
        } else {
            $arraylc = mysql_fetch_array(mysql_query("SELECT `interface_content" . $this->locale . "` FROM `interface` WHERE `interface_id`=" . $this->id));
        }
        $stringtoreturn = $arraylc[$toreturn];
        if (strpos('</textarea>', $stringtoreturn) <= 0) {
            $stringtoreturn = $stringtoreturn;
        } else {
            if (strpos('>', $stringtoreturn) <= 0) {
                //Contains >
                $stringtoreturn = str_replace('>', '><!-- itf' . $this->id . ' -->', $stringtoreturn);
            } else {
                if (strpos('<', $stringtoreturn) <= 0) {
                    //Contains <
                    $stringtoreturn = str_replace('<', '<!-- itf' . $this->id . ' --><', $stringtoreturn);
                } else {
                    $stringtoreturn = '<!-- itf' . $this->id . ' -->' . $stringtoreturn;
                }
            }
        }
        return str_replace('https://futuramerlincom.fatcow.com', $this->url, $stringtoreturn);
    }
    function new_intf()
    {
        global $pageBody;
        global $pageTitle;
        $newInterface_exchange = new intf(0, $wvLocaleString, $HttpsWPUrl, '');
        global $baggage_claim;
        $text_object=$baggage_claim->claim_luggage('InterfaceTextObject');
        $pageBody = beginForm(itr(1264),itr(1263)) . $text_object->request_content() . finishForm(itr(67));
        $pageTitle = itr(81);
    }
}
?>
