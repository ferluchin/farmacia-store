<?php

class Database {
	public static $db;
	public static $con;
	function Database(){
		$this->user="root";$this->pass="toor";$this->host="localhost";$this->ddbb="tiendaonline";
//		$this->user="minedeck_mduser";$this->pass="l00lapal00za";$this->host="localhost";$this->ddbb="minedeck_md";
	}

	function connect(){
		$con = new mysqli($this->host,$this->user,$this->pass,$this->ddbb);
		//$con->set_charset("utf8");
		$con->query("SET NAMES utf8");
		return $con;
	}

	public static function getCon(){
		if(self::$con==null && self::$db==null){
			self::$db = new Database();
			self::$con = self::$db->connect();
		}
		return self::$con;
	}
	
}
?>
