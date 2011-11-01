<?php
class Mysql extends DB {
	
	/**
	 * 
	 * Tabela do modelo que esta chamando
	 * @var string
	 */
	var $table=null;
	
	function __construct() {
		
	}
	
	
	/**
	 * Conecta no bando de dados
	 *
	 * @return boolean
	 */
	function conectar() {
		$this->dbcon = mysql_connect($this->dbhost,$this->dbuser,$this->dbpass);
		if($this->dbcon === false) {
			return false;
		}
		if(mysql_select_db($this->dbname,$this->dbcon) === false) {
			return false;
		}
	}
	
	/**
	 * Executa uma query no banco de dados
	 *
	 * @param string $sql
	 */
	function query($sql) {
		if(strpos($sql,";") !== false) return false;
		$rs=mysql_query($sql,$this->dbcon);

		if($erro=mysql_error()) {
			debugSQL("mysql_error: ".$sql."<br>\n".$erro);
			return false;
		} else {
			debugSQL($sql);
		}
		
		return $rs;
	}
	
	/**
	 * retorna um array com todos os resultados
	 *
	 * @param result_set $rs
	 */
	function getResults($rs,$chavePrimaria) {
		$resposta=array();
		$ids="";
		while($a=mysql_fetch_array($rs,MYSQL_ASSOC)) {
			$r=array();
			foreach($a as $k => $value) {
				$t=explode("__",$k);
				if($t[1]==$chavePrimaria) $ids .= $value.",";
				$r[$t[0]][$t[1]]= $value;
			}
			$resposta[]=$r;
		}
		return array(substr($ids,0,-1) ,$resposta);
	}
	/**
	 * retorna um array com todos os resultados para relacionamento
	 *
	 * @param result_set $rs
	 */
	function getResultsRel($rs,$chavePrimaria,$rel) {
		$resposta=array();
		$ids="";
		while($a=mysql_fetch_array($rs,MYSQL_ASSOC)) {
			$r=array();
			$rel_value = $a[$rel];
			foreach($a as $k => $value) {
				$t=explode("__",$k);
				if($t[1]==$chavePrimaria) $ids .= $value.",";
				
				 $r[$t[0]][$t[1]]= $value;
			}
			$resposta[$rel_value][]=$r;
		}
		return array(substr($ids,0,-1) ,$resposta);
	}
	/**
	 * retorna um array com todos os resultados estilo atingo
	 *
	 * @param result_set $rs
	 */
	function getResultsNoModel($rs,$relacionado=array()) {
		$resposta=array();
	
		while($a=mysql_fetch_array($rs,MYSQL_ASSOC)) {
			$r=array();
			
			foreach($a as $k => $value) {
					
				if(is_array($value)) {
					$value=$value['Field'];
				}
				$r[$k]= $value;
			}
			$resposta[]=$r;
		}
		return $resposta;
	}
	
	/**
	 * Executa uma query de insercao ou atualizacao do banco
	 *
	 * @param string $sql
	 */
	function queryiou($sql) {
		if(strpos($sql,";") !== false) return false;
		$id="";
		$r=$this->query($sql);
		if($r===false) {
			return false;
		}
		
		if(stripos($sql,"insert")!==false) {
			
			$id=mysql_insert_id($this->dbcon);
		}
		
		return $id;
	}
	
	/**
	 * Retorna um array com o nome das colunas
	 *
	 * @param string $table 
	 * @return array
	 */
	function getColumns($table) {
		$rs=$this->query("DESCRIBE ".$table);
		if($rs===false) {
			return false;
		}
		$resposta=array();
	
		while($a=mysql_fetch_array($rs,MYSQL_ASSOC)) {
			$r=array();
			
			$resposta[]=$a['Field'];
		}
		return $resposta;
	}
	
	/**
	 * executa uma query no banco de dados
	 * 
	 * @param string $sql A query que se quer executar
	 * @return mixed
	 */
	function executeQuery($sql) {
		if(strpos($sql,";") !== false) return false;
		$erro = "";
 		$rs=@mysql_query($sql);
 		
 		if($rs===true) {
 			$this->last_id = @mysql_insert_id($this->dbcon);
 		} else {
 			$this->last_id = null;
 		}
 		if($rs===false) {
 				$erro= "\nErro Ao realizar consulta\n"
 					."SQL: ".$sql."\n"
 					."ERRO: ".mysql_error($this->dbcon)."\n\n";
 				debugSQL($erro);
 				return false;
 		}
 		
 		
 		return $rs;
 	}
 	
 	/**
 	 * retorna o resultado de uma consulta em formato de objeto
 	 * 
 	 * @param resultset $rs result set da consulta
 	 * @param $ob opcional. classe que será usada para criar os objetos
 	 * @return Object
 	 */
 	function fetchObject($rs,$ob=null) {
 		$r = null;
 		if($ob != null) {
	 		if(class_exists($ob)) {
	 			$r = @mysql_fetch_object($rs,$ob);
	 		} else { 
	 			$r = @mysql_fetch_object($rs);
	 	    }
	 		
 		} else { 
	 			$r = @mysql_fetch_object($rs);
	 	}
 		return $r;
 	}
 	
 	/**
 	 * Retorna o numero de linhas resultantes da consulta
 	 * 
 	 * @param resultset $rs resultset da consulta
 	 * @return int linhas da consulta
 	 */
 	function numRows($rs) {
 		
 		$r=@mysql_num_rows($rs);
 		return $r;	
 	}
 	
 	/**
 	 * Libera a memoria de uma consulta
 	 * @param $rs resultset da consulta
 	 */
 	function free($rs) {
 		@mysql_free_result($rs);
 	}
	
	
	
	
}
?>