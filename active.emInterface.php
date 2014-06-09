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
			$ret=$ret.$this->addLink('3', 'recordId=' . fv('recordId') . '&', 'Log in…');
			$ret=$ret.'&#32;';
			$ret=$ret.$this->addLink('4', '', 'New user…');
			$ret=$ret.'&#32;';
			$ret=$ret.$this->addLink('11', '', 'Operation index… ');
		}
		return $ret;
    }
    
    function ui_breadcrumb_fragment() {
    	//Breadcrumb navigation
    	$recordId = fv('recordId');
		$emActionDispName = $_REQUEST['emAction'];
		$breadSeparator = ' &#x02192; ';
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
// 		$ret=$ret.$recordNameTag;
// 		if(!isset($pageMenu)) {
// 			$pageMenu = null;
// 		}
// 		$ret=$ret.$pageMenu;
		$ret=$ret.'</td></tr></tbody></table><h1>';
		return $ret;
    }
    
    function ui($action) {
     	$this->$action();
//     	$this->ui_logo_fragment();
//     	$this->ui_breadcrumb_fragment();
//     	$this->oappend($this->title);
//     	$this->oappend('</h1>');
    	$this->oappend($this->body);
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
		if(is_null($this->title)) {
			$title = 'Ember';
		}
		else {
			$title = ' — Ember';
		}
		$page = new Document_F($this->page,'',$title,'@NULL@','../../');
		$page->display();
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