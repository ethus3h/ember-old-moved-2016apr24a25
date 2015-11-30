<?php
#Futuramerlin Active Scripting Library. Version 0.86.9, 16 December 2013.
#Some code based on StudyMaster; some based on the other d/r scripts.
#Useful SQL commands:
#
#Return all currently-out barrels to pool (follow this up with an expire-old-barrels from the management tool):
#
#UPDATE `am_barrels` SET dateAssigned=0 WHERE status=0
#
#See whatever's running on the database:
#
#SHOW FULL PROCESSLIST
include('authData.php');
//CONFIG
error_reporting(E_ALL);
ini_set("display_errors", 1);
$displayDebugMessages = False;
//IMPORTANT VARIABLES
if ($_SERVER["HTTP_HOST"] == '127.0.0.1') {
    //HTTP server
    //default: futuramerlin.com
    //development: 127.0.0.1
    $serverHttp  = '127.0.0.1';
    //Secure server
    //default: futuramerlincom.fatcow.com
    //development: 127.0.0.1
    $serverHttps = '127.0.0.1';
    //Database server
    //default: futuramerlincom.fatcowmysql.com
    //development: 127.0.0.1
    $dbServer    = 'localhost';
    //Uncomment the next two lines for development
    $HttpsWPUrl  = 'http://127.0.0.1';
} else {
    //HTTP server
    //default: futuramerlin.com
    //development: 127.0.0.1
    $serverHttp  = 'futuramerlin.com';
    //Secure server
    //default: futuramerlincom.fatcow.com
    //development: 127.0.0.1
    $serverHttps = 'futuramerlincom.fatcow.com';
    //Database server
    //default: futuramerlincom.fatcowmysql.com
    //development: 127.0.0.1
    $dbServer    = 'localhost';
    $HttpsWPUrl  = 'http://futuramerlin.com';
}
//default: futuramerlin
// $websiteName = 'futuramerlin';
// global $pageClass;
// $pageClass = Rq('c');
// global $pageAction;
// $pageAction = Rq('a');
// global $dataDirectory;
$dataDirectory = 'd/';
//Server URL
if (isset($_SERVER['HTTPS'])) {
    //If it's secure HTTP
    //default: 'https://' . $serverHttps . '/'
    $serverUrl = 'http://' . $serverHttps . '/';
} else {
    //If it's normal HTTP
    //default: 'http://' . $serverHttp . '/'
    $serverUrl = 'http://' . $serverHttp . '/';
}
$dataUrl = '/' . $dataDirectory;
if (strpos($_SERVER['HTTP_USER_AGENT'], 'AppleWebKit')) {
    $cssFile = $dataUrl . 'f.css';
} else {
    $cssFile = $dataUrl . 's.css';
}
$pageVersion = '2';
class Database extends PDO
#from http://us2.php.net/manual/en/pdo.begintransaction.php#109753
{
    protected $transactionCounter = 0;
    function beginTransaction()
    {
        if (!$this->transactionCounter++)
            return parent::beginTransaction();
        return $this->transactionCounter >= 0;
    }
    
    function commit()
    {
        if (!--$this->transactionCounter)
            return parent::commit();
        return $this->transactionCounter >= 0;
    }
    
    function rollback()
    {
        if ($this->transactionCounter >= 0) {
            $this->transactionCounter = 0;
            return parent::rollback();
        }
        $this->transactionCounter = 0;
        return false;
    }
}
class Document_F
{
    var $content;
    var $style;
    var $title;
    #Unless header is supplied explicitly, title will be used
    var $header;
    
    function __construct($content, $style, $title, $header = '@NULL@')
    {
        $this->content = $content;
        $this->style   = $style;
        $this->title   = $title;
        if ($header == '@NULL@') {
            $this->header = '';
        } elseif ($header !== $this->title) {
            $this->header = $header;
        } else {
            $this->header = $this->title;
        }
    }
    
    function parse_legacy($data)
    {
        global $websiteName;
        global $cssFile;
        global $serverUrl;
        global $pageClass;
        if ($pageClass == 'w') {
            $tbl = '';
        } else {
            $tbl = '<table border="0" cellpadding="24" width="100%"><tbody><tr><td><br><h1>';
        }
        $pageVersionDirect = '2';
        $univNeedles       = array(
            'href="' . $serverUrl,
            "\t",
            "\n",
            "\r",
            "  ",
            "  ",
            "  ",
            "> <",
            '@p1@',
            '@p2@',
            '@p3@',
            '@p4@',
            '@cssFile@',
            '@websiteName@',
            '@tbl@',
            '@l@',
            '@n@',
            'https://futuramerlincom.fatcow.com',
            '@greenhead@',
            '@sylfan2@',
            '@bonnou@',
            '@bonnousuper@',
            '@fullmoon@',
            '@yy@',
            '@sumquiaestis@',
            '@128@',
            '@flautrock@',
            '@taito@',
            '@itfaen@',
            '.css";</style></title>'
        );
        $univHaystacks     = array(
            'href="',
            " ",
            " ",
            " ",
            " ",
            " ",
            " ",
            '><',
            res('1.d' . $pageVersionDirect),
            res('2.d' . $pageVersionDirect),
            res('3.d' . $pageVersionDirect),
            res('4.d' . $pageVersionDirect),
            $cssFile,
            $websiteName,
            $tbl,
            '<a href="r.php?',
            '">',
            'http://localhost',
            '<div class="greenpage"></div><div class="fh"><a href="/" id="tl"><i>futuramerlin</i></a></div>',
            getmusic('sylfan2'),
            getmusic('bonnou'),
            getmusic('bonnousuper'),
            getmusic('fullmoon'),
            getmusic('yy'),
            getmusic('sumquiaestis'),
            getmusic('128'),
            getmusic('flautrock'),
            getmusic('taito'),
            getmusic('itfaen'),
            '.css";</style>'
        );
        return trim(str_replace("\n", ' ', str_replace($univNeedles, $univHaystacks, $data)));
    }
    
    
    function display()
    {
        global $websiteName;
        $content = $this->parse_legacy($this->content);
        $title   = $this->parse_legacy($this->title);
        $header  = $this->parse_legacy($this->header);
        $display = trim(str_replace("\n", ' ', '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><style type="text/css" media="all">@font-face{font-family:\'anoeyfuturamerlincommedium\';src:url(\'d/f/anoeyfuturamerlincom2.61.eot\');src:url(\'d/f/anoeyfuturamerlincom2.61.eot?#iefix\') format(\'embedded-opentype\'),url(\'d/f/anoeyfuturamerlincom2.61.woff\') format(\'woff\'),url(\'d/f/anoeyfuturamerlincom2.61.ttf\') format(\'truetype\'),url(\'d/f/anoeyfuturamerlincom2.61.svg#anoeyfuturamerlincommedium\') format(\'svg\');font-weight:normal;font-style:normal}@font-face{font-family:\'wreatherweb\';src:url(\'d/f/wreathe-r.eot\');src:url(\'d/f/wreathe-r.eot?#iefix\') format(\'embedded-opentype\'),url(\'d/f/wreathe-r.woff\') format(\'woff\'),url(\'d/f/wreathe-r.ttf\') format(\'truetype\'),url(\'d/f/wreathe-r.svg#wreatherweb\') format(\'svg\');font-weight:normal;font-style:normal}@font-face{font-family:\'wreatherweb\';src:url(\'d/f/wreathe-b.eot\');src:url(\'d/f/wreathe-b.eot?#iefix\') format(\'embedded-opentype\'),url(\'d/f/wreathe-b.woff\') format(\'woff\'),url(\'d/f/wreathe-b.ttf\') format(\'truetype\'),url(\'d/f/wreathe-b.svg#wreathebold\') format(\'svg\');font-weight:bold;font-style:normal}@font-face{font-family:\'wreatherweb\';src:url(\'d/f/wreathe-i.eot\');src:url(\'d/f/wreathe-i.eot?#iefix\') format(\'embedded-opentype\'),url(\'d/f/wreathe-i.woff\') format(\'woff\'),url(\'d/f/wreathe-i.ttf\') format(\'truetype\'),url(\'d/f/wreathe-i.svg#wreatheitalic\') format(\'svg\');font-weight:normal;font-style:italic}@font-face{font-family:\'wreatherweb\';src:url(\'d/f/wreathe-bi.eot\');src:url(\'d/f/wreathe-bi.eot?#iefix\') format(\'embedded-opentype\'),url(\'d/f/wreathe-bi.woff\') format(\'woff\'),url(\'d/f/wreathe-bi.ttf\') format(\'truetype\'),url(\'d/f/wreathe-bi.svg#wreathebold_italic\') format(\'svg\');font-weight:bold;font-style:italic}html{background:#d5e3cf;background:-webkit-gradient(radial,50% 0,0,50% 0,300,from(#FFF),to(#d5e3cf));background:-moz-radial-gradient(50% 0 90deg,circle farthest-side,#FFF,#d5e3cf,#d5e3cf 31.25em);background-repeat:no-repeat;background-color:#d5e3cf}body{text-align:justify;overflow-x:hidden;min-height:18.75em}form{display:inline}pre,blockquote,li,table,tbody,tr,td,ul,ol{color:#22520f;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;table-layout:fixed}a{color:#520f22;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:none;-webkit-transition:all .8s ease-out;-moz-transition:all .8s ease-out;-o-transition:all .8s ease-out;transition:all .8s ease-out}a:hover{color:#520f22;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:underline}p{color:#22520f;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;word-wrap:break-word;text-indent:30pt;text-align:justify;margin-top:0;margin-bottom:0}table{border-color:transparent}input{background-color:transparent}h1{color:#22520f;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;font-weight:normal;text-align:center}h2,h3,h4,h5,h6{color:#22520f;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;font-weight:normal;font-style:italic;text-align:center}.t{border:0;background-color:transparent;padding:0;overflow:visible;font-size:1em;color:#520f22;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif}.t:hover{border:0;background-color:transparent;padding:0;overflow:visible;font-size:1em;color:#520f22;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:underline;cursor:pointer}div.floattb{position:fixed;bottom:0;left:0;width:100%;z-index:3;text-align:center}div.floatbg{left:25px;right:25px;position:fixed;bottom:0;height:25px;z-index:2;opacity:.85;background-color:#f0f0f0}a.floatlink{font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:none}a.floatlink:hover{font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:underline}div.generated-toc{text-align:left;list-style-type:none;position:fixed;bottom:25px;left:35px;width:25%;z-index:2;background-color:#efefef;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:none}div#hideshow{position:fixed;bottom:0;left:-12px;width:10%;z-index:4;text-align:left}div#generated-toc a{font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:none;overflow-y:scroll}div#generated-toc ul{text-indent:-10pt;list-style-type:none;font-size:x-small}a#generated_toc_d_toggle:hover{text-decoration:none}p#toggle-container{text-align:left}div.greenpage{position:absolute;top:0;min-height:18.75em;background-color:transparent;margin:8px;margin-right:8px;z-index:-1}div.fh{left:25px;right:25px;position:absolute;top:25px;height:25px;z-index:100;text-align:center;font-size:large}div.litem{padding:10px;text-align:center}div.smalllink{padding-top:20px;text-align:center;font-size:x-small}div.relative{position:relative;padding-top:0}.reveal-modal-bg{position:fixed;height:100%;width:100%;background:#000;background:rgba(0,0,0,.8);z-index:100;display:none;top:0;left:0}.reveal-modal{visibility:hidden;top:75px;left:0;margin-left:-10px;width:90%;max-width:900px;background:#eee url(g.png) no-repeat -200px -80px;position:absolute;z-index:101;padding:0;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;-moz-box-shadow:0 0 10px rgba(0,0,0,.4);-webkit-box-shadow:0 0 10px rgba(0,0,0,.4);-box-shadow:0 0 10px rgba(0,0,0,.4)}.reveal-modal .close-reveal-modal{font-size:22px;line-height:.5;position:absolute;top:8px;right:11px;color:#aaa;font-weight:bold;cursor:pointer}div.logobox{margin:auto;display:inline-block;position:relative;height:20%;width:auto;padding-top:16px;padding-left:24px;float:left;padding-right:75px}div.holder{-webkit-box-shadow:0 0 10px 5px #FFF;box-shadow:0 0 10px 0 #FFF;margin:auto;position:relative;left:25px;width:322px;display:inline-block}div.caption{color:white;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:none}div.captionbg{position:absolute;bottom:0;left:0;width:100%;height:100%;background:#000;background:url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSI5MCUiIHN0b3AtY29sb3I9IiMwMDAwMDAiIHN0b3Atb3BhY2l0eT0iMSIvPgogICAgPHN0b3Agb2Zmc2V0PSI5MCUiIHN0b3AtY29sb3I9IiMwMDAwMDAiIHN0b3Atb3BhY2l0eT0iMSIvPgogICAgPHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSIjNjA2MDYwIiBzdG9wLW9wYWNpdHk9IjEiLz4KICAgIDxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzI2MjYyNiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgPC9saW5lYXJHcmFkaWVudD4KICA8cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIiBmaWxsPSJ1cmwoI2dyYWQtdWNnZy1nZW5lcmF0ZWQpIiAvPgo8L3N2Zz4=");background:-moz-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:-webkit-gradient(linear,left top,left bottom,color-stop(90%,rgba(0,0,0,1)),color-stop(90%,rgba(0,0,0,1)),color-stop(100%,rgba(40,40,40,1)),color-stop(100%,rgba(38,38,38,1)));background:-webkit-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:-o-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:-ms-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:linear-gradient(to bottom,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#000000\',endColorstr=\'#282828\',GradientType=0);opacity:.8;z-index:-1}img.logo{border-right:1px solid #666;border-left:1px solid #666;border-top:1px solid #666;z-index:105}div.caption:hover{color:white;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:underline}a.captionlink{color:white}div#paddingbottom{height:16px}div#content{position:static;padding-top:55px;padding-bottom:35px}div#page{position:absolute;left:50%;width:640px;margin-left:-320px}a:focus,a:active,button,input[type="reset"]::-moz-focus-inner,input[type="button"]::-moz-focus-inner,input[type="submit"]::-moz-focus-inner,select::-moz-focus-inner,input[type="file"]>input[type="button"]::-moz-focus-inner{outline:none !important}.popuplink{color:white !important}a#tl{font-size:.93em !important}</style>' . $this->style . '<title>' . $title . '</title></head><body><div class="greenpage"></div><div class="fh"><a href="/" id="tl"><i>' . $websiteName . '</i></a></div><div id="content"><h1>' . $header . '</h1>' . $content . '</div><div class="floatbg"></div><div class="floattb"><a href="/" class="floatlink">Home</a> | <a href="javascript:history.back();" class="floatlink">Previous page</a> | <a href="/r.php?c=news&amp;a=main" class="floatlink">News</a> | <a href="/r.php?c=events&amp;a=main" class="floatlink">Events</a> | <a href="/r.php?c=articles&amp;a=main" class="floatlink">Articles</a> | <div style="display:inline;height:25px;margin-bottom:-4px;"><div style="display:inline-block;z-index:200;" id="projecthoverdiv" onMouseOver="show();" onMouseOut="hide();"><img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="" onMouseOver="forcehide();" style="width:5px;height:30px;margin-left:-5px;margin-bottom:-5px;"><a class="floatlink" id="projectHoverLink" href="r.php?c=main&amp;a=projects">Projects</a><img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="" id="pngstretch" onMouseOver="forcehide();" style="width:5px;height:26px;margin-left:0px;margin-bottom:-5px;"></div></div><noscript><a href="r.php?c=main&amp;a=projects" class="floatlink">Projects</a></noscript></div><div id="projectsdisplaybg" style="position:fixed;bottom:25px;left:100px;width:250px;height:126px;background-color:#030007;opacity:.5;z-index:180;display:none;"><div onMouseOver="persistin();" id="projectsdisplaydiv" style="position:fixed;bottom:26px;left:101px;width:250px;height:126px;opacity:.8;color:white;z-index:200;border:1px dotted white;"><ul style="text-align:left;"><li style="color:white;"><a style="color:white;" class="popuplink" href="r.php?c=Wreathe&amp;a=main">Wreathe</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="r.php?c=Ember&amp;a=main">Ember</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="r.php?c=DCE&amp;a=main">DCE</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="r.php?c=main&amp;a=music">Music</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="r.php?c=main&amp;a=more-projects">More…</a></li></ul></div></div><div id="pt" style="position:fixed;bottom:22px;left:100px;width:250px;height:126px;z-index:180;" onMouseOver="persistin();"></div><div id="triggerout" style="position:fixed;bottom:25px;left:100px;width:282px;height:142px;z-index:102;" onMouseOut="forcehide();"></div><script>function getOffset(a){var b=0;var c=0;while(a&&!isNaN(a.offsetLeft)&&!isNaN(a.offsetTop)){b+=a.offsetLeft-a.scrollLeft;c+=a.offsetTop-a.scrollTop;a=a.offsetParent}return{top:c,left:b}}var persistvar=\'0\';var div=document.getElementById(\'projectsdisplaybg\');var bg=document.getElementById(\'projectsdisplaydiv\');var phd=document.getElementById(\'projecthoverdiv\');var pt=document.getElementById(\'pt\');var triggerout=document.getElementById(\'triggerout\');var leftedge=getOffset(phd).left;div.style.left=leftedge+\'px\';bg.style.left=leftedge+\'px\';pt.style.left=leftedge+\'px\';triggerout.style.left=(leftedge-16)+\'px\';window.onresize=function(){leftedge=getOffset(phd).left;div.style.left=leftedge+\'px\';div.style.backgroundColor=\'#030007\';bg.style.left=leftedge+\'px\';pt.style.left=leftedge+\'px\';triggerout.style.left=(leftedge-16)+\'px\'};function persistin(){div.style.backgroundColor=\'black\';persistvar=\'1\';div.style.opacity=\'.8\';bg.style.opacity=\'1\';div.style.border=\'1px solid white\';pt.style.zIndex=\'0\';triggerout.style.display=\'block\'}function persistout(){persistvar=\'0\';div.style.opacity=\'.5\';bg.style.opacity=\'.8\';div.style.border=\'1px dotted white\';div.style.backgroundColor=\'#030007\';pt.style.zIndex=\'180\';triggerout.style.display=\'none\'}function show(){div.style.opacity=\'.5\';bg.style.opacity=\'.8\';div.style.backgroundColor=\'#030007\';div.style.border=\'1px dotted white\';div.style.display=\'block\';pt.style.display=\'block\';triggerout.style.display=\'block\'}function hide(){if(persistvar==\'1\'){void(0)}else{div.style.display=\'none\';pt.style.display=\'none\';triggerout.style.display=\'none\'}}function forcehide(){div.style.backgroundColor=\'#030007\';div.style.display=\'none\';pt.style.display=\'none\';triggerout.style.display=\'none\';persistvar=\'0\';div.style.opacity=\'.5\';bg.style.opacity=\'.8\';div.style.border=\'1px dotted white\';pt.style.zIndex=\'180\'}</script><div style="position:fixed;bottom:35px;right:10px;z-index:115 !important;"><a href="https://twitter.com/Futuramerlin" class="twitter-follow-button" data-show-count="false" data-dnt="true">Follow @Futuramerlin</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script></div></body></html>'));
        echo $display;
    }
}

class Discography
{
    var $title;
    var $content;
    
    
    function __construct()
    {
        /*
        $this->id=$id;
        $this->content=$title;
        $this->title=$title;*/
    }
    
    
    function getMusic($id)
    {
        global $dataDirectory;
        $musicdata = file_get_contents($dataDirectory . '/s/music/r/' . $id . '.html');
        
        #reencode to utf8 if necessary
        
        if (strpos($musicdata, 'ISO-8859-1')) {
            $musicdata = str_replace("ISO-8859-1", "UTF-8", iconv("ISO-8859-1", "UTF-8", $musicdata));
        }
        
        #clean up html mess
        $musicdata = str_replace('<html>', '', $musicdata);
        $musicdata = str_replace('</html>', '', $musicdata);
        $musicdata = str_replace('<head>', '', $musicdata);
        $musicdata = str_replace('</head>', '', $musicdata);
        $musicdata = str_replace('<body>', '', $musicdata);
        $musicdata = str_replace('</body>', '', $musicdata);
        $musicdata = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">', '', $musicdata);
        $musicdata = str_replace('<meta content="text/html; charset=UTF-8" http-equiv="content-type">', '', $musicdata);
        $musicdata = preg_replace("/<title(.*)<\/title>/iUs", "", $musicdata);
        //$musicdata = preg_replace('/<a target="_blank" href="http:\/\/archive.org\/download\/(.*)"flac">&#128266;<\/a>/', '<a target="_blank" href="http://archive.org/download/' . '$1' . 'mp3">&#128266;</a>', $musicdata);
        
        $musicdata = preg_replace('/<a href="http:\/\/archive.org\/download\/(.*)\/(.*).flac">/', '<audio src="http://archive.org/download/$1/$2.mp3" preload="none" class="audioplayer">Could not start audio player. Does your browser support it?</audio><a href="http://archive.org/download/$1/$2.flac">', $musicdata);
        $musicdata = str_replace('&#128266;</a>', 'Lossless…</a>', $musicdata);
        
        
        
        $musicdata = str_replace('cellpadding="2" cellspacing="2"', '', $musicdata);
        
        $musicdata = str_replace('AnoeyFuturamerlincom', 'anoeyfuturamerlincommedium,AnoeyFuturamerlincom', str_replace('<img', ' <img', str_replace('src="a/', 'class="mlefti" src="d/s/music/r/a/', str_replace('href="a/', 'class="mlefti" href="d/s/music/r/a/', str_replace('href="../', 'href="d/s/music/', str_replace('href=', 'target="_blank" href=', str_replace('</h1>', '</h2>', str_replace('<h1>', '<h2>', $musicdata))))))));
        return $musicdata;
    }
    
    function addThumbnail($id, $title)
    {
        return '<img class="musicthumb" src="http://futuramerlin.com/d/s/music/r/a/' . $id . '/t1.png" alt="' . $title . '"  onClick="hideAll();document.getElementById(\'' . $id . '\').style.display=\'block\';">';
    }
    
    function addContent($id, $content = '')
    {
        //if(strlen($content)>0){$separator='';}else{$separator='<hr>';}
        $separator = '';
        
        return str_replace('<table style="text-align: left;" border="1" >', '<table style="text-align: left; width:100%;" border="1" >', str_replace('<div class="musicbig" id="itfaen">', '<div class="musicbig" id="itfaen" style="/*max-width:450px;*/">', '<div class="musicbig" id="' . $id . '">' . $content . $separator . getMusic($id) . '</div>'));
    }
}

class baggage_claim
{
    public $temp_temp_table;
    public $tableid;
    public $table;
    public $next;
    function check_luggage($variable, $new_content)
    {
        $this->$variable = $new_content;
    }
    function claim_luggage($variable)
    {
        return $this->$variable;
    }
}
global $baggage_claim;
$baggage_claim = new baggage_claim;

class FluidActive
{
    function __construct($title)
    {
        $this->globalIdsCounter = 1;
        $this->targetLocation   = '/d/r/active.php';
        $this->page             = '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<style type="text/css" media="all">table,tr,td{border:1px dotted maroon;}</style>
<title>' . $title . '</title>
</head>
<body>';
    }
    function newId()
    {
        $this->globalIdsCounter++;
        return 'fluidFragment' . $this->globalIdsCounter;
    }
    function getId()
    {
        return 'fluidFragment' . $this->globalIdsCounter;
    }
    function DBTableEntry($db, $table)
    {
        $idList = $db->listIds($table);
        $idData = explode_esc(',', $idList);
        foreach ($idData as $counter => $id) {
            $this->append('Counter: ' . $counter . '<br>');
            $this->append('Id: ' . $id . '<br>');
            $this->DBRowEntry($db, $table, $id);
        }
        // $counter=0;
        // $id=1;
        // $this->append('Counter: '.$counter.'<br>');
        // $this->append('Id: '.$id.'<br>');
        // $this->DBRowEntry($db,$table,$id);
        // $counter=1;
        // $id=2;
        // $this->append('Counter: '.$counter.'<br>');
        // $this->append('Id: '.$id.'<br>');
        // $this->DBRowEntry($db,$table,$id);
        
    }
    function DBRowEntry($db, $table, $id)
    {
        $result = $db->getRow($table, 'id', $id);
        #$result = $db->getTable($table);
        #$rowData=$result->fetchAll(PDO::FETCH_ASSOC);
        #print_r($result);
        #$rowDataArray=$result[0];
        $meta   = $db->getRowList($table);
        #print_r($meta);
        $this->append('<table><thead><tr>');
        
        $this->append('</tr></thead><tbody><tr id="' . $this->newId() . '"><td id="' . $this->newId() . '"><b>Row ' . $id . ':</b>');
        foreach ($result as $key => $value) {
            $rowType = $db->getRowType($table, $key);
            $this->append("\n" . '<td id="' . $this->newId() . '">');
            #$this->DBTextEntry($db,$table,$key,$id));
            if ($rowType == 'bool') {
                $this->append('bool not supported');
            } else {
                if ($rowType == 'text') {
                    $this->DBTextEntry($db, $table, $key, $id);
                } else {
                    if ($rowType == 'int') {
                        $this->DBIntegerEntry($db, $table, $key, $id);
                    }
                }
            }
            $this->append("</td>\n");
            
        }
        $this->append('</tr></tbody></table');
        
        //         for ($i = 0; $i < $numberOfRows; $i++) {
        //                    // print_r($db->getRowList($table)->fetch_array());
        //                     $arrayNextId  = 'rowDataArraySum' . $i;
        //                     $$arrayNextId = $db->getRowList($table)->fetch_assoc();
        //                 }        //         logAdd(print '<br><br>Result: <br>');
        //         print_r($result);
        //         $numberOfRows   = $db->getRowList($table)->num_rows;
        //         for ($i = 0; $i < $numberOfRows; $i++) {
        //             // print_r($db->getRowList($table)->fetch_array());
        //             $arrayNextId  = 'rowDataArraySum' . $i;
        //             $$arrayNextId = $db->getRowList($table)->fetch_assoc();
        //             logAdd('<br><br>'.$arrayNextId.' value: ');
        //             print_r($$arrayNextId);
        //         }
        //         for ($i = 0; $i < $numberOfRows; $i++) {
        //             $arrayrowlist = $arrayrowlist . '$rowDataArraySum' . $i . ',';
        //         }
        //         $mergeRowData = '$arrayOfRowData=array_merge(' . substr($arrayrowlist, 0, -1) . ');';
        //         logAdd('<br><br>Executing: '.$mergeRowData.'<br><br>');
        //         eval($mergeRowData)
        //         logAdd('<br><br>Sum: <br>');
        //         print_r($arrayOfRowData);
        #print_r($arrayOfRowData);
        //  for($i=0;$i<$numberOfRows;$i++){
        //         $arrayNextId='rowDataArraySum'.$i;
        //         #print_r($arrayNextId);
        //         $arrayOfRowData=array_merge($arrayOfRowData,$$arrayNextId);
        //         }
        //         while ($row = $result->fetch_assoc()) {
        //             echo '<li><a href="StudyMaster.php?a=pick_class&year=' . $result['id'] . '&why=facts">' . $result['location'] . '</a></li>';
        //         }
        //        
        //         $this->append('doom');
        //         #print_r($db->getRowList($table));
        //         #print_r($db->getRowList($table)->fetch_assoc());
        //         $numberOfRows   = $db->getRowList($table)->num_rows;
        //         #print $numberOfRows;
        //         $arrayOfRowData = array();
        //         for ($i = 0; $i < $numberOfRows; $i++) {
        //             #print_r($db->getRowList($table)->fetch_array());
        //             $arrayNextId  = 'rowDataArraySum' . $i;
        //             $$arrayNextId = $db->getRowList($table)->fetch_assoc();
        //         }
        //         $arrayrowlist = '';
        //         for ($i = 0; $i < $numberOfRows; $i++) {
        //             $arrayrowlist = $arrayrowlist . '$rowDataArraySum' . $i . ',';
        //         }
        //         $mergeRowData = '$arrayOfRowData=array_push(' . substr($arrayrowlist, 0, -1) . ');';
        //         print $mergeRowData;
        //         eval($mergeRowData);
        //         print_r($arrayOfRowData);
        #$mergeRowData='$arrayOfRowData=array_merge('.substr($arrayrowlist,0,-1).');';
        // for($i=0;$i<$numberOfRows;$i++){
        // $arrayNextId='rowDataArraySum'.$i;
        // #print_r($arrayNextId);
        // $arrayOfRowData=array_merge($arrayOfRowData,$$arrayNextId);
        // }
        
        #print_r($db->getRowList($table)->fetch_all());
        #http://stackoverflow.com/questions/6694437/mysqli-fetch-all-not-a-valid-function
        //         $results_array = array();
        //         #$result = $mysqli->query($query);
        //         while ($row = $db->getRowList($table)->fetch_assoc()) {
        //             $results_array[] = $row;
        //         }
        #http://stackoverflow.com/questions/6694437/mysqli-fetch-all-not-a-valid-function
        // while ($row = $db->getRowList($table)->fetch_all()) {
        // print 'doom';
        // }
        #http://stackoverflow.com/questions/13664659/php-mysqli-only-returns-one-row
        // while ($request_list_row = $db->getRowList($table)->fetch_array()) {
        //     print_r($request_list);
        // }
        #http://stackoverflow.com/questions/17288044/function-returns-only-first-array-value-with-mysqli-query-result
        // $data  = array();
        //   while ($row = $db->getRowList($table)->fetch_assoc()) {
        //      $data[] = $row;
        //   }
        // return $data;
        // print_r($rows);
        #http://www.php.net/manual/en/mysqli-result.fetch-assoc.php#112924
        #for($set=array();$row=$db->getRowList($table)->fetch_assoc();$set[]=$row);
    }
    function DBTextEntry($db, $table, $field, $id)
    #$db is a FractureDB instance
    {
        $this->newId();
        //   // http://kevin.vanzonneveld.net
        //   // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        //   // +   bugfixed by: Onno Marsman
        //   // +   bugfixed by: Linuxworld
        //   // +   improved by: ntoniazzi (http://phpjs.org/functions/bin2hex:361#comment_177616)
        //   // *     example 1: bin2hex(\'Kev\');
        //   // *     returns 1: \'4b6576\'
        //   // *     example 2: bin2hex(String.fromCharCode(0x00));
        //   // *     returns 2: \'00\'
        $fieldVal = hex2bin($db->getField($table, $field, $id));
        $this->append('Field type: Text.
<form method="post"><input type="text" value="' . $fieldVal . '" id="' . $this->getId() . '" onKeyPress="sync_' . $this->getId() . '();"></form>
    <script type="text/javascript">function bin2hex (s) {

  var i, l, o = "", n;

  s += "";

  for (i = 0, l = s.length; i < l; i++) {
    n = s.charCodeAt(i).toString(16);
    o += n.length < 2 ? "0" + n : n;
  }

  return o;
}

function sync_' . $this->getId() . '() {
     setTimeout(function () {   var ' . $this->getId() . ' = document.getElementById(\'' . $this->getId() . '\').innerHTML;

          var ' . $this->getId() . ' = bin2hex(document.getElementById(\'' . $this->getId() . '\').value);
alert(' . $this->getId() . ');

   var xmlhttp;
if (window.XMLHttpRequest)
  {
  xmlhttp=new XMLHttpRequest();
  }
else
  {
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  
  var send="handler=1&handlerNeeded=DBSimpleSubmissionHandler&authorizationKey=blablabla&db=' . $db->name . '&dataTargetTable=' . $table . '&dataTargetRowId=' . $id . '&dataTargetField=' . $field . '&dataValue="+' . $this->getId() . ';
xmlhttp.open("POST","' . $this->targetLocation . '",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

xmlhttp.send(send);
   }, 100);
}

</script>');
    }
    function DBIntegerEntry($db, $table, $field, $id)
    #$db is a FractureDB instance
    {
        $this->newId();
        
        $this->append('Field type: Integer. It only likes numbers.
<form method="post"><input type="text" value="' . $db->getField($table, $field, $id) . '" id="' . $this->getId() . '" onKeyPress="sync_' . $this->getId() . '();"></form>
    <script type="text/javascript">

function sync_' . $this->getId() . '() {
     setTimeout(function () {   var ' . $this->getId() . ' = document.getElementById(\'' . $this->getId() . '\').innerHTML;

          var ' . $this->getId() . ' = document.getElementById(\'' . $this->getId() . '\').value;


   var xmlhttp;
if (window.XMLHttpRequest)
  {
  xmlhttp=new XMLHttpRequest();
  }
else
  {
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  
  var send="handler=1&handlerNeeded=DBSimpleSubmissionHandler&authorizationKey=blablabla&db=' . $db->name . '&dataTargetTable=' . $table . '&dataTargetRowId=' . $id . '&dataTargetField=' . $field . '&dataValue="+' . $this->getId() . ';
xmlhttp.open("POST","' . $this->targetLocation . '",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

xmlhttp.send(send);
   }, 100);
}

</script>');
    }
    function filter_out($data)
    {
        global $websiteName;
        global $cssFile;
        global $serverUrl;
        global $pageClass;
        if ($pageClass == 'w') {
            $tbl = '';
        } else {
            $tbl = '<table border="0" cellpadding="24" width="100%"><tbody><tr><td><br><h1>';
        }
        $pageVersionDirect = '2';
        $univNeedles       = array(
            'href="' . $serverUrl,
            "\t",
            "\n",
            "\r",
            "  ",
            "  ",
            "  ",
            "> <",
            '@p1@',
            '@p2@',
            '@p3@',
            '@p4@',
            '@cssFile@',
            '@websiteName@',
            '@tbl@',
            '@l@',
            '@n@',
            'https://futuramerlincom.fatcow.com',
            '@greenhead@',
            '.css";</style></title>'
        );
        $univHaystacks     = array(
            'href="',
            " ",
            " ",
            " ",
            " ",
            " ",
            " ",
            '><',
            resl('1.d' . $pageVersionDirect),
            resl('2.d' . $pageVersionDirect),
            resl('3.d' . $pageVersionDirect),
            resl('4.d' . $pageVersionDirect),
            $cssFile,
            $websiteName,
            $tbl,
            '<a href="r.php?',
            '">',
            'http://localhost',
            '<div class="greenpage"></div><div class="fh"><a href="/" id="tl"><i>futuramerlin</i></a></div>',
            '.css";</style>'
        );
        return trim(preg_replace('/\s+/', ' ', str_replace("\n", ' ', str_replace($univNeedles, $univHaystacks, $data))));
    }
    function getQueryCount($db)
    {
        $this->append('<br><br>Page served using ' . $db->queryCount . ' queries.<br><br>');
    }
    function close()
    {
        $this->append('</body></html>');
        
        $this->writeOut();
    }
    function append($data)
    {
        $this->page = $this->page . $data;
    }
    function writeOut()
    {
        echo $this->filter_out($this->page);
    }
    
}
function dHandler($db, $query, $e, $qf = 'normal', $failed = False)
{
    #This function is part of FractureDB.
    global $displayDebugMessages;
    #based on http://stackoverflow.com/questions/12368561/deadlock-exception-code-for-php-mysql-pdoexception
    if ($e->errorInfo[0] == 40001 /*(ISO/ANSI) Serialization failure, e.g. timeout or deadlock*/ && $pdoDBHandle->getAttribute(PDO::ATTR_DRIVER_NAME == "mysql") && $exc->errorInfo[1] == 1213 /*(MySQL SQLSTATE) ER_LOCK_DEADLOCK*/ ) {
        if ($failed = False) {
            if ($qf = 'num') {
                $db->query_num($query, True);
            } else {
                $db->query($query, True);
            }
        } else {
            if ($displayDebugMessages) {
                print("Error: " . $e->getMessage());
            }
            throw $e;
        }
    } else {
        if ($displayDebugMessages) {
            print("Error: " . $e->getMessage());
        }
        throw $e;
    }
}
class FractureDB
{
    function __construct($name)
    {
        $this->name = $name;
        
        $this->queryCount = 0;
        global $db_data;
        #print_r($db_data);
        //echo '<br><br><font color="red">EXECUTING QUERY: ' . $query . '</font><br><br>';
        $username = $db_data[$this->name][0];
        $password = $db_data[$this->name][1];
        $host     = $db_data[$this->name][2];
        $this->db = new Database('mysql:host=' . $host . ';dbname=' . $this->name . ';charset=utf8', $username, $password);
        $dbh      = $this->db;
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->beginTransaction();
    }
    function UrlEscS($data)
    {
        $data = str_replace(' ', '%20', $data);
        $data = str_replace('\'', '%27', $data);
        $data = str_replace('<', '%3C', $data);
        $data = str_replace('>', '%3E', $data);
        $data = str_replace('[', '%5B', $data);
        $data = str_replace(']', '%5D', $data);
        $data = str_replace('(', '%28', $data);
        $data = str_replace(')', '%29', $data);
        return $data;
    }
    function close()
    {
        $dbh = $this->db;
        $dbh->commit();
    }
    function query($query, $failed = False)
    {
        #echo '<br><br><font color="red">EXECUTING QUERY: ' . $query . '</font><br><br>';
        $dbh = $this->db;
        $this->queryCount++;
        #http://pastebin.com/bbCRpA2m        
        try {
            $result = $dbh->prepare($query);
            $result->execute();
        }
        catch (PDOException $e) {
            dHandler($dbh, $query, $e, 'normal', True);
        }
        #print '<br><br>This query returned: <br>';
        #print_r($result->fetchAll(PDO::FETCH_ASSOC));
        #print '<br><br>';
        if (stripos($query, 'INSERT') === 0) {
            #query begins with INSERT
            #$query = 'SHOW TABLE STATUS LIKE \'' . $table . '\';';
            #return $this->query($query);
            return 'Inserted';
        }
        if (stripos($query, 'UPDATE') === 0) {
            #query begins with INSERT
            #$query = 'SHOW TABLE STATUS LIKE \'' . $table . '\';';
            #return $this->query($query);
            return 'Updated';
        }
        return $result->fetchAll(Database::FETCH_ASSOC);
    }
    function query_num($query, $failed = False)
    {
        #echo '<br><br><font color="red">EXECUTING QUERY_NUM: ' . $query . '</font><br><br>';
        $dbh = $this->db;
        $this->queryCount++;
        #http://pastebin.com/bbCRpA2m        
        try {
            $result = $dbh->prepare($query);
            $result->execute();
        }
        catch (PDOException $e) {
            dHandler($dbh, $query, $e, 'num', True);
        }
        #print '<br><br>This query returned: <br>';
        #print_r($result->fetchAll(PDO::FETCH_ASSOC));
        #print '<br><br>';
        return $result->fetchAll(Database::FETCH_NUM);
    }
    function queryInsert($query)
    {
        global $displayDebugMessages;
        #echo '<br><br><font color="red">EXECUTING QUERYINSERT: ' . $query . '</font><br><br>';
        $dbh = $this->db;
        $this->queryCount++;
        #http://pastebin.com/bbCRpA2m        
        try {
            $result = $dbh->prepare($query);
            #print_r($result);
            $result->execute();
            #print $query;
        }
        catch (PDOException $e) {
            if ($displayDebugMessages) {
                print("Error: " . $e->getMessage());
            }
        }
        #print '<br><br>This query returned: <br>';
        #print_r($result->fetchAll(PDO::FETCH_ASSOC));
        #print '<br><br>';
        return $dbh->lastInsertId();
    }
    function getRowList($table)
    {
        #explain extended select * from am_urls; show warnings;
        #return $this->query('SHOW columns FROM '.$table);
        #return $this->query('SELECT GROUP_CONCAT(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name=\''.$table.'\'');
        return $this->query('SELECT COLUMN_NAME,DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name=\'' . $table . '\'');
    }
    function getRowType($table, $key)
    {
        #explain extended select * from am_urls; show warnings;
        #return $this->query('SHOW columns FROM '.$table);
        #return $this->query('SELECT GROUP_CONCAT(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name=\''.$table.'\'');
        $resultAll      = $this->query('SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name=\'' . $table . '\' AND column_name=\'' . $key . '\'');
        $resultFiltered = str_replace('longtext', 'text', str_replace('bigint', 'int', $resultAll[0]['DATA_TYPE']));
        return $resultFiltered;
    }
    function getNextId($table)
    {
        $query = 'SHOW TABLE STATUS LIKE \'' . $table . '\';';
        return $this->query($query);
    }
    function getTable($table)
    {
        $query = 'SELECT * FROM ' . $table . ' ORDER BY id;';
        return $this->query($query);
    }
    function countTable($table, $filterField = '', $filterValue = '', $additions = '')
    {
        if ($filterField !== '') {
            $queryInsert = ' WHERE ' . $filterField . ' = \'' . $filterValue . '\'';
        } else {
            $queryInsert = '';
        }
        $query = 'SELECT COUNT(*) FROM ' . $table . $queryInsert . $additions . ';';
        #echo "\n".$query."\n";
        return $this->query($query);
    }
    function countRows($table, $additions = '')
    {
        $query = 'SELECT COUNT(*) FROM `' . $table . '`' . $additions . ';';
        #echo "\n".$query."\n";
        return $this->query($query);
    }
    function listIds($table)
    {
        $query      = 'SELECT GROUP_CONCAT(id) FROM ' . $table . ';';
        $qResLevel1 = $this->query($query);
        $qResLevel2 = $qResLevel1[0];
        #print_r($qResLevel2);
        return $qResLevel2['GROUP_CONCAT(id)'];
    }
    function getRow($table, $field, $value)
    {
        $query    = 'SELECT * FROM ' . $table . ' WHERE ' . $field . ' = \'' . $value . '\';';
        $rowData  = $this->query($query);
        $rowDataP = $rowData[0];
        return $rowDataP;
    }
    function getRows($table, $field, $value)
    {
        $query   = 'SELECT * FROM ' . $table . ' WHERE `' . $field . '` = \'' . $value . '\';';
        $rowData = $this->query($query);
        return $rowData;
    }
    function getRandomRow($table, $filterField = '', $filterValue = '', $idFieldName = 'id', $limit = 1)
    {
        if ($filterField !== '') {
            $queryInsert = $filterField . ' = \'' . $filterValue . '\' ';
        } else {
            $queryInsert = '';
        }
        $query    = 'SELECT * FROM `' . $table . '` WHERE ' . $queryInsert . 'ORDER BY RAND() LIMIT ' . $limit . ';';
        #echo "\n".$query."\n";
        $rowData  = $this->query($query);
        $rowDataP = $rowData[0];
        return $rowDataP;
    }
    function getNextRow($table, $filterField = '', $filterValue = '', $idFieldName = 'id', $limit = 1)
    {
        if ($filterField !== '') {
            $queryInsert = $filterField . ' = \'' . $filterValue . '\' ';
        } else {
            $queryInsert = '';
        }
        $query    = 'SELECT * FROM `' . $table . '` WHERE ' . $queryInsert . ' LIMIT ' . $limit . ';';
        #echo "\n".$query."\n";
        $rowData  = $this->query($query);
        $rowDataP = $rowData[0];
        return $rowDataP;
    }
    function getNextRowEF($table, $filterField = '', $filterValue = '', $custom = '', $idFieldName = 'id', $limit = 1)
    {
        if ($filterField !== '') {
            $queryInsert = $filterField . ' = \'' . $filterValue . '\' ';
        } else {
            $queryInsert = '';
        }
        //         if ($custom !== '') {
        //             $queryInsert2 = $filterField2 . ' = \'' . $filterValue2 . '\' ';
        //         } else {
        //             $queryInsert2 = '';
        //         }
        $query    = 'SELECT * FROM `' . $table . '` WHERE ' . $queryInsert . ' AND ' . $custom . ' LIMIT ' . $limit . ';';
        #echo "\n".$query."\n";
        $rowData  = $this->query($query);
        $rowDataP = $rowData[0];
        return $rowDataP;
    }
    function getField($table, $field, $id)
    {
        $query    = 'SELECT ' . $field . ' FROM ' . $table . ' WHERE id = ' . $id . ';';
        $rowData  = $this->query($query);
        $rowDataP = $rowData[0];
        #print_r($rowDataP);
        #echo $rowDataP[$field];
        return $rowDataP[$field];
    }
    function getColumn($table, $field, $filterField = '', $filterValue = '')
    {
        if (strlen($filterValue) !== 0) {
            $queryInsert = ' WHERE ' . $filterField . ' = \'' . $filterValue . '\'';
        } else {
            $queryInsert = '';
        }
        $query = 'SELECT ' . $field . ' FROM ' . $table . ' ' . $queryInsert . ';';
        return $this->query($query);
    }
    function getColumn_num($table, $field, $filterField = '', $filterValue = '')
    {
        if (strlen($filterValue) !== 0) {
            $queryInsert = ' WHERE ' . $filterField . ' = \'' . $filterValue . '\'';
        } else {
            $queryInsert = '';
        }
        $query = 'SELECT ' . $field . ' FROM ' . $table . ' ' . $queryInsert . ';';
        return $this->query_num($query);
    }
    function setField($table, $field, $value, $id = '')
    {
        $query = 'INSERT INTO ' . $table . ' (id, ' . $field . ') VALUES (' . $id . ',\'' . $value . '\') ON DUPLICATE KEY UPDATE ' . $field . ' = \'' . $value . '\';';
        $this->query($query);
    }
    function addRow($table, $fields, $values)
    {
        $query    = 'INSERT INTO ' . $table . ' (' . $fields . ') VALUES (' . $values . ');';
        $newRowId = $this->queryInsert($query);
        return $newRowId;
    }
    function addRowFuzzy($table, $fields, $values)
    {
        $query    = 'INSERT INTO ' . $table . ' (' . $fields . ') VALUES (' . $values . ') ON DUPLICATE KEY UPDATE id=id;';
        #$query    = 'INSERT INTO ' . $table . ' (' . $fields . ') VALUES (' . $values . ');';
        #echo '<br><br><font color="red">EXECUTING addRowfuzzy QUERY: ' . $query . '</font><br><br>';
        $newRowId = $this->queryInsert($query);
        return $newRowId;
    }
    function updateColumn($table, $field, $value, $filterField = 'id', $filterValue = '')
    {
        if (strlen($filterValue) !== 0) {
            $queryInsert = ' WHERE ' . $filterField . ' = \'' . $filterValue . '\'';
        } else {
            $queryInsert = '';
        }
        $query = 'UPDATE `' . $table . '` SET ' . $field . ' = \'' . $value . '\'' . $queryInsert . ';';
        $this->query($query);
        return $query;
    }
    function fuzzyMatchGetRow($table, $field, $filterField, $filterValue, $limit = '')
    {
        if (strlen($limit) !== 0) {
            $limitIns = ' LIMIT ' . $limit;
        } else {
            $limitIns = '';
        }
        # from http://stackoverflow.com/questions/6447899/select-where-row-value-contains-string-mysql
        $query = 'SELECT ' . $field . ' FROM ' . $table . ' WHERE ' . $filterField . ' LIKE \'%' . $filterValue . '%\'' . $limitIns . ';';
        return $this->query($query);
        
        
    }
    
    
}
#INSERT USEFUL GENERIC FUNCTIONS BELOW THIS LINE
function resl($name)
{
    global $dataDirectory;
    return file_get_contents('../../' . $dataDirectory . $name);
}
//explode_escaped (not written by me)
function explode_esc($delimiter, $string)
{
    $exploded = explode($delimiter, $string);
    $fixed    = array();
    for ($k = 0, $l = count($exploded); $k < $l; ++$k) {
        if ($exploded[$k][strlen($exploded[$k]) - 1] == '\\') {
            if ($k + 1 >= $l) {
                $fixed[] = trim($exploded[$k]);
                break;
            }
            $exploded[$k][strlen($exploded[$k]) - 1] = $delimiter;
            $exploded[$k] .= $exploded[$k + 1];
            array_splice($exploded, $k + 1, 1);
            --$l;
            --$k;
        } else
            $fixed[] = trim($exploded[$k]);
    }
    return $fixed;
}


#from http://comments.gmane.org/gmane.mail.squirrelmail.plugins/9672
if (!function_exists("hex2bin")) {
    function hex2bin($data)
    {
        /* Original code by josh <at> superfork.com */
        
        $len     = strlen($data);
        $newdata = '';
        for ($i = 0; $i < $len; $i += 2) {
            $newdata .= pack("C", hexdec(substr($data, $i, 2)));
        }
        return $newdata;
    }
}
function Rq($name)
{
    if (isset($_REQUEST[$name])) {
        return $_REQUEST[$name];
    } else {
        return '';
    }
}
if (!function_exists("gzdecode")) {
    function gzdecode($gzdata, $maxlen = NULL)
    {
        #from http://include-once.org/p/upgradephp/
        #-- decode header
        $len = strlen($gzdata);
        if ($len < 20) {
            return;
        }
        $head = substr($gzdata, 0, 10);
        $head = unpack("n1id/C1cm/C1flg/V1mtime/C1xfl/C1os", $head);
        list($ID, $CM, $FLG, $MTIME, $XFL, $OS) = array_values($head);
        $FTEXT    = 1 << 0;
        $FHCRC    = 1 << 1;
        $FEXTRA   = 1 << 2;
        $FNAME    = 1 << 3;
        $FCOMMENT = 1 << 4;
        $head     = unpack("V1crc/V1isize", substr($gzdata, $len - 8, 8));
        list($CRC32, $ISIZE) = array_values($head);
        
        #-- check gzip stream identifier
        if ($ID != 0x1f8b) {
            trigger_error("gzdecode: not in gzip format", E_USER_WARNING);
            return;
        }
        #-- check for deflate algorithm
        if ($CM != 8) {
            trigger_error("gzdecode: cannot decode anything but deflated streams", E_USER_WARNING);
            return;
        }
        
        #-- start of data, skip bonus fields
        $s = 10;
        if ($FLG & $FEXTRA) {
            $s += $XFL;
        }
        if ($FLG & $FNAME) {
            $s = strpos($gzdata, "\000", $s) + 1;
        }
        if ($FLG & $FCOMMENT) {
            $s = strpos($gzdata, "\000", $s) + 1;
        }
        if ($FLG & $FHCRC) {
            $s += 2; // cannot check
        }
        
        #-- get data, uncompress
        $gzdata = substr($gzdata, $s, $len - $s);
        if ($maxlen) {
            $gzdata = gzinflate($gzdata, $maxlen);
            return ($gzdata); // no checks(?!)
        } else {
            $gzdata = gzinflate($gzdata);
        }
        
        #-- check+fin
        $chk = crc32($gzdata);
        if ($CRC32 != $chk) {
            trigger_error("gzdecode: checksum failed (real$chk != comp$CRC32)", E_USER_WARNING);
        } elseif ($ISIZE != strlen($gzdata)) {
            trigger_error("gzdecode: stream size mismatch", E_USER_WARNING);
        } else {
            return ($gzdata);
        }
    }
}
function get_url($url)
#From http://www.howtogeek.com/howto/programming/php-get-the-contents-of-a-web-page-rss-feed-or-xml-file-into-a-string-variable/ and http://stackoverflow.com/questions/5522636/get-file-content-from-a-url
    
# and from http://stackoverflow.com/questions/13988365/what-are-the-possible-reasons-for-curl-error-60-on-an-https-site
{
    $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
    $crl       = curl_init();
    $timeout   = 30;
    curl_setopt($crl, CURLOPT_URL, $url);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($crl, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($crl, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($crl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
    #print_r($crl);
    $ret      = curl_exec($crl);
    $error_no = curl_errno($crl);
    curl_close($crl);
    if ($error_no == 0) {
        echo '';
    } else {
        echo $error_no;
    }
    return $ret;
}
function get_domain($url)
#from http://stackoverflow.com/questions/16027102/get-domain-name-from-full-url
{
    $pieces = parse_url($url);
    $domain = isset($pieces['host']) ? $pieces['host'] : '';
    if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
        return $regs['domain'];
    }
    return false;
}
function get_domain_simple($url)
{
    $paros   = parse_url($url);
    $host    = $paros['host'];
    $host_ex = explode('.', $host);
    $frags   = count($host_ex);
    $hostr   = $host_ex[$frags - 2] . '.' . $host_ex[$frags - 1];
    if ($host_ex[$frags - 2] == 'yahoo' | $host_ex[$frags - 3] == 'yahoo') {
        $hostr = $host_ex[$frags - 3] . '.' . $host_ex[$frags - 2] . '.' . $host_ex[$frags - 1];
    }
    echo "<br>\n<br>\nDetected potential project: $hostr<br>\n<br>\n";
    return $hostr;
}


#INSERT SPECIFIC FUNCTIONS BELOW THIS LINE

function arcmaj3_return_barrel($db, $newBarrelId, $urlsPerBucket = 1, $projectsToCrawl = '')
{
    #echo 'returning barrel';
    $projects = $db->getColumn_num('am_projects', 'id', 'status', '0');
    $prArrayv = array();
    foreach ($projects as $key => $value) {
        $prArrayv = array_merge($prArrayv, $value);
    }
    #print_r($prArrayv);
    
    #$projects              = $db->getRandomRow('am_projects', 'status', '0', 'id', $urlsPerBucket);
    $pArray = explode(',', $projectsToCrawl);
    #print_r($pArray);
    #$pInt is a list of project IDs to crawl.
    $pInt   = array();
    #If the user has specified which buckets to call, populate $pInt with those choices that are present in the Projects table. Otherwise, populate it with all available projects with status 0.
    if (strlen($projectsToCrawl) !== 0) {
        #echo 'Using this.';
        #echo 'Intersecting:';
        #print_r($prArrayv);
        
        #echo 'with';
        #print_r($pArray);
        #echo 'yields:';
        $pInt = array_intersect($prArrayv, $pArray);
        #print_r($pInt);
        // if(count($pInt)==0){
        // #No projects suggested matched; return all projects
        //  $pInt = $projects;
        //  } 
    }
    
    else {
        $pInt = $projects;
        #print_r($pInt);
        #echo 'Using that.';
    }
    // $returnBarrel='';
    //     foreach ($pInt as $key => $value)
    // {
    // #foreach ($value as $key => $value){
    // $rowToAppend = $db->getRandomRow('am_urls', 'barrel', $value);
    // $returnBarrel = $returnBarrel + $rowToAppend['location'] + '\n';
    // #}
    // }
    //     foreach ($pInt as $key => $value) {
    //         $rowToAppend = $db->getRandomRow('am_urls', 'project', $value);
    //         $db->setField('am_urls', 'barrel', $newBarrelId, $rowToAppend['id']);
    //         $pInt[$key] = $rowToAppend['location'];
    //     }
    #echo '$pInt=';print_r($pInt);echo '.';
    $barrelRet  = '';
    #echo "\n\nENTERING DEBUG SECTION\n\n";
    #print_r($pInt);
    $urlCounter = 0;
    //     echo 'Urls per bucket: '. $urlsPerBucket . ".\n";
    while ($urlCounter < $urlsPerBucket) {
        //         echo 'Entered URL choice loop';
        //         echo $urlCounter . "\n";
        $randomChoice = array_rand($pInt);
        #echo $randomChoice . "\n";
        #echo '$randomChoice=';print_r($randomChoice);echo '.';
        #$randChInd    = $randomChoice[0];
        #echo $randChInd . "\n";
        $randomPA     = $pInt[$randomChoice];
        if (gettype($randomPA) == 'array') {
            $randomP = $randomPA[0];
        } else {
            $randomP = $randomPA;
        }
        #print_r($randomPA);
        #echo '$randomPA=';print_r($randomPA);echo '.';
        #echo 'RandomP='.$randomP . ".\n";
        #$rowToAppend = $db->getNextRow('am_urls', 'project', $randomP);
        $rowToAppend = $db->getNextRowEF('am_urls', 'project', $randomP, 'barrel = 0');
        #echo '$rowToAppend=';print_r($rowToAppend);echo '.';
        //         if ($rowToAppend['barrel'] !== '0') {
        //             #do nothing, the URL is already taken
        //             echo '';
        //         } else {
        echo '';
        if ($rowToAppend['project'] == '0') {
            #This is a stopgap measure until the # of barrel 0 rows goes down. Remove this block of code then.
            #What this block of code does is check each row chosen to get added to the barrel that has its Project listed as 0, check it against the Projects table, and if it matches an available project, update the URLs table to reflect that.
            #All URL patterns are in $pps.
            $pps              = $db->getColumn('am_projects', 'urlPattern');
            $testProjects     = False;
            $potentialProject = '';
            //$pp=fuzzyMatchGetRow('am_projects','projectId','urlPattern','',$limit='')['projectId'];
            //print_r($pps);
            foreach ($pps as $ppid) {
                #Operate on each URL pattern from the Projects table. If the selected row contains a URL pattern, set $testProjects to True and $potentialProject to the URL pattern it matched.
                if (stripos($rowToAppend['location'], $ppid['urlPattern']) !== false) {
                    $testProjects     = True;
                    $potentialProject = $ppid['urlPattern'];
                }
            }
            #$potentialProject = get_domain_simple($value);
            #$projects contains the row from Projects corresponding to the URL pattern that matched the selected URL.
            $projects  = $db->getRow('am_projects', 'urlPattern', $potentialProject);
            #$projectId contains the ID of the project that the current URL matched. If the matched ID is other than 0, update the URLs table to reflect this.
            $projectId = $projects['id'];
            if ($projectId !== 0) {
                $db->setField('am_urls', 'project', $projectId, $rowToAppend['id']);
            }
        }
        #print_r($rowToAppend);
        #The chosen URL has now been updated if necessary. Update the barrel column to reflect its addition to the barrel.
        
        $UrlNotAppended = False;
        #Next line can be commented out for debugging this function. UNCOMMENT WHEN DONE.
        if ($rowToAppend['id'] > 1) {
            $db->setField('am_urls', 'barrel', $newBarrelId, $rowToAppend['id']);
        } else {
            $UrlNotAppended = True;
        }
        
        
        #$pInt[$randChInd] = $rowToAppend['location'];
        #If the URL is already in the barrel, do nothing. Otherwise, add it to the barrel.
        if ((stripos($barrelRet, $rowToAppend['id']) !== false) | $UrlNotAppended === True) {
            #if (stripos($barrelRet, $rowToAppend['id']) !== false) {
            #do nothing, the URL is already in here
            echo '';
        } else {
            $barrelRet = $barrelRet . $rowToAppend['location'] . "\n";
            $urlCounter++;
        }
        #}
    }
    #print_r($pInt);
    #echo "\n\nLEAVING DEBUG SECTION\n\n";
    
    #print_r($pInt);
    #$returnBarrel = implode("\n", $pInt);
    #print_r($returnBarrel);
    $returnBarrel = $barrelRet;
    #$returnBarrel = 'Test barrel result.';
    return $returnBarrel;
}
function arcmaj3_barrel_expire($barrelId)
{
    echo 'Expiring barrel ' . $barrelId . '...<br>' . "\n";
    $db = new FractureDB('futuqiur_arcmaj3');
    $db->updateColumn('am_urls', 'barrel', '0', 'barrel', $barrelId);
    $db->setField('am_barrels', 'status', '2', $barrelId);
    echo 'Expired barrel ' . $barrelId . '.<br>' . "\n";
    $db->close();
}
#INSERT REQUEST RESPONDERS BELOW THIS LINE
#Arcmaj3
// $db             = new FractureDB('futuqiur_arcmaj3');
// $pps              = $db->getColumn('am_projects', 'urlPattern');
//             $testProjects     = False;
//             $potentialProject = '';
//             print_r($pps);
function wordlist_handler()
{
    $dataValue = Rq('wordToCheck');
    $db        = new FractureDB('futuqiur_wordlist');
    $data      = $db->getRows('words', 'word', $dataValue);
    $db->close();
    #print_r($data);
    if (count($data) !== 0) {
        echo '1';
    } else {
        echo '0';
    }
}
// function array_deep_search($array,$needle){
// $step1=array_map(in_array())
// }
function arcmaj3_handler()
{
    #verd: Arcmaj3 protocol version ID
    #echo Rq('verd');
    if (Rq('verd') == '2') {
        #do things...
        if (Rq('amtask') == 'up') {
            echo 'Uploaded barrel notification received.';
            #Client has finished a barrel. Parse new URL list provided and add to URLs table. Mark barrel's original URLs as completed, excluding URLs that ran into problems. Mark barrel as finished.
            #For now, all that will be provided here is an Internet Archive identifier.
            #Check for duplicates. Add new unique URLs matching intake criteria (matching an existing project) to URLs table.
            //         $ulBarrel   = file_get_contents($_FILES['uploadedBarrelData']['tmp_name']);
            //         $ulFailed   = file_get_contents($_FILES['failedUrlData']['tmp_name']);
            $BarrelUrlListLoc = 'https://archive.org/download/' . Rq('amloc') . '/' . 'URLs.lst';
            $uBarrelData      = get_url($BarrelUrlListLoc);
            #echo $uBarrelData;
            #echo $uBarrelData;
            echo "\n\n" . 'List data URL: ' . $BarrelUrlListLoc;
            $BarrelFailedListLoc = 'https://archive.org/download/' . Rq('amloc') . '/' . 'failed.lst';
            $uBarrelFailed       = get_url($BarrelFailedListLoc);
            #echo gzdecode($uBarrelFailed);
            echo "\n\n" . 'Failed entry data URL: ' . $BarrelFailedListLoc . "\n\n";
            echo "Decoding barrel data...\n";
            $ulBarrel = preg_replace('/[\n]+/', "\n", str_replace('\nhttp://http://', 'http://', gzdecode($uBarrelData)));
            #echo 'ulBarrel:' . "\n\n";
            //print_r($ulBarrel);
            echo "\n\n";
            echo "\n\n";
            echo "Decoding failed URL data...\n";
            $ulFailed   = preg_replace('/[\n]+/', "\n", gzdecode($uBarrelFailed));
            $ulBarrel   = str_replace("\r", "\n", $ulBarrel);
            $ulFailed   = str_replace("\r", "\n", $ulFailed);
            $barrelData = explode("\n", $ulBarrel);
            echo '<pre>';
            print_r($barrelData);
            echo "\n\n";
            $failedData = explode("\n", $ulFailed);
            print_r($failedData);
            echo '</pre>';
            $barrelId       = $barrelData[0];
            $barrelUserName = $barrelData[1];
            $db             = new FractureDB('futuqiur_arcmaj3');
            $urlsFinished   = $db->getRows('am_urls', 'barrel', $barrelId);
            $urlsFinished   = $urlsFinished[0];
            $barrelSize     = Rq('barrelSize');
            #Set status to 1. Set who to $barrelUserName.
            #$db->setField('am_barrels', 'who', $barrelUserName, $barrelId);
            $db->setField('am_barrels', 'size', $barrelSize, $barrelId);
            #$pps              = $db->getColumn('am_projects', 'urlPattern');
            $pptb = $db->getTable('am_projects');
            #echo '<H1>ENTERING GENERAL ROW PROCESSING</H1>';
            foreach ($barrelData as $value) {
                #Add URL to URL list.
                $testProjects     = False;
                $potentialProject = '';
                //$pp=fuzzyMatchGetRow('am_projects','projectId','urlPattern','',$limit='')['projectId'];
                //print_r($pps);
                #echo "\n\n<br><br><hr><br><br>Beginning processing url: ".$value.".\n\n<br><br>";
                #echo '<pre>';
                // foreach ($pps as $ppid) {
                //                     if (stripos($value, $ppid['urlPattern']) !== false) {
                //                         $testProjects     = True;
                //                         #$potentialProject = $ppid['id'];
                //     echo '$ppid=';print_r($ppid);echo '.';
                //     #echo '$pptb=';print_r($pptb);echo '.';
                //                         foreach ($pptb as $value) {
                // if(in_array($ppid['urlPattern'],$value))
                // {
                //                         $potentialProjectA = $value;
                // 
                // }
                // }
                // #                        $potentialProjectA = array_search($ppid['urlPattern'],$pptb);
                // 
                //                         #$ppAstep1=array_map(in_array)
                //                         $potentialProject = $potentialProjectA['id'];
                //                     }
                //                 }
                
                foreach ($pptb as $pprow) {
                    
                    if (stripos($value, $pprow['urlPattern']) !== false) {
                        $potentialProjectA = $pprow;
                        $testProjects      = True;
                    }
                    
                }
                if (isset($potentialProjectA)) {
                    $potentialProject = $potentialProjectA['id'];
                }
                
                
                #echo '$potentialProjectA=';print_r($potentialProjectA);echo '.';
                #echo '$potentialProject=';print_r($potentialProject);echo '.';
                #echo '</pre>';
                
                #$potentialProject = get_domain_simple($value);
                #$projects  = $db->getRow('am_projects', 'id', $potentialProject);
                //                 $projects  = $potentialProjectA;
                //                 $projectId = $projects['id'];
                $projectId = $potentialProject;
                #$projectId=1;
                if ($testProjects) {
                    $newUrlId = $db->addRowFuzzy('am_urls', 'location, project, locationHashUnique, originBarrel', "'" . $db->UrlEscS($value) . "', '" . $projectId . "', '" . hash('sha512', $db->UrlEscS($value)) . "', '" . $barrelId . "';");
                }
                //                 echo "<br>\n";
                //                 echo 'Added/updated row ';
                //                 echo $newUrlId;
                //                 echo ', URL ';
                //                 echo $value;
                //                 echo "<br>\n";
                
            }
            #echo '<H1>EXITING GENERAL ROW PROCESSING</H1>';
            //             foreach ($urlsFinished as $value) {
            //                 #Set completed to true.
            //                 $db->setField('am_urls', 'completed', 1, $value);
            //             }
            //             $pps              = $db->getColumn('am_projects', 'urlPattern');
            #$pptb              = $db->getTable('am_projects');
            
            #echo '<H1>ENTERING FAILED ROW PROCESSING</H1>';
            foreach ($failedData as $value) {
                
                #TODO: Increment failedAttempts, set completed to false, set barrel to 0
                #echo "\n\n<br><br><hr><br><br>Beginning working with failed row: ".$value.".\n\n<br><br>";
                $testProjects     = False;
                $potentialProject = '';
                //$pp=fuzzyMatchGetRow('am_projects','projectId','urlPattern','',$limit='')['projectId'];
                //print_r($pps);
                
                foreach ($pptb as $pprow) {
                    
                    if (stripos($value, $pprow['urlPattern']) !== false) {
                        $potentialProjectA = $pprow;
                        $testProjects      = True;
                    }
                    
                }
                $potentialProject = $potentialProjectA['id'];
                /*
                foreach ($pps as $ppid) {
                if (stripos($value, $ppid['urlPattern']) !== false) {
                $testProjects     = True;
                $potentialProjectA = array_search($ppid['urlPattern'],$pptb);
                $potentialProject = $potentialProjectA['id'];
                }
                }*/
                #$potentialProject = get_domain_simple($value);
                #                $projects  = $db->getRow('am_projects', 'id', $potentialProject);
                /*                 $projects  = $potentialProjectA;
                $projectId = $projects['id'];*/
                $projectId        = $potentialProject;
                $db->addRowFuzzy('am_urls', 'location, project, locationHashUnique, originBarrel', "'" . $db->UrlEscS($value) . "', '" . $projectId . "', '" . hash('sha512', $db->UrlEscS($value)) . "', '" . $barrelId . "'");
                #$failedRowIdRecord = $db->getRow('am_urls', 'location', $db->UrlEscS($value));
                $failedRowIdRecordA = $db->query("SELECT id, failedAttempts FROM `am_urls` WHERE location = '" . $db->UrlEscS($value) . "'");
                $failedRowIdRecord  = $failedRowIdRecordA[0];
                $failedRowId        = $failedRowIdRecord['id'];
                #echo "\n\n<br><br><hr><br><br>Beginning working with failed row (ID $failedRowId): ".$value.".\n\n<br><br>";
                
                #echo "\n\nWorking with failed row $value, ID $failedRowId";
                //                 $currentFailed = $db->getField('am_urls', 'failedAttempts', $failedRowId);
                //                 $currentFailed++;
                $db->query('UPDATE `am_urls` SET failedAttempts = failedAttempts+1, completed=0 WHERE id=' . $failedRowId . ';');
                //                 $db->setField('am_urls', 'failedAttempts', $currentFailed, $failedRowId);
                //                 $db->setField('am_urls', 'completed', 0, $failedRowId);
                #Permanently fail URLs that have failed 100 times.
                if ($failedRowIdRecord['failedAttempts'] < 100) {
                    $db->setField('am_urls', 'barrel', 0, $failedRowId);
                }
            }
            #echo '<H1>EXITING FAILED ROW PROCESSING</H1>';
            
            $db->setField('am_barrels', 'status', 1, $barrelId);
            $db->close();
            if (count($barrelData) < 2) {
                echo 'Barrel upload failed. Expiring barrel ' . $barrelId . '…';
                #The barrel data array should always have at least two things in it, I'm saying. Could be a one-URL barrel I suppose, but even that should get listed twice (once for Wget, once for Heritrix)
                arcmaj3_barrel_expire($barrelId);
                echo 'Expired barrel ' . $barrelId . '.';
            }
            echo "\n\n<br><br>\n\nBarrel notification processing finished.";
            
        } else {
            if (Rq('amtask') == 'down') {
                #Client wants a barrel. Compile random URLs from the URL table of unfinished URLs and create a barrel. Mark the URLs as taken. Add barrel to barrels table.
                #For now, choose an uncompleted URL from the table, and send it. 
                #Number of URLs per bucket:
                #Default
                $urlsPerBucket = 20;
                #Override
                if (strlen(Rq('NSConfLmDs')) !== 0) {
                    $urlsPerBucket = Rq('NSConfLmDs');
                } else {
                    $urlsPerBucket = $urlsPerBucket;
                }
                #Barrel format 0.1:
                #ID,0xURL\n            
                $db          = new FractureDB('futuqiur_arcmaj3');
                #Make a new barrel.
                $newBarrelId = $db->addRow('am_barrels', 'status, who, dateAssigned', "'0', '" . Rq('userName') . "', '" . date('Y') . "-" . date('m') . "-" . date('d') . "'");
                echo $newBarrelId . "\n";
                $projCrawlRq = Rq('projectsToCrawl');
                $barrel      = arcmaj3_return_barrel($db, $newBarrelId, $urlsPerBucket, $projCrawlRq);
                echo $barrel;
                //             while ($urlCounter < $urlsPerBucket) {
                //                 $rowToReturn = $db->getRandomRow('am_urls', 'barrel', '0');
                //                 $db->setField('am_urls', 'barrel', $newBarrelId, $rowToReturn['id']);
                //                 #echo $rowToReturn['id'].','.$rowToReturn['location'];
                //                 #Barrel format 0.11:
                //                 #0xURL\n
                //                 echo $rowToReturn['location'] . "\n";
                //                 $urlCounter++;
                //             }
                #echo '<br><br>Page served using ' . $db->queryCount . ' queries.<br><br>';
                $db->close();
                // echo "http://drive.google.com/\n";
                // echo "http://wretch.cc/\n";
                // echo "http://example.com/\n";
                // echo "http://comments.gmane.org/gmane.mail.squirrelmail.plugins/9672\n";
                // echo "http://futuramerlin.com\n";
                // echo "http://archive.org\n";
                // echo "https://archive.org\n";
            } else {
                if (Rq('amtask') == 'expireBarrel') {
                    $barrelId = Rq('barrelId');
                    arcmaj3_barrel_expire($barrelId);
                } else {
                    if (Rq('amtask') == 'expireOldBarrels') {
                        $db         = new FractureDB('futuqiur_arcmaj3');
                        # from http://stackoverflow.com/questions/17307587/mysql-datetime-evaluation-get-all-records-whose-value-is-before-midnight-of-the
                        $barrelData = $db->query('SELECT id FROM `am_barrels` WHERE status=0 AND dateAssigned < ( DATE(NOW()) - INTERVAL 1 DAY );');
                        # print_r($barrelData);
                        foreach ($barrelData as $key => $value) {
                            $barrelId = $value['id'];
                            arcmaj3_barrel_expire($barrelId);
                        }
                        $db->close();
                    } else {
                        if (Rq('amtask') == 'addUrl') {
                            $db               = new FractureDB('futuqiur_arcmaj3');
                            $newUrl           = Rq('amNewUrl');
                            $pps              = $db->getColumn('am_projects', 'urlPattern');
                            $testProjects     = False;
                            $potentialProject = '';
                            //$pp=fuzzyMatchGetRow('am_projects','projectId','urlPattern','',$limit='')['projectId'];
                            //print_r($pps);
                            foreach ($pps as $ppid) {
                                if (stripos($newUrl, $ppid['urlPattern']) !== false) {
                                    $testProjects     = True;
                                    $potentialProject = $ppid['urlPattern'];
                                }
                            }
                            #$potentialProject = get_domain_simple($value);
                            $projects  = $db->getRow('am_projects', 'urlPattern', $potentialProject);
                            $projectId = $projects['id'];
                            #$projectId=1;
                            if ($testProjects) {
                                $newUrlId = $db->addRowFuzzy('am_urls', 'location, project, locationHashUnique', "'" . $db->UrlEscS($newUrl) . "', '" . $projectId . "', '" . hash('sha512', $db->UrlEscS($newUrl)) . "'");
                            } else {
                                $newUrlId = $db->addRowFuzzy('am_urls', 'location, project, locationHashUnique', "'" . $db->UrlEscS($newUrl) . "', '0', '" . hash('sha512', $db->UrlEscS($newUrl)) . "'");
                            }
                            echo 'Inserted URL ' . $newUrlId . "<br>\n";
                            $db->close();
                        } else {
                            if (Rq('amtask') == 'addProject') {
                                $db               = new FractureDB('futuqiur_arcmaj3');
                                $newUrl           = Rq('amSeedUrl');
                                $newProject       = Rq('amFilterPattern');
                                $newProjectId     = $db->addRow('am_projects', 'urlPattern, patternHashUnique', "'" . $db->UrlEscS($newProject) . "', '" . hash('sha512', $db->UrlEscS($newProject)) . "'");
                                $pps              = $db->getColumn('am_projects', 'urlPattern');
                                $testProjects     = False;
                                $potentialProject = '';
                                //$pp=fuzzyMatchGetRow('am_projects','projectId','urlPattern','',$limit='')['projectId'];
                                //print_r($pps);
                                foreach ($pps as $ppid) {
                                    if (stripos($newUrl, $ppid['urlPattern']) !== false) {
                                        $testProjects     = True;
                                        $potentialProject = $ppid['urlPattern'];
                                    }
                                }
                                #$potentialProject = get_domain_simple($value);
                                $projects  = $db->getRow('am_projects', 'urlPattern', $potentialProject);
                                $projectId = $projects['id'];
                                #$projectId=1;
                                if ($testProjects) {
                                    $newUrlId = $db->addRowFuzzy('am_urls', 'location, project, locationHashUnique', "'" . $db->UrlEscS($newUrl) . "', '" . $projectId . "', '" . hash('sha512', $db->UrlEscS($newUrl)) . "'");
                                }
                                echo 'Inserted project ' . $newProjectId . ' (' . $newProject . ') with seed URL ' . $newUrlId . "<br>\n";
                                $db->close();
                            } else {
                                echo 'Unrecognized operation';
                            }
                        }
                    }
                    
                    
                }
            }
        }
    } else {
        echo 'Incorrect protocol auth ID';
    }
}
function DBSimpleSubmissionHandler()
{
    $authorizationKey = $_REQUEST['authorizationKey'];
    $dbName           = $_REQUEST['db'];
    $dataTargetTable  = $_REQUEST['dataTargetTable'];
    $dataTargetField  = $_REQUEST['dataTargetField'];
    $dataTargetRowId  = $_REQUEST['dataTargetRowId'];
    $dataValue        = $_REQUEST['dataValue'];
    $db               = new FractureDB($dbName);
    $db->setField($dataTargetTable, $dataTargetField, $dataValue, $dataTargetRowId);
    $db->close();
}

#INSERT WEB INTERFACE RESPONDERS BELOW THIS LINE
#Arcmaj3
function arcmaj3_wint()
{
    $main_console = new FluidActive('Arcmaj3 statistics web console');
    $db           = new FractureDB('futuqiur_arcmaj3');
    #$main_console->DBTextEntry($db, 'am_urls', 'location', 0);
    #$main_console->DBRowEntry($db, 'am_urls', 1);
    #$main_console->DBTableEntry($db, 'am_urls');
    #$main_console->DBRowEntry($db, 'am_urls', 1);
    #$main_console->getQueryCount($db);
    $allProjects  = $db->getTable('am_projects');
    $main_console->append('<small>');
    $main_console->append('<big><b>Overall</b></big><br>');
    $total     = $db->countTable('am_urls');
    $total     = $total[0];
    $total     = $total['COUNT(*)'];
    #print_r($total);
    $crawled   = $db->countRows('am_urls', ' WHERE barrel != \'0\'');
    $crawled   = $crawled[0];
    $crawled   = $crawled['COUNT(*)'];
    #print_r($crawled);
    $remaining = $total - $crawled;
    if ($total == 0) {
        $pcCr = 0;
        $pcRm = 0;
    } else {
        $pcCr = round(($crawled / $total) * 100, 2);
        $pcRm = round(($remaining / $total) * 100, 2);
    }
    $main_console->append('Total URLs: ' . $total . '<br>');
    $main_console->append('Remaining URLs: ' . $remaining . ' (' . $pcRm . '%)<br>');
    $main_console->append('Crawled URLs: ' . $crawled . ' <b>(' . $pcCr . '%)</b><br>');
    foreach ($allProjects as $index => $data) {
        $main_console->append('<br><br><big><b>Project: ' . $data['urlPattern'] . ' (ID ' . $data['id'] . ')</b></big><br>');
        $total     = $db->countTable('am_urls', 'project', $data['id']);
        $total     = $total[0];
        $total     = $total['COUNT(*)'];
        #print_r($total);
        $crawled   = $db->countTable('am_urls', 'project', $data['id'], ' AND barrel != \'0\'');
        $crawled   = $crawled[0];
        $crawled   = $crawled['COUNT(*)'];
        #print_r($crawled);
        $remaining = $total - $crawled;
        if ($total == 0) {
            $pcCr = 0;
            $pcRm = 0;
        } else {
            $pcCr = round(($crawled / $total) * 100, 2);
            $pcRm = round(($remaining / $total) * 100, 2);
        }
        $main_console->append('Total URLs: ' . $total . '<br>');
        $main_console->append('Remaining URLs: ' . $remaining . ' (' . $pcRm . '%)<br>');
        $main_console->append('Crawled URLs: ' . $crawled . ' <b>(' . $pcCr . '%)</b><br>');
    }
    $main_console->append('</small>');
    $main_console->close();
    $db->close();
}
function arcmaj3_adm()
{
    $main_console = new FluidActive('Arcmaj3 management web console');
    #handler=1&handlerNeeded=arcmaj3&amtask=expireBarrel&verd=2&barrelId=117
    $main_console->append('Expire old barrels (>1wk out): <form action="active.php"><input type="hidden" name="verd" value="2"><input type="hidden" name="amtask" value="expireOldBarrels"><input type="hidden" name="handlerNeeded" value="arcmaj3"><input type="hidden" name="handler" value="1"><input type="submit"></form><br><br>
Expire a barrel:<br><form action="active.php"><input type="hidden" name="verd" value="2"><input type="hidden" name="amtask" value="expireBarrel"><input type="hidden" name="handlerNeeded" value="arcmaj3"><input type="hidden" name="handler" value="1">Barrel ID:<input type="text" name="barrelId"><input type="submit"></form><br><br>
Add a URL:<br><form action="active.php"><input type="hidden" name="verd" value="2"><input type="hidden" name="amtask" value="addUrl"><input type="hidden" name="handlerNeeded" value="arcmaj3"><input type="hidden" name="handler" value="1">New URL:<input type="text" name="amNewUrl"><input type="submit"></form><br><br>
Add a new project:<br><form action="active.php"><input type="hidden" name="verd" value="2"><input type="hidden" name="amtask" value="addProject"><input type="hidden" name="handlerNeeded" value="arcmaj3"><input type="hidden" name="handler" value="1">Filter pattern:<input type="text" name="amFilterPattern">Seed URL:<input type="text" name="amSeedUrl"><input type="submit"></form>');
    $main_console->close();
}

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