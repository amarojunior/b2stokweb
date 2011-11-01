<?php
class Controller extends Object {
	
	/**
	 * Nome do controller
	 *
	 * @var string
	 * @access public
	 */
	var $name;
	
	/**
	 * Action chamada
	 *
	 * @var string
	 * @access public
	 */
	var $action;
	
	/**
	 * Armezena um ou mais Models para este controller
	 *
	 * @var mixed
	 * @access public
	 */
	var $model = array();
	
	/**
	 * Diz quais models serao usados
	 * Use FALSE para nenhum
	 * @var mixed
	 * @access public
	 */
	var $uses = array();
	
	/**
	 * Layout usado para abrir a página.
	 * Use default para o layout padrao
	 *
	 * @var string
	 * @access public
	 */
	var $layout = "default";
	
	/**
	 * Variaveis que serao passadas para a view
	 *
	 * @var array
	 * @access public
	 */
	var $vars_for_view = array();
	
	/**
	 * Contem a classe sessao
	 * 
	 * 
	 * @var Sessao
	 */
	var $sessao;
	
	/**
	 * 
	 * Contem os dados enviados pelos formularios via post
	 * @var Array
	 */
	var $data = null;
	
	
	function __construct() {
		//$this->name=get_class($this);
		//$this->set('controller_name',strtolower($this->name));
		parent::__construct();
		$this->sessao = new Sessao();
	}
	
	function beforeAction() {
		
	}
	
	function afterAction() {
		
	}
	
	/**
	 * adiciona um model ao controller
	 *
	 * @param Object $model
	 * @access public
	 */	
	function addModel($model) {
		$this->model[get_class($model)] = $model;
	}
	
	/**
	 * Seta as variaveis para a view
	 *
	 * @param string $nome
	 * @param mixed $variavel
	 * @access public
	 */
	function set($nome,$variavel) {
		$this->vars_for_view[$nome]=$variavel;
	}
	
	/**
	 * 
	 * Redireciona a pagina para o caminho especificado
	 * @param mixed $caminho
	 */
	function redirect($caminho) {
		
		if(is_array($caminho)) {
			$controlador="";
			$acao="";
			$parametros=array();
			
			if(isset($caminho['controlador'])) {
				$controlador=$caminho['controlador'];
			}
			
			if(isset($caminho['acao'])) {
				$acao=$caminho['acao'];
			}
			
			if(isset($caminho['parametros'])) {
				$parametros=$caminho['parametros'];
			}
			$html= new Html();
			$caminho = $html->url($controlador,$acao,$parametros);
		}
		//debug($caminho);
		
		header("Location:".$caminho);
		exit(0);
		
	}
}
?>