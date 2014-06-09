<?php
class emInterface
{
    function __construct()
    {
    	$this->db = new FractureDB('futuqiur_ember');
    	$this->title = null;
    	$this->body = '';
    	$this->page = '';
    	$this->status = 0;
    	if (isset($_REQUEST['emSession'])) {
			session_id($_REQUEST['emSession']);
		} else {
		}
		global $emUserName;
		if(isset($_SESSION['emUserName'])) {
			$emUserName = $_SESSION['emUserName'];
		}
		global $emUserPassword;
		if(isset($_SESSION['emUserPassword'])) {
			$emUserPassword = $_SESSION['emUserPassword'];
		}
		if(isset($_REQUEST['emAction'])) {
			if ($_REQUEST['emAction'] == 'login') {
				$_SESSION['emUserName'] = $_POST['emUserName'];
				$_SESSION['emUserPassword'] = $_POST['emUserPassword'];
			} else {
			}
		}
		if(isset($_REQUEST['locale'])) {
			$this->locale = $_REQUEST['locale'];
		}
		else {
			$this->locale=0;
		}
		$parameters = array();
		$this->ui($_REQUEST['emAction'],$parameters);
    }

    function store($data,$csum) {
		$status = 0;
		$csumn = new Csum($data);
		if(!is_object($csum)) {
			return null;
		}
		if(!$csum->matches($csumn)) {
			return null;
		}
    	return store($data,$csum);
    }
    
    function retrieve($id) {
    	return retrieveCoal($id);
    }
    
    function api() {
    	ob_start();
    	$this->display();
    	$layoutres = ob_get_clean();
    }
    
    function lstore($data,$csum,$language,$fallbackLanguage = 0) {
		$status = 0;
		$csumn = new Csum($data);
		if(!is_object($csum)) {
			return null;
		}
		if(!$csum->matches($csumn)) {
			return null;
		}
    	return lstore($data,$csum,$language,$fallbackLanguage);
    }
    
    function lretrieve($id,$language = null,$fallbackLanguage = 0) {
    	if(is_null($language)) {
    		$language = $this->locale;
    	}
    	return lget($id,$language,$fallbackLanguage);
    }

    function adduser($name,$password,$record = null,$authorisation = 1) {
		$hash = phash($password);
		$username = amd5($name);
		if(!is_null($this->db->getRowUH('users', 'name', $username))) {
			return false;
		}
		return $this->db->addRow('users', 'name, password, record, authorisation', 'UNHEX(\''.$username.'\'), \''.$hash.'\', \''.$record.'\', \''.$authorisation.'\'');
    }
    
    function append($text) {
    	$this->body = $this->body.$text;
    }
    
    function oappend($text) {
    	$this->page = $this->page.$text;
    }
    
    function appendToTitle($text) {
    	$this->title = $this->title.$text;
    }

    function home() {
    	$this->append('<p>');
    	$this->append('Welcome to Ember.');
    	$this->append('</p>');
    }

    function test($value,$expected,$description = '',$f=false) {
    	ob_start();
    	test($value,$expected,$description,$f);
    	$this->append(ob_get_clean());
    }
    
    function getRecordProperty($id,$property) {
    	return 'dummy value';
    }
    
    
    function runTests() {
    	$this->append('<p><br>');
// 		$pres = array( 'id' => '3', 'csum' => 'Tzo0OiJDc3VtIjo0OntzOjM6ImxlbiI7aTo0O3M6MzoibWQ1IjtzOjMyOiJiNGY5NDU0MzNlYTRjMzY5YzEyNzQxZjYyYTIzY2NjMCI7czozOiJzaGEiO3M6NDA6ImZlMDQ2YTQwODY4OWQwNzA2NmQ1N2VmOTU4YWQ5MGQ4YzMyZjcwMTMiO3M6NDoiczUxMiI7czoxMjg6Ijk0ZGNmOTVhZWNhODBmYmUzZDZmMzQxYzAyY2UzNzg5ZmNkNmNhOGVmNTBkZTliNWM2MTM4YjhmYjg5NTVkNjJhYWEyMjVhODAyODk2MzkwOTU5ZWQxNjg4MTQwMzdhYTEwYTNhMzYxYjVhNTg0NDgxZTI0N2E5MGZiNjIwZTg5Ijt9', 'status' => 0);
// 		$this->test($this->store('doom',new Csum('doom')),$pres,'Store');
// 		$prer = array ( 'data' => 'doom', 'csum' => 'Tzo0OiJDc3VtIjo0OntzOjM6ImxlbiI7aTo0O3M6MzoibWQ1IjtzOjMyOiJiNGY5NDU0MzNlYTRjMzY5YzEyNzQxZjYyYTIzY2NjMCI7czozOiJzaGEiO3M6NDA6ImZlMDQ2YTQwODY4OWQwNzA2NmQ1N2VmOTU4YWQ5MGQ4YzMyZjcwMTMiO3M6NDoiczUxMiI7czoxMjg6Ijk0ZGNmOTVhZWNhODBmYmUzZDZmMzQxYzAyY2UzNzg5ZmNkNmNhOGVmNTBkZTliNWM2MTM4YjhmYjg5NTVkNjJhYWEyMjVhODAyODk2MzkwOTU5ZWQxNjg4MTQwMzdhYTEwYTNhMzYxYjVhNTg0NDgxZTI0N2E5MGZiNjIwZTg5Ijt9','filename'=>'coal_temp/5393f4ff63987.cstf','status' => 0 );
// 		$this->test($this->store('doom',null),null,'Store with null csum');
// 		$this->test($this->retrieve(3),$prer,'Retrieve');
// 		$prel = array ( 'id' => '2', 'csum' => 'Tzo0OiJDc3VtIjo0OntzOjM6ImxlbiI7aTo0O3M6MzoibWQ1IjtzOjMyOiJiNGY5NDU0MzNlYTRjMzY5YzEyNzQxZjYyYTIzY2NjMCI7czozOiJzaGEiO3M6NDA6ImZlMDQ2YTQwODY4OWQwNzA2NmQ1N2VmOTU4YWQ5MGQ4YzMyZjcwMTMiO3M6NDoiczUxMiI7czoxMjg6Ijk0ZGNmOTVhZWNhODBmYmUzZDZmMzQxYzAyY2UzNzg5ZmNkNmNhOGVmNTBkZTliNWM2MTM4YjhmYjg5NTVkNjJhYWEyMjVhODAyODk2MzkwOTU5ZWQxNjg4MTQwMzdhYTEwYTNhMzYxYjVhNTg0NDgxZTI0N2E5MGZiNjIwZTg5Ijt9','status' => 0 );
// 		$this->test($this->lstore('doom',new Csum('doom'),0),$prel,'Lstore');
// 		$this->test($this->lstore('doom',null,0),null,'Lstore with null csum');
// 		$this->test($this->lretrieve(2,0),$prer,'Lretrieve');
// 		$this->test($this->adduser('test','fracture'),false,'Add user');
 		$this->test($this->ui_logo_fragment(),'<table border="0" cellpadding="24" width="100%"><tbody><tr><td><br><table border="0" width="100%"><tbody><tr><td style="vertical-align:top"><a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=home&locale=0"><img src="d/w.png" alt="Ember" width="132" height="57" border="0"></a>&nbsp;&nbsp;(not logged in)&nbsp;&nbsp;<a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=3&recordId=&locale=0">Log in…</a>&#32;<a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=4&locale=0">New user…</a>&#32;<a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=11&locale=0">Operation index… </a>','UI logo fragment');
 		$this->test($this->ui_breadcrumb_fragment(),'<br><small><a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=1&locale=0">Ember</a> &#x02192; <a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=runTests&locale=0">runTests</a></td></tr></tbody></table><h1>','UI breadcrumb fragment');
    	$this->append('</p>');
    }
    
    function ui_logo_fragment() {
    	$login = fv('login');
		$ret='<table border="0" cellpadding="24" width="100%"><tbody><tr><td><br><table border="0" width="100%"><tbody><tr><td style="vertical-align:top">';
		if ($login == 1) {
			$ret=$ret.'<form target="ember.php" action="post"><input type="hidden" name="wint" value="1"><input type="hidden" name="wintNeeded" value="emberWebView"><input type="hidden" name="emAction" value="home"><input type="hidden" name="wvSession" value="';
			$ret=$ret.fv('emSession');
			/* This contains the logo link */
			$ret=$ret.'"><input type="hidden" name="login" value="1"><input type="image" src="d/w.png" width="132" height="57"></form>';
		} else {
			$ret=$ret.$this->addLink('home', '', '<img src="d/w.png" alt="Ember" width="132" height="57" border="0">');
		}
		if ($login == 1) {
			$ret=$ret.'&nbsp;&nbsp;(logged in)&nbsp;&nbsp;';
			$ret=$ret.$this->addLink('15', '', 'Log out…');
			$ret=$ret.'&#32;';
			$ret=$ret.$this->addLink('11', '', 'Operation index… ');
		} else {
			$ret=$ret.'&nbsp;&nbsp;(not logged in)&nbsp;&nbsp;';
			$ret=$ret.$this->addLink('logIn', 'recordId=' . fv('recordId') . '&', 'Log in…');
			$ret=$ret.'&#32;';
			$ret=$ret.$this->addLink('newUser', '', 'New user…');
			$ret=$ret.'&#32;';
			$ret=$ret.$this->addLink('operationIndex', '', 'Operation index… ');
		}
		return $ret;
    }
    
    function ui_breadcrumb_fragment() {
    	//Breadcrumb navigation
    	$recordId = fv('recordId');
		$emActionDispName = $_REQUEST['emAction'];
		$breadSeparator = '&#x02192;';
		if(!isset($disambigStr)) {
			$disambigStr = null;
		}
		$recordBCTitle = $recordId . '. ' . shorten($this->getRecordProperty($recordId,'title') . $this->getRecordProperty($recordId,'disambigstring'));
		if (!strlen(fv('recordId')) > 0) {
			$recordNameTag = "";
		} else {
			$recordNameTag = '?' . $this->addLink(6, '&recordId=' . fv('recordId') . '&', $recordBCTitle);
		}
		$actionlinkid = fv('emAction');
		$ret=str_replace('&a=6&locale', '&a=19&locale', '<br><small>');
 		$ret=$ret.$this->addLink(1,'','Ember');
 		$ret=$ret.' ' . $breadSeparator . ' ';
 		$ret=$ret.$this->addLink($actionlinkid, '', $emActionDispName);
		$ret=$ret.$recordNameTag;
		if(!isset($pageMenu)) {
			$pageMenu = null;
		}
		$ret=$ret.$pageMenu;
		$ret=$ret.'</td></tr></tbody></table><h1>';
		return $ret;
    }
    
    function ui($action) {
		if(is_null($this->title)) {
			$title = 'Ember';
		}
		else {
			$title = ' — Ember';
		}
     	$this->$action();
     	$this->oappend('<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title>' . $title . '</title><style type="text/css" media="all">@font-face{font-family:\'anoeyfuturamerlincommedium\';src:url(\'../../d/f/anoeyfuturamerlincom2.61.eot\');src:url(\'../../d/f/anoeyfuturamerlincom2.61.eot?#iefix\') format(\'embedded-opentype\'),url(\'../../d/f/anoeyfuturamerlincom2.61.woff\') format(\'woff\'),url(\'../../d/f/anoeyfuturamerlincom2.61.ttf\') format(\'truetype\'),url(\'../../d/f/anoeyfuturamerlincom2.61.svg#anoeyfuturamerlincommedium\') format(\'svg\');font-weight:normal;font-style:normal}@font-face{font-family:\'wreatherweb\';src:url(\'../../d/f/wreathe-r.eot\');src:url(\'../../d/f/wreathe-r.eot?#iefix\') format(\'../../embedded-opentype\'),url(\'../../d/f/wreathe-r.woff\') format(\'woff\'),url(\'../../d/f/wreathe-r.ttf\') format(\'truetype\'),url(\'../../d/f/wreathe-r.svg#wreatherweb\') format(\'svg\');font-weight:normal;font-style:normal}@font-face{font-family:\'wreatherweb\';src:url(\'../../d/f/wreathe-b.eot\');src:url(\'../../d/f/wreathe-b.eot?#iefix\') format(\'embedded-opentype\'),url(\'../../d/f/wreathe-b.woff\') format(\'woff\'),url(\'../../d/f/wreathe-b.ttf\') format(\'truetype\'),url(\'../../d/f/wreathe-b.svg#wreathebold\') format(\'svg\');font-weight:bold;font-style:normal}@font-face{font-family:\'wreatherweb\';src:url(\'../../d/f/wreathe-i.eot\');src:url(\'../../d/f/wreathe-i.eot?#iefix\') format(\'embedded-opentype\'),url(\'../../d/f/wreathe-i.woff\') format(\'woff\'),url(\'../../d/f/wreathe-i.ttf\') format(\'truetype\'),url(\'../../d/f/wreathe-i.svg#wreatheitalic\') format(\'svg\');font-weight:normal;font-style:italic}@font-face{font-family:\'wreatherweb\';src:url(\'../../d/f/wreathe-bi.eot\');src:url(\'../../d/f/wreathe-bi.eot?#iefix\') format(\'embedded-opentype\'),url(\'../../d/f/wreathe-bi.woff\') format(\'woff\'),url(\'../../d/f/wreathe-bi.ttf\') format(\'truetype\'),url(\'../../d/f/wreathe-bi.svg#wreathebold_italic\') format(\'svg\');font-weight:bold;font-style:italic}html{background:#d5e3cf;background:-webkit-gradient(radial,50% 0,0,50% 0,300,from(#FFF),to(#d5e3cf));background:-moz-radial-gradient(50% 0 90deg,circle farthest-side,#FFF,#d5e3cf,#d5e3cf 31.25em);background-repeat:no-repeat;background-color:#d5e3cf}body{text-align:justify;overflow-x:hidden;min-height:18.75em}form{display:inline}pre,blockquote,li,table,tbody,tr,td,ul,ol{color:#22520f;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;table-layout:fixed}a{color:#520f22;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:none;-webkit-transition:all .8s ease-out;-moz-transition:all .8s ease-out;-o-transition:all .8s ease-out;transition:all .8s ease-out}a:hover{color:#520f22;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:underline}p{color:#22520f;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;word-wrap:break-word;text-indent:30pt;text-align:justify;margin-top:0;margin-bottom:0}table{border-color:transparent}input{background-color:transparent}h1{color:#22520f;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;font-weight:normal;text-align:center}h2,h3,h4,h5,h6{color:#22520f;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;font-weight:normal;font-style:italic;text-align:center}.t{border:0;background-color:transparent;padding:0;overflow:visible;font-size:1em;color:#520f22;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif}.t:hover{border:0;background-color:transparent;padding:0;overflow:visible;font-size:1em;color:#520f22;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:underline;cursor:pointer}div.floattb{position:fixed;bottom:0;left:0;width:100%;z-index:3;text-align:center}div.floatbg{left:25px;right:25px;position:fixed;bottom:0;height:25px;z-index:2;opacity:.85;background-color:#f0f0f0}a.floatlink{font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:none}a.floatlink:hover{font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:underline}div.generated-toc{text-align:left;list-style-type:none;position:fixed;bottom:25px;left:35px;width:25%;z-index:2;background-color:#efefef;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:none}div#hideshow{position:fixed;bottom:0;left:-12px;width:10%;z-index:4;text-align:left}div#generated-toc a{font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:none;overflow-y:scroll}div#generated-toc ul{text-indent:-10pt;list-style-type:none;font-size:x-small}a#generated_toc_d_toggle:hover{text-decoration:none}p#toggle-container{text-align:left}div.greenpage{position:absolute;top:0;min-height:18.75em;background-color:transparent;margin:8px;margin-right:8px;z-index:-1}div.fh{left:25px;right:25px;position:absolute;top:25px;height:25px;z-index:100;text-align:center;font-size:large}div.litem{padding:10px;text-align:center}div.smalllink{padding-top:20px;text-align:center;font-size:x-small}div.relative{position:relative;padding-top:0}.reveal-modal-bg{position:fixed;height:100%;width:100%;background:#000;background:rgba(0,0,0,.8);z-index:100;display:none;top:0;left:0}.reveal-modal{visibility:hidden;top:75px;left:0;margin-left:-10px;width:90%;max-width:900px;background:#eee url(g.png) no-repeat -200px -80px;position:absolute;z-index:101;padding:0;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;-moz-box-shadow:0 0 10px rgba(0,0,0,.4);-webkit-box-shadow:0 0 10px rgba(0,0,0,.4);-box-shadow:0 0 10px rgba(0,0,0,.4)}.reveal-modal .close-reveal-modal{font-size:22px;line-height:.5;position:absolute;top:8px;right:11px;color:#aaa;font-weight:bold;cursor:pointer}div.logobox{margin:auto;display:inline-block;position:relative;height:20%;width:auto;padding-top:16px;padding-left:24px;float:left;padding-right:75px}div.holder{-webkit-box-shadow:0 0 10px 5px #FFF;box-shadow:0 0 10px 0 #FFF;margin:auto;position:relative;left:25px;width:322px;display:inline-block}div.caption{color:white;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:none}div.captionbg{position:absolute;bottom:0;left:0;width:100%;height:100%;background:#000;background:url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSI5MCUiIHN0b3AtY29sb3I9IiMwMDAwMDAiIHN0b3Atb3BhY2l0eT0iMSIvPgogICAgPHN0b3Agb2Zmc2V0PSI5MCUiIHN0b3AtY29sb3I9IiMwMDAwMDAiIHN0b3Atb3BhY2l0eT0iMSIvPgogICAgPHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSIjNjA2MDYwIiBzdG9wLW9wYWNpdHk9IjEiLz4KICAgIDxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzI2MjYyNiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgPC9saW5lYXJHcmFkaWVudD4KICA8cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIiBmaWxsPSJ1cmwoI2dyYWQtdWNnZy1nZW5lcmF0ZWQpIiAvPgo8L3N2Zz4=");background:-moz-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:-webkit-gradient(linear,left top,left bottom,color-stop(90%,rgba(0,0,0,1)),color-stop(90%,rgba(0,0,0,1)),color-stop(100%,rgba(40,40,40,1)),color-stop(100%,rgba(38,38,38,1)));background:-webkit-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:-o-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:-ms-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:linear-gradient(to bottom,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#000000\',endColorstr=\'#282828\',GradientType=0);opacity:.8;z-index:-1}img.logo{border-right:1px solid #666;border-left:1px solid #666;border-top:1px solid #666;z-index:105}div.caption:hover{color:white;font-family:\'wreatherweb\',\'Wreathe\',\'Centaur MT Std\',\'Centaur MT\',\'Centaur\',serif;text-decoration:underline}a.captionlink{color:white}div#paddingbottom{height:16px}div#content{position:static;padding-top:55px;padding-bottom:35px}div#page{position:absolute;left:50%;width:640px;margin-left:-320px}a:focus,a:active,button,input[type="reset"]::-moz-focus-inner,input[type="button"]::-moz-focus-inner,input[type="submit"]::-moz-focus-inner,select::-moz-focus-inner,input[type="file"]>input[type="button"]::-moz-focus-inner{outline:none !important}.popuplink{color:white !important}a#tl{font-size:.93em !important}</style><script src="/d/jquery-2.1.0.min.js" type="text/javascript"></script><script src="/d/r/jquery.transit.min.js" type="text/javascript"></script><style>@font-face {font-family:"Lato";font-style:normal;font-weight:100;src: local("Lato Hairline"),local("Lato-Hairline"),url(/d/f/lh.woff) format("woff");}@-webkit-keyframes spin{0%{-webkit-transform:rotate(0)}100%{-webkit-transform:rotate(360deg)}}@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}.loadingSpinnerContainer{margin-left:auto;margin-right:auto;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;-ms-box-sizing:border-box;box-sizing:border-box;display:block;width:100%;height:100%;margin:auto;border-width:0.1rem;border-style:solid;border-color:#444444 transparent transparent;border-radius:50%;-webkit-animation:spin 2.2s linear infinite;animation:spin 2.2s linear infinite}html{background-color:#b7b0b0;height:100%;font-size:100%;}body{display:flex;align-items:center;justify-content:center;margin:0;height:100%;width:100%;flex-flow:column;text-align:center}#loadingbox{font-size:3rem;font-family:"Lato",sans-serif;color:#444444;display:flex;align-items:center;flex-flow:column}#bgloading{margin-bottom:3rem;}input::-webkit-calendar-picker-indicator {display: none;}</style>');
     	$this->oappend('<!-- based on http://stackoverflow.com/questions/17891603/jquery-fade-in-page-load --><script type="text/javascript">
     	$(document).ready(function()
     	{ $(\'#pagects\').fadeIn(500);
     	$(\'a\').click(function(event) 
     	{     event.preventDefault(); newLocation = this.href;    
     	$(\'#pagects\').fadeOut(1000, function () {     
     	   window.location = newLocation;     
     	     });      });});</script></head>');
     	$this->oappend('<body id="bodyContents"><div id="loadingbox"><div id="bgloading">Loading…</div><br><div class="loading"></div></div><div id="pageContents" style="width:100%;height:100%;top:0px;left:0px;position:fixed;"></div><!-- <div id="pageShield" style="left:0px;top:0px;width:100%;height:100%;z-index:2;position:fixed;"></div> -->');
     	$this->oappend('<div id="pagects" style="position:fixed;top:0px;left:0px;display:none;">');
     	//$this->oappend('Hello World!');
     	$this->oappend('<div class="greenpage"></div><div class="fh"><a href="/" id="tl"><i>futuramerlin</i></a></div><div id="content"><h1></h1><table border="0" cellpadding="24" width="100%"><tbody><tr><td><br><table border="0" width="100%"><tbody><tr><td style="vertical-align:top"><a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=home&locale=0"><img src="d/w.png" alt="Ember" width="132" height="57" border="0"></a>&nbsp;&nbsp;(not logged in)&nbsp;&nbsp;<a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=logIn&recordId=&locale=0">Log in…</a>&#32;<a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=newUser&locale=0">New user…</a>&#32;<a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=operationIndex&locale=0">Operation index… </a><br><small><a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=1&locale=0">Ember</a> &#x02192; <a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=home&locale=0">home</a></td></tr></tbody></table><h1></h1><p>Welcome to Ember.</p></div><div class="floatbg"></div><div class="floattb"><a href="/" class="floatlink">Home</a> | <a href="javascript:history.back();" class="floatlink">Previous page</a> | <a href="/r.php?c=news&amp;a=main" class="floatlink">News</a> | <a href="/r.php?c=events&amp;a=main" class="floatlink">Events</a> | <a href="/r.php?c=articles&amp;a=main" class="floatlink">Articles</a> | <div style="display:inline;height:25px;margin-bottom:-4px;"><div style="display:inline-block;z-index:200;" id="projecthoverdiv" onMouseOver="show();" onMouseOut="hide();"><img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="" onMouseOver="forcehide();" style="width:5px;height:30px;margin-left:-5px;margin-bottom:-5px;"><a class="floatlink" id="projectHoverLink" href="r.php?c=main&amp;a=projects">Projects</a><img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="" id="pngstretch" onMouseOver="forcehide();" style="width:5px;height:26px;margin-left:0px;margin-bottom:-5px;"></div></div><noscript><a href="r.php?c=main&amp;a=projects" class="floatlink">Projects</a></noscript></div><div id="projectsdisplaybg" style="position:fixed;bottom:25px;left:100px;width:250px;height:126px;background-color:#030007;opacity:.5;z-index:180;display:none;"><div onMouseOver="persistin();" id="projectsdisplaydiv" style="position:fixed;bottom:26px;left:101px;width:250px;height:126px;opacity:.8;color:white;z-index:200;border:1px dotted white;"><ul style="text-align:left;"><li style="color:white;"><a style="color:white;" class="popuplink" href="../../r.php?c=Wreathe&amp;a=main">Wreathe</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="../../r.php?c=Ember&amp;a=main">Ember</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="../../r.php?c=DCE&amp;a=main">DCE</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="../../r.php?c=main&amp;a=music">Music</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="../../r.php?c=main&amp;a=more-projects">More…</a></li></ul></div></div><div id="pt" style="position:fixed;bottom:22px;left:100px;width:250px;height:126px;z-index:180;" onMouseOver="persistin();"></div><div id="triggerout" style="position:fixed;bottom:25px;left:100px;width:282px;height:142px;z-index:102;" onMouseOut="forcehide();"></div><script>function getOffset(a){var b=0;var c=0;while(a&&!isNaN(a.offsetLeft)&&!isNaN(a.offsetTop)){b+=a.offsetLeft-a.scrollLeft;c+=a.offsetTop-a.scrollTop;a=a.offsetParent}return{top:c,left:b}}var persistvar=\'0\';var div=document.getElementById(\'projectsdisplaybg\');var bg=document.getElementById(\'projectsdisplaydiv\');var phd=document.getElementById(\'projecthoverdiv\');var pt=document.getElementById(\'pt\');var triggerout=document.getElementById(\'triggerout\');var leftedge=getOffset(phd).left;div.style.left=leftedge+\'px\';bg.style.left=leftedge+\'px\';pt.style.left=leftedge+\'px\';triggerout.style.left=(leftedge-16)+\'px\';window.onresize=function(){leftedge=getOffset(phd).left;div.style.left=leftedge+\'px\';div.style.backgroundColor=\'#030007\';bg.style.left=leftedge+\'px\';pt.style.left=leftedge+\'px\';triggerout.style.left=(leftedge-16)+\'px\'};function persistin(){div.style.backgroundColor=\'black\';persistvar=\'1\';div.style.opacity=\'.8\';bg.style.opacity=\'1\';div.style.border=\'1px solid white\';pt.style.zIndex=\'0\';triggerout.style.display=\'block\'}function persistout(){persistvar=\'0\';div.style.opacity=\'.5\';bg.style.opacity=\'.8\';div.style.border=\'1px dotted white\';div.style.backgroundColor=\'#030007\';pt.style.zIndex=\'180\';triggerout.style.display=\'none\'}function show(){div.style.opacity=\'.5\';bg.style.opacity=\'.8\';div.style.backgroundColor=\'#030007\';div.style.border=\'1px dotted white\';div.style.display=\'block\';pt.style.display=\'block\';triggerout.style.display=\'block\'}function hide(){if(persistvar==\'1\'){void(0)}else{div.style.display=\'none\';pt.style.display=\'none\';triggerout.style.display=\'none\'}}function forcehide(){div.style.backgroundColor=\'#030007\';div.style.display=\'none\';pt.style.display=\'none\';triggerout.style.display=\'none\';persistvar=\'0\';div.style.opacity=\'.5\';bg.style.opacity=\'.8\';div.style.border=\'1px dotted white\';pt.style.zIndex=\'180\'}</script><div style="position:fixed;bottom:35px;right:10px;z-index:115 !important;"><a href="https://twitter.com/Futuramerlin" class="twitter-follow-button" data-show-count="false" data-dnt="true">Follow @Futuramerlin</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script></div></body>');
//      	$this->oappend($this->ui_logo_fragment());
//      	$this->oappend($this->ui_breadcrumb_fragment());
//      	$this->oappend($this->title);
//      	$this->oappend('</h1>');
//     	$this->oappend($this->body);
    	$this->oappend('</div>');
    	global $fractureVersion;
        global $activeVersion;
        $this->dynamicJsProperties = "var activeVersion = '" . $activeVersion . "';\n\n";
    	$this->oappend('<script type="text/javascript">fractureVersion = "' . $fractureVersion . '";' . "\nvar self = this;\n\n" . $this->dynamicJsProperties . "\n\n" . file_get_contents("fluid.js"));
    	$this->oappend('MainLoadingScreen = new LoadingScreen(0);
			MainLoadingScreen.show();');
    	$this->oappend('</script></body></html>');
    }
    
    function fail($message = null) {
    	if(is_null($message)) {
    		$this->oappend('Ember failed. Please try again later.');
    		$this->display();
    	}
    	else {
    		$this->oappend($message);
    		$this->display();
    	}
    }
    
    function display() {
    	echo $this->page;
		//example; <!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><script src="/d/jquery-2.1.0.min.js" type="text/javascript"></script><script type="text/javascript">$(document).ready(function() { $('body').delay(500).fadeIn(1000);$('a').click(function(event) {     event.preventDefault(); newLocation = this.href;     $('body').fadeOut(1000, function () {         window.location = newLocation;        });      });});</script><style type="text/css" media="all">@font-face{font-family:'anoeyfuturamerlincommedium';src:url('../../d/f/anoeyfuturamerlincom2.61.eot');src:url('../../d/f/anoeyfuturamerlincom2.61.eot?#iefix') format('embedded-opentype'),url('../../d/f/anoeyfuturamerlincom2.61.woff') format('woff'),url('../../d/f/anoeyfuturamerlincom2.61.ttf') format('truetype'),url('../../d/f/anoeyfuturamerlincom2.61.svg#anoeyfuturamerlincommedium') format('svg');font-weight:normal;font-style:normal}@font-face{font-family:'wreatherweb';src:url('../../d/f/wreathe-r.eot');src:url('../../d/f/wreathe-r.eot?#iefix') format('../../embedded-opentype'),url('../../d/f/wreathe-r.woff') format('woff'),url('../../d/f/wreathe-r.ttf') format('truetype'),url('../../d/f/wreathe-r.svg#wreatherweb') format('svg');font-weight:normal;font-style:normal}@font-face{font-family:'wreatherweb';src:url('../../d/f/wreathe-b.eot');src:url('../../d/f/wreathe-b.eot?#iefix') format('embedded-opentype'),url('../../d/f/wreathe-b.woff') format('woff'),url('../../d/f/wreathe-b.ttf') format('truetype'),url('../../d/f/wreathe-b.svg#wreathebold') format('svg');font-weight:bold;font-style:normal}@font-face{font-family:'wreatherweb';src:url('../../d/f/wreathe-i.eot');src:url('../../d/f/wreathe-i.eot?#iefix') format('embedded-opentype'),url('../../d/f/wreathe-i.woff') format('woff'),url('../../d/f/wreathe-i.ttf') format('truetype'),url('../../d/f/wreathe-i.svg#wreatheitalic') format('svg');font-weight:normal;font-style:italic}@font-face{font-family:'wreatherweb';src:url('../../d/f/wreathe-bi.eot');src:url('../../d/f/wreathe-bi.eot?#iefix') format('embedded-opentype'),url('../../d/f/wreathe-bi.woff') format('woff'),url('../../d/f/wreathe-bi.ttf') format('truetype'),url('../../d/f/wreathe-bi.svg#wreathebold_italic') format('svg');font-weight:bold;font-style:italic}html{background:#d5e3cf;background:-webkit-gradient(radial,50% 0,0,50% 0,300,from(#FFF),to(#d5e3cf));background:-moz-radial-gradient(50% 0 90deg,circle farthest-side,#FFF,#d5e3cf,#d5e3cf 31.25em);background-repeat:no-repeat;background-color:#d5e3cf}body{text-align:justify;overflow-x:hidden;min-height:18.75em}form{display:inline}pre,blockquote,li,table,tbody,tr,td,ul,ol{color:#22520f;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;table-layout:fixed}a{color:#520f22;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:none;-webkit-transition:all .8s ease-out;-moz-transition:all .8s ease-out;-o-transition:all .8s ease-out;transition:all .8s ease-out}a:hover{color:#520f22;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:underline}p{color:#22520f;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;word-wrap:break-word;text-indent:30pt;text-align:justify;margin-top:0;margin-bottom:0}table{border-color:transparent}input{background-color:transparent}h1{color:#22520f;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;font-weight:normal;text-align:center}h2,h3,h4,h5,h6{color:#22520f;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;font-weight:normal;font-style:italic;text-align:center}.t{border:0;background-color:transparent;padding:0;overflow:visible;font-size:1em;color:#520f22;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif}.t:hover{border:0;background-color:transparent;padding:0;overflow:visible;font-size:1em;color:#520f22;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:underline;cursor:pointer}div.floattb{position:fixed;bottom:0;left:0;width:100%;z-index:3;text-align:center}div.floatbg{left:25px;right:25px;position:fixed;bottom:0;height:25px;z-index:2;opacity:.85;background-color:#f0f0f0}a.floatlink{font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:none}a.floatlink:hover{font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:underline}div.generated-toc{text-align:left;list-style-type:none;position:fixed;bottom:25px;left:35px;width:25%;z-index:2;background-color:#efefef;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:none}div#hideshow{position:fixed;bottom:0;left:-12px;width:10%;z-index:4;text-align:left}div#generated-toc a{font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:none;overflow-y:scroll}div#generated-toc ul{text-indent:-10pt;list-style-type:none;font-size:x-small}a#generated_toc_d_toggle:hover{text-decoration:none}p#toggle-container{text-align:left}div.greenpage{position:absolute;top:0;min-height:18.75em;background-color:transparent;margin:8px;margin-right:8px;z-index:-1}div.fh{left:25px;right:25px;position:absolute;top:25px;height:25px;z-index:100;text-align:center;font-size:large}div.litem{padding:10px;text-align:center}div.smalllink{padding-top:20px;text-align:center;font-size:x-small}div.relative{position:relative;padding-top:0}.reveal-modal-bg{position:fixed;height:100%;width:100%;background:#000;background:rgba(0,0,0,.8);z-index:100;display:none;top:0;left:0}.reveal-modal{visibility:hidden;top:75px;left:0;margin-left:-10px;width:90%;max-width:900px;background:#eee url(g.png) no-repeat -200px -80px;position:absolute;z-index:101;padding:0;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;-moz-box-shadow:0 0 10px rgba(0,0,0,.4);-webkit-box-shadow:0 0 10px rgba(0,0,0,.4);-box-shadow:0 0 10px rgba(0,0,0,.4)}.reveal-modal .close-reveal-modal{font-size:22px;line-height:.5;position:absolute;top:8px;right:11px;color:#aaa;font-weight:bold;cursor:pointer}div.logobox{margin:auto;display:inline-block;position:relative;height:20%;width:auto;padding-top:16px;padding-left:24px;float:left;padding-right:75px}div.holder{-webkit-box-shadow:0 0 10px 5px #FFF;box-shadow:0 0 10px 0 #FFF;margin:auto;position:relative;left:25px;width:322px;display:inline-block}div.caption{color:white;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:none}div.captionbg{position:absolute;bottom:0;left:0;width:100%;height:100%;background:#000;background:url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSI5MCUiIHN0b3AtY29sb3I9IiMwMDAwMDAiIHN0b3Atb3BhY2l0eT0iMSIvPgogICAgPHN0b3Agb2Zmc2V0PSI5MCUiIHN0b3AtY29sb3I9IiMwMDAwMDAiIHN0b3Atb3BhY2l0eT0iMSIvPgogICAgPHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSIjNjA2MDYwIiBzdG9wLW9wYWNpdHk9IjEiLz4KICAgIDxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzI2MjYyNiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgPC9saW5lYXJHcmFkaWVudD4KICA8cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIiBmaWxsPSJ1cmwoI2dyYWQtdWNnZy1nZW5lcmF0ZWQpIiAvPgo8L3N2Zz4=");background:-moz-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:-webkit-gradient(linear,left top,left bottom,color-stop(90%,rgba(0,0,0,1)),color-stop(90%,rgba(0,0,0,1)),color-stop(100%,rgba(40,40,40,1)),color-stop(100%,rgba(38,38,38,1)));background:-webkit-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:-o-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:-ms-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:linear-gradient(to bottom,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#000000',endColorstr='#282828',GradientType=0);opacity:.8;z-index:-1}img.logo{border-right:1px solid #666;border-left:1px solid #666;border-top:1px solid #666;z-index:105}div.caption:hover{color:white;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:underline}a.captionlink{color:white}div#paddingbottom{height:16px}div#content{position:static;padding-top:55px;padding-bottom:35px}div#page{position:absolute;left:50%;width:640px;margin-left:-320px}a:focus,a:active,button,input[type="reset"]::-moz-focus-inner,input[type="button"]::-moz-focus-inner,input[type="submit"]::-moz-focus-inner,select::-moz-focus-inner,input[type="file"]>input[type="button"]::-moz-focus-inner{outline:none !important}.popuplink{color:white !important}a#tl{font-size:.93em !important}</style><title>Ember</title></head><body><div class="greenpage"></div><div class="fh"><a href="/" id="tl"><i>futuramerlin</i></a></div><div id="content"><h1></h1><table border="0" cellpadding="24" width="100%"><tbody><tr><td><br><table border="0" width="100%"><tbody><tr><td style="vertical-align:top"><a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=home&locale=0"><img src="d/w.png" alt="Ember" width="132" height="57" border="0"></a>&nbsp;&nbsp;(not logged in)&nbsp;&nbsp;<a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=logIn&recordId=&locale=0">Log in…</a>&#32;<a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=newUser&locale=0">New user…</a>&#32;<a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=operationIndex&locale=0">Operation index… </a><br><small><a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=1&locale=0">Ember</a> &#x02192; <a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=home&locale=0">home</a></td></tr></tbody></table><h1></h1><p>Welcome to Ember.</p></div><div class="floatbg"></div><div class="floattb"><a href="/" class="floatlink">Home</a> | <a href="javascript:history.back();" class="floatlink">Previous page</a> | <a href="/r.php?c=news&amp;a=main" class="floatlink">News</a> | <a href="/r.php?c=events&amp;a=main" class="floatlink">Events</a> | <a href="/r.php?c=articles&amp;a=main" class="floatlink">Articles</a> | <div style="display:inline;height:25px;margin-bottom:-4px;"><div style="display:inline-block;z-index:200;" id="projecthoverdiv" onMouseOver="show();" onMouseOut="hide();"><img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="" onMouseOver="forcehide();" style="width:5px;height:30px;margin-left:-5px;margin-bottom:-5px;"><a class="floatlink" id="projectHoverLink" href="r.php?c=main&amp;a=projects">Projects</a><img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="" id="pngstretch" onMouseOver="forcehide();" style="width:5px;height:26px;margin-left:0px;margin-bottom:-5px;"></div></div><noscript><a href="r.php?c=main&amp;a=projects" class="floatlink">Projects</a></noscript></div><div id="projectsdisplaybg" style="position:fixed;bottom:25px;left:100px;width:250px;height:126px;background-color:#030007;opacity:.5;z-index:180;display:none;"><div onMouseOver="persistin();" id="projectsdisplaydiv" style="position:fixed;bottom:26px;left:101px;width:250px;height:126px;opacity:.8;color:white;z-index:200;border:1px dotted white;"><ul style="text-align:left;"><li style="color:white;"><a style="color:white;" class="popuplink" href="../../r.php?c=Wreathe&amp;a=main">Wreathe</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="../../r.php?c=Ember&amp;a=main">Ember</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="../../r.php?c=DCE&amp;a=main">DCE</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="../../r.php?c=main&amp;a=music">Music</a></li><li style="color:white;"><a style="color:white;" class="popuplink" href="../../r.php?c=main&amp;a=more-projects">More…</a></li></ul></div></div><div id="pt" style="position:fixed;bottom:22px;left:100px;width:250px;height:126px;z-index:180;" onMouseOver="persistin();"></div><div id="triggerout" style="position:fixed;bottom:25px;left:100px;width:282px;height:142px;z-index:102;" onMouseOut="forcehide();"></div><script>function getOffset(a){var b=0;var c=0;while(a&&!isNaN(a.offsetLeft)&&!isNaN(a.offsetTop)){b+=a.offsetLeft-a.scrollLeft;c+=a.offsetTop-a.scrollTop;a=a.offsetParent}return{top:c,left:b}}var persistvar='0';var div=document.getElementById('projectsdisplaybg');var bg=document.getElementById('projectsdisplaydiv');var phd=document.getElementById('projecthoverdiv');var pt=document.getElementById('pt');var triggerout=document.getElementById('triggerout');var leftedge=getOffset(phd).left;div.style.left=leftedge+'px';bg.style.left=leftedge+'px';pt.style.left=leftedge+'px';triggerout.style.left=(leftedge-16)+'px';window.onresize=function(){leftedge=getOffset(phd).left;div.style.left=leftedge+'px';div.style.backgroundColor='#030007';bg.style.left=leftedge+'px';pt.style.left=leftedge+'px';triggerout.style.left=(leftedge-16)+'px'};function persistin(){div.style.backgroundColor='black';persistvar='1';div.style.opacity='.8';bg.style.opacity='1';div.style.border='1px solid white';pt.style.zIndex='0';triggerout.style.display='block'}function persistout(){persistvar='0';div.style.opacity='.5';bg.style.opacity='.8';div.style.border='1px dotted white';div.style.backgroundColor='#030007';pt.style.zIndex='180';triggerout.style.display='none'}function show(){div.style.opacity='.5';bg.style.opacity='.8';div.style.backgroundColor='#030007';div.style.border='1px dotted white';div.style.display='block';pt.style.display='block';triggerout.style.display='block'}function hide(){if(persistvar=='1'){void(0)}else{div.style.display='none';pt.style.display='none';triggerout.style.display='none'}}function forcehide(){div.style.backgroundColor='#030007';div.style.display='none';pt.style.display='none';triggerout.style.display='none';persistvar='0';div.style.opacity='.5';bg.style.opacity='.8';div.style.border='1px dotted white';pt.style.zIndex='180'}</script><div style="position:fixed;bottom:35px;right:10px;z-index:115 !important;"><a href="https://twitter.com/Futuramerlin" class="twitter-follow-button" data-show-count="false" data-dnt="true">Follow @Futuramerlin</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></div></body></html> 
    }
    
    function addLink($action,$options,$caption) {
    	$lttemp = false;
		if(isset($_REQUEST["login"])) {
			if($_REQUEST["login"] == "1") {
				$lttemp = true;
			}
		}
		if ($lttemp == true) {
			//Check that the user is properly logged in
			if ($this->checkLogin()) {
				$loginverifiedln = 1;
				$loginln = 1;
			} else {
				$loginverifiedln = 0;
				$this->fail("Login not verified: Password does not match stored check — Probably the password provided was incorrect.");
				$loginbl = 0;
			}
			if ($loginverifiedln == 1) {
			} else {
				$this->fail("Login error: could not authenticate.");
				$loginln = 0;
			}
		} else {
			$loginln = 0;
		}
		$localeid = fv('locale');
		if ($loginln == 0) {
			if ($options == '') {
				$separator = '';
				$options = $options . '&locale=' . $localeid;
				$options = str_replace('&&', '&', $options);
			} else {
				$separator = '&';
				$options = $options . 'locale=' . $localeid;
				$options = str_replace('&&', '&', $options);
			}
			$linkGenerated = '<a href="ember.php?wintNeeded=emberWebView&wint=1&emAction=' . $action . $separator . $options . '">' . $caption . '</a>';
		} else {
			if ($options == '') {
				$separator = '';
				$options = $options . '&emSession=' . session_id() . '&locale=' . $localeid;
				$options = str_replace('&&', '&', $options . '&emSession=' . session_id());
			} else {
				$separator = itr(41);
				$options = $options . '&locale=' . $localeid;
				$options = str_replace('&&', '&', $options);
			}
			$linkGenerated = str_replace('&&', '&', '<form action="ember.php" method="post"><input type="hidden" name="wint" value="1"><input type="hidden" name="wintNeeded" value="emberWebView"><input type="hidden" name="emAction" value="' . $action . str_replace('&', '"><input type="hidden" name="', str_replace('=', '" value="', $options)) . '"><input type="hidden" name="login" value="1"><button type="submit" class="t">' . $caption . '</button></form>');
		}
		return($linkGenerated);
    }

}
?>