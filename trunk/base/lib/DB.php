<?php
abstract class DB {
	
	
	public static $instance;
	
	/**
	 * Nome do SGBD (mysql,sqlite,postgre)
	 *
	 * @var string
	 */
	var $dbtype;
	
	/**
	 * Nome do usuario de conexao do banco de dados
	 *
	 * @var string
	 */
	var $dbuser;
	
	/**
	 * Senha de conxao com o banco de dados
	 *
	 * @var string
	 */
	var $dbpass;
	
	/**
	 * Endereco do servidor de banco dedados
	 *
	 * @var string
	 */
	var $dbhost;
	
	/**
	 * Nome do banco de dados
	 *
	 * @var string
	 */
	var $dbname;
	
	/**
	 * handler de conexao com o sgbd
	 *
	 * @var mixed
	 */
	var $dbcon;

	
	protected function __construct() {
		
	}
	
	public static function singleton($dbtype) {
        if (!isset(self::$instance)) {
       	 // Você deve informar os dados para conexão com o banco de dados.
       		$c = ucfirst($dbtype);
        	self::$instance = new $c;
   		 }

   		 return self::$instance;
    }
	
	/**
	 * Funcao que deve ser implementada para conectar ao Banco de dados
	 *
	 * @return boolean
	 */
	abstract function  conectar();
	
	/**
	 * Executa esta query no DB
	 *
	 * @param string $sql
	 */
	abstract  function query($sql);
	
	/**
	 * retorna um array com todos os resultados
	 *
	 * @param mixed $rs
	 */
	abstract  function getResults($rs,$chavePrimaria);
	
	/**
	 * Executa uma query de insercao ou atualizacao do banco
	 *
	 * @param unknown_type $sql
	 */
	abstract function queryiou($sql);
	
	/**
	 * Retorna um array com o nome das colunas
	 *
	 * @param string $table 
	 * @return array
	 */
	abstract function getColumns($table);
}
?>