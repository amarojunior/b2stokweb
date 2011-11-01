<?php
class Sessao extends Object {
	
	/**
	 * 
	 * Verifica se existe uam parametro de sessao;
	 * @param unknown_type $param
	 */
	function check($param) {
		return isset($_SESSION[$param]);
	}
	
	/**
	 * 
	 * Salvar informacoes na sessao
	 * @param $nome
	 * @param $dados
	 */
	function save($nome,$dados) {
		$salvar=array();
		if(is_array($dados)) {
			$salvar['tipo']="array";
			$salvar['dados']=serialize($dados);
		} elseif(is_object($dados)) {
			$salvar['tipo']="objeto";
			$salvar['classe']=get_class($dados);
			$salvar['dados']=serialize($dados);
		} else {
			$salvar['tipo']='string';
			$salvar['dados']=$dados;
		}
		$_SESSION[$nome]=serialize($salvar);
	}
	
	/**
	 * 
	 * recupera os dados armazenados na sessao
	 * @param $nome
	 */
	function read($nome) {
		if(!isset($_SESSION[$nome]))
			return false;

		$info=unserialize($_SESSION[$nome]);

		if($info['tipo']=="array") {
			return unserialize($info['dados']);
		}
		
		if($info['string']) {
			return $info['dados'];
		}
		
		if($info['dados']) {
			if(!class_exists($info['class'])) {
				debug("Classe ".$info['class']."nao esta definida");
				return false;
			}
			return unserialize($info['dados']);
		}
		
	}
	
	/**
	 * 
	 * Armazena uma mensagem para ser vista na view
	 *
	 * @param $str
	 */
	function setFlash($str) {
		$_SESSION['flash'] = $str;
	}
	
	/**
	 * 
	 * return uma string contendo a mensagem armazenada
	 */
	
	function readFlash() {
		if(isset($_SESSION['flash'])) {
			$str= $_SESSION['flash'];
			unset($_SESSION['flash']);
			return $str;
		}
		return "";
	}
	
	/**
	 * 
	 * Remove dados da sessao
	 * @param $param
	 */
	function delete($param) {
		if(isset($_SESSION[$param])) {
			unset($_SESSION[$param]);
		}
	}
	
	
}