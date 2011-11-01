<?php
class Form extends Object {
	var $nome;
	
	function __construct($nome="") {
		parent::__construct();
		$this->nome = ucfirst($nome);
	}
	
	function create($acao,$method="post",$options=array()) {
		$h="<form name=\"".$this->nome."\" id=\"".$this->nome."\"  action=\"".$acao."\"  method=\"".$method."\" >\n";
		return $h;
	}
	
	function end() {
		return "</form>\n";
	}
	
	function inputText($nome,$value="", $options=array()) {
		$opt="";
		foreach($options as $option => $v) {
			$opt .= $option . "=\"" .$v ."\" "; 
		}
		$id=$this->nome.ucfirst($nome);
		$nome="data[".$this->nome."][".$nome."]";
		$h=sprintf("<input type='text' name=\"%s\" id=\"%s\"  value=\"%s\"  %s / >\n",$nome,$id,$value,$opt);
		return $h;
	}

	function inputRadio($nome,$value, $options=array()) {
		$opt="";
		foreach($options as $option => $v) {
			$opt .= $option . "=\"" .$v ."\" "; 
		}
		$id=$this->nome.ucfirst($nome);
		$nome="data[".$this->nome."][".$nome."]";
		$h=sprintf("<input type='radio' name=\"%s\" id=\"%s\"  value=\"%s\"  %s / >\n",$nome,$id,$value,$opt);
		return $h;
	}
	
	function submit($value) {
		$nome="data[".$this->nome."][submit]";
		$h="<input type=\"submit\" name=\"".$nome."\" value=\"".$value."\" />\n";
		return $h;
	}
	
	function inputHidden($nome,$value,$options=array()) {
		$opt="";
		foreach($options as $option => $v) {
			$opt .= $option . "=\"" .$v ."\" "; 
		}
		$id=$this->nome.ucfirst($nome);
		$nome="data[".$this->nome."][".$nome."]";
		$h=sprintf("<input type='hidden' name=\"%s\" id=\"%s\"  value=\"%s\"  %s / >\n",$nome,$id,$value,$opt);
		return $h;
	}
	/**
	 * 
	 * Boolean chekcbox
	 * @param string $nome
	 * @param boolean $value
	 * @param array $options
	 */
	function inputCheck($nome,$value,$options=array()) {
		$opt="";
		foreach($options as $option => $v) {
			$opt .= $option . "=\"" .$v ."\" "; 
		}
		$id=$this->nome.ucfirst($nome);
		$nome="data[".$this->nome."][".$nome."]";
		$checked="";
		if($value) {
			$checked="checked=chequed";
		} 
		
		$h=sprintf("<input type='checkbox' name=\"%s\" id=\"%s\"  value=\"%s\"  %s  %s/ >\n",$nome,$id,"1",$opt,$checked);
		return $h;
	}
	
	
	
}