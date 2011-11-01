<?php
class Html extends Object {

	
	var $base;
	
	function __construct() {
		parent::__construct();
		$this->base = $_SERVER['SCRIPT_NAME'];
	}
	
	/**
	 * 
	 * retorna uma url pronta 
	 * @param $controlador
	 * @param $acao
	 * @param array $parametros
	 */
	
	function url($controlador="",$acao="",$parametros=array()) {
		$para="";
		if(count($parametros)>0) {
			$para = "&parametros=".urlencode(implode(":",$parametros));
		} 

		if($acao != "") {
			$acao = "&acao=".$acao;
		}
		
		if($controlador!="") {
			$controlador="&controlador=".$controlador;
		}
		
		$url=$this->base."?ID=".base64_encode(time()).$controlador.$acao.$para;
		return $url;
	}
	
}