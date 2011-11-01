<?php
class Debug  {
	
	public static $instance;
	
	public $sql="";
	
	
	private function  __construct() {
		
	}

	public static function singleton() {
        if (!isset(self::$instance)) {
       	 // Você deve informar os dados para conexão com o banco de dados.
       		$c = __CLASS__;
        	self::$instance = new $c;
   		 }

   		 return self::$instance;
    }
    
    function addSql($sql) {
    	$this->sql .= $sql."<br/>\n";
    }
	
}