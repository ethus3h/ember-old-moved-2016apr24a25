<?php
class emInterface
{
    function __construct()
    {
    	$this->db = new FractureDB('futuqiur_ember');
    	$this->title = null;
    	$this->body = '';
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
    
    function lretrieve($id,$language,$fallbackLanguage = 0) {
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
    
    function appendToTitle($text) {
    	$this->title = $this->title.$text;
    }

    function home($parameters) {
    	$this->test('doom','doom');
    }

    function test($value,$expected,$description = '',$f=false) {
    	ob_start();
    	test($value,$expected,$description,$f);
    	$this->append(ob_get_clean());
    }
    
    function ui($action,$parameters = array()) {
    	$this->$action($parameters);
    }
    
    function display() {
		if(is_null($this->title)) {
			$this->title = 'Ember';
		}
		else {
			$this->appendToTitle(' — Ember');
		}
		$page = new Document_F($this->body,'',$this->title,'@NULL@','../../');
		$page->display();
    }
}
?>