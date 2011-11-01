<?php
/*
 * Created on 18/02/2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class Log extends Object {
 	var $arquivo = "/tmp/centro.log";
 	var $fp;
 	
 	function __construct() {
 		
 	}
 	
 	function abrir() {
 		$this->fp = fopen($this->arquivo,"a");
 	}
 	
 	function fechar() {
 		fclose($this->fp);
 	}
 	
 	function escrever($msg) {
 		fwrite($this->fp,$msg,strlen($msg));
 	}
 	
 	function logar($msg) {
 		$this->abrir();
 		$this->escrever($msg);
 		$this->fechar();
 	} 
 	
 }
?>
