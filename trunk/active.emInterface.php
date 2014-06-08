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
    	$this->append('<p>');
    	$this->append('Welcome to Ember.');
    	$this->append('</p>');
    }

    function test($value,$expected,$description = '',$f=false) {
    	ob_start();
    	test($value,$expected,$description,$f);
    	$this->append(ob_get_clean());
    }
    
    function runTests($parameters) {
    	$this->append('<p><br>');
		$pres = array( 'id' => '3', 'csum' => 'Tzo0OiJDc3VtIjo0OntzOjM6ImxlbiI7aTo0O3M6MzoibWQ1IjtzOjMyOiJiNGY5NDU0MzNlYTRjMzY5YzEyNzQxZjYyYTIzY2NjMCI7czozOiJzaGEiO3M6NDA6ImZlMDQ2YTQwODY4OWQwNzA2NmQ1N2VmOTU4YWQ5MGQ4YzMyZjcwMTMiO3M6NDoiczUxMiI7czoxMjg6Ijk0ZGNmOTVhZWNhODBmYmUzZDZmMzQxYzAyY2UzNzg5ZmNkNmNhOGVmNTBkZTliNWM2MTM4YjhmYjg5NTVkNjJhYWEyMjVhODAyODk2MzkwOTU5ZWQxNjg4MTQwMzdhYTEwYTNhMzYxYjVhNTg0NDgxZTI0N2E5MGZiNjIwZTg5Ijt9', 'status' => 0);
		$this->test($this->store('doom',new Csum('doom')),$pres,'Store');
		$prer = array ( 'data' => 'doom', 'csum' => 'Tzo0OiJDc3VtIjo0OntzOjM6ImxlbiI7aTo0O3M6MzoibWQ1IjtzOjMyOiJiNGY5NDU0MzNlYTRjMzY5YzEyNzQxZjYyYTIzY2NjMCI7czozOiJzaGEiO3M6NDA6ImZlMDQ2YTQwODY4OWQwNzA2NmQ1N2VmOTU4YWQ5MGQ4YzMyZjcwMTMiO3M6NDoiczUxMiI7czoxMjg6Ijk0ZGNmOTVhZWNhODBmYmUzZDZmMzQxYzAyY2UzNzg5ZmNkNmNhOGVmNTBkZTliNWM2MTM4YjhmYjg5NTVkNjJhYWEyMjVhODAyODk2MzkwOTU5ZWQxNjg4MTQwMzdhYTEwYTNhMzYxYjVhNTg0NDgxZTI0N2E5MGZiNjIwZTg5Ijt9','filename'=>'coal_temp/5393f4ff63987.cstf','status' => 0 );
		$this->test($this->store('doom',null),null,'Store with null csum');
		$this->test($this->retrieve(3),$prer,'Retrieve');
		$prel = array ( 'id' => '2', 'csum' => 'Tzo0OiJDc3VtIjo0OntzOjM6ImxlbiI7aTo0O3M6MzoibWQ1IjtzOjMyOiJiNGY5NDU0MzNlYTRjMzY5YzEyNzQxZjYyYTIzY2NjMCI7czozOiJzaGEiO3M6NDA6ImZlMDQ2YTQwODY4OWQwNzA2NmQ1N2VmOTU4YWQ5MGQ4YzMyZjcwMTMiO3M6NDoiczUxMiI7czoxMjg6Ijk0ZGNmOTVhZWNhODBmYmUzZDZmMzQxYzAyY2UzNzg5ZmNkNmNhOGVmNTBkZTliNWM2MTM4YjhmYjg5NTVkNjJhYWEyMjVhODAyODk2MzkwOTU5ZWQxNjg4MTQwMzdhYTEwYTNhMzYxYjVhNTg0NDgxZTI0N2E5MGZiNjIwZTg5Ijt9','status' => 0 );
		$this->test($this->lstore('doom',new Csum('doom'),0),$prel,'Lstore');
		$this->test($this->lstore('doom',null,0),null,'Lstore with null csum');
		$this->test($this->lretrieve(2,0),$prer,'Lretrieve');
		$this->test($this->adduser('test','fracture'),false,'Add user');
    	$this->append('</p>');
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