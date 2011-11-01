<?php
class Model extends Object {
	
	/**
	 * Nome da tabela para este model
	 *
	 * @var string
	 */
	var $useTable = NULL;
	
	/**
	 * Instancia de conecao com o Banco de dados
	 *
	 * @var Object
	 */
	var $db;
	
	/**
	 * Ultimo ID gerado automaticamente pelo db
	 *
	 * @var mixed
	 */
	var $id;
	
	/**
	 * 
	 * Define a coluna de chave primaria
	 * @var string
	 */
	var $chavePrimaria="id";
	
	/**
	 * Array de Dados a serem salvos
	 *
	 * @var array
	 */
	var $data=array();
	
	/**
	 * 
	 * Relacao de 1 para muitos
	 * @var array
	 */
	var $temMuitos=array();
	
	/**
	 * 
	 * Relacao de 1 para 1
	 * @var array
	 */
	var $temUm=array();
	
	/**
	 * 
	 * Relacao inverca a temMuitos
	 * @var array
	 */
	var $pertenceA=array();
	
	/**
	 * 
	 * Indica se esta a busca esta sendo por um relacionamento
	 * @var string
	 */
	var $relacionado=null;
	
	/**
	 * 
	 * Array contendo as regras de validacao
	 * @var array
	 */
	var $validacao=array();
	
	/**
	 * 
	 * Armazena as mensagens de erro
	 * @var array
	 */
	var $erros=array();
	
	function __construct() {
		parent::__construct();
		if($this->useTable == null) {
			$this->useTable = strtolower(get_class($this))."s";
		}
		$this->db=DB::singleton("driver");
		
	}
	
	
	
	/**
	 * Executa uma query no banco de dados
	 *
	 * @param string $sql
	 * @return array
	 */
	function query($sql,$relacionado=array()) {
		if($this->useTable === false) return array();
		$rs=$this->db->query($sql);
		if($this->relacionado != null) {
			if(stripos($sql,"select")!==false)
				return $this->db->getResultsRel($rs,$this->chavePrimaria,get_class($this)."__". $this->relacionado);
		}
		
		if(stripos($sql,"select")!==false)
			return $this->db->getResults($rs,$this->chavePrimaria);
	}
	
	/**
	 * retorna todos os registros da tabela
	 *
	 * @param mixed $q
	 * @param mixed $conditions
	 * @return array
	 */
	
	function get($q='all',$conditions=null,$adicional='') {
		$condicoes= " WHERE 1 ";
		
		
		if($conditions != null) {
			foreach($conditions as $k => $v) {
				if(strpos($k,".")===false) {
					$k=$this->useTable.".".$k;
				}
				if(strpos($v,"(")!==false) {
					$condicoes .= " AND ".$k." ".$v." ";
				} else {
					$condicoes .= " AND ".$k." \"".$v."\" ";
				}
			}
		}
		
		
		/*
		 * cria as relacoes para consulta - FIXME
		 */
		$relacoes="";
		$colunas_rel="";
		
		foreach($this->temUm as $Model => $modelName) {
			$rel=array();
			if(is_array($modelName)) {
				$rel=$modelName;
				$modelName= $Model;
			} 
			//debug($rel);
			$chaveEstrangeira="";
			importModel($modelName);
			$m=new $modelName();
			
			if(!isset($rel['chaveEstrangeira'])) {
				$chaveEstrangeira = ($this->useTable{strlen($this->useTable)-1}=="s")?substr($this->useTable,0,-1):$this->useTable;
				$chaveEstrangeira .= "_id";
			} else {
				$chaveEstrangeira = $rel['chaveEstrangeira'];
			}
			$colunas=$this->db->getColumns($m->useTable);
			foreach($colunas as $col) {
				$colunas_rel .=", ".$m->useTable.".".$col." as ".get_class($m)."__".$col." ";
			}
			$relacoes = " LEFT JOIN ".$m->useTable. " on (".$this->useTable.".id = ".$m->useTable.".".$chaveEstrangeira.") ";
			unset($m);
		}
		
		
		
		if($this->useTable === false) return array();
		$colunas_model="";
		if($q=='all') {
		$colunas=$this->db->getColumns($this->useTable);
			foreach($colunas as $col) {
				$colunas_model .=" ".$this->useTable.".".$col." as ".get_class($this)."__".$col.",";
			}
			$colunas_model=substr($colunas_model, 0,-1);
			$colunas = $colunas_model.$colunas_rel;
			$sql="SELECT ".trim($colunas)." FROM ".$this->useTable.$relacoes.$condicoes.$adicional;
		
		}
		
		if(is_array($q)) {
			$campos="";
			$co=count($q);
			for($i=0;$i<$co;$i++) {
				$campos.=$q[$i];
				if($i+1!=$co) $campos .= ",";
			}
			$sql="SELECT ".$campos." FROM ".$this->useTable.$condicoes;
		}
		list($ids,$resultado)=$this->query($sql);
		
		foreach($this->temMuitos as $Model => $modelName) {
			$rel=array();
			if(is_array($modelName)) {
				$rel=$modelName;
				$modelName= $Model;
			} 
			importModel($modelName);
			$m=new $modelName;
			$chaveEstrangeira="";
			if(!isset($rel['chaveEstrangeira'])) {
				$chaveEstrangeira = ($this->useTable{strlen($this->useTable)-1}=="s")?substr($this->useTable,0,-1):$this->useTable;
				$chaveEstrangeira .= "_id";
			} else {
				$chaveEstrangeira = $rel['chaveEstrangeira'];
			}
			$m->relacionado = $chaveEstrangeira;
			$f=$m->get('all',array($m->useTable.".".$chaveEstrangeira." IN "=> "(".$ids.")"));
			
			$saida=array();
			
			foreach($resultado as $k => $rs) {
				$saida[$k] = $this->relaciona($rs, $f, $modelName);
			}
			$resultado=$saida;
		} 
		/*if(count($resultado) == 1) {
			debug($resultado);
			$resultado=$resultado[0];
		}*/
		return $resultado;
	}
	
	/**
	 * 
	 * faz um relacionamento entre as filhos
	 * @param array $rs
	 * @param array $related
	 * @param string $model
	 */
	function relaciona($rs,$related,$model) {
		
		$rs[$model]=$related[$rs[get_class($this)][$this->chavePrimaria]];
		return $rs;
	}
	
	/**
	 * retorna todos os registros da tabela de acordo com o id
	 *
	 * @param mixed $id
	 * @param mixed $conditions
	 * @return array
	 */
	
	function getOneById($id=null,$q="all",$conditions=array()) {
		return $this->get("all",array_merge(array('id='=>$id),$conditions));
	}
	
	/**
	 * Executa antes de salvar os dados
	 * se retornar boolean falso os dados não seram salvos
	 *
	 * @return boolean
	 */
	function beforeSave() {
		return true;
	}
	
	
	function save($data) {
		$this->data = $data;
		
		if(!$this->validar()) {
			return false;
		}
		
		if($this->beforeSave() !== true) {
			return false;
		}
		$salvar=$this->data[get_class($this)];
		
		$colunas=$this->db->getColumns($this->useTable);
		if(!isset($salvar['id'])) {
			$sql="INSERT INTO ".$this->useTable." ";
			$campos="";
			$valores="";
			$ds=$salvar;
			$co=count($ds);
			$i=0;
			foreach($ds as $c => $v) {
				if(!in_array($c,$colunas)) {
					$i++; 
					
					continue;
				}
				$campos .= $c;
				$valores .= " \"" .$v. "\"";
				if($i+1 != $co) {
					$campos .= ",";
					$valores .= ",";
				}
				$i++;
			}
			
			if($valores{strlen($valores)-1} == ",") {
				$valores = substr($valores,0,-1);
			}
			if($campos{strlen($campos)-1} == ",") {
				$campos = substr($campos,0,-1);
			}
			$sql .= "(".$campos.") values(".$valores.")";
			$this->id=$this->db->queryiou($sql);
			if($this->id === false) {
				return false; 
			} else {
				return true;
			}
			
		}//insert
		 else {
		 	$salvar=$this->data[get_class($this)];
		 	$sql="UPDATE ".$this->useTable ." SET ";
		 	$this->id=$salvar['id'];
		 	unset($salvar['id']);
			$valores="";
			$ds=$salvar;
			$co=count($ds);
			$i=0;
			foreach($ds as $c => $v) {
				if(!in_array($c,$colunas)) {
					$i++; 
					continue;
				}
				$valores .= $c." = \"" .$v. "\"";
				if($i+1 != $co) {
					$valores .= ",";
				}
				$i++;
			}
			
			if($valores{strlen($valores)-1} == ",") {
				$valores = substr($valores,0,-1);
			}
			
			$sql .=  $valores. " WHERE 1 AND id = ".$this->id;
			$r=$this->db->queryiou($sql);
		 	if($r === false) {
				return false; 
			} else {
				return true;
			}
		 }
		
		
	}
	
	function delete($id,$cascata=false) {
		return $this->deletePor(array( $this->chavePrimaria.' ='=>$id),$cascata);
	}
	
	function deletePor($conditions,$cascata=false) {
		$r=$this->get('all',$conditions);
		$g=0;
		if($cascata) {
			foreach($r as $item) {
				
					$id=$item[get_class($this)][$this->chavePrimaria];
					foreach($this->temUm as $Model => $modelName) {
						$rel=array();
						if(is_array($modelName)) {
							$rel=$modelName;
							$modelName= $Model;
						} 
						importModel($modelName);
						$m=new $modelName;
						$chaveEstrangeira="";
						if(!isset($rel['chaveEstrangeira'])) {
							$chaveEstrangeira = ($this->useTable{strlen($this->useTable)-1}=="s")?substr($this->useTable,0,-1):$this->useTable;
							$chaveEstrangeira .= "_id";
						} else {
							$chaveEstrangeira = $rel['chaveEstrangeira'];
						}
						$m->deletePor(array($chaveEstrangeira." ="=>$id),$cascata);
						
					}//temUm
					$j=0;
					foreach($this->temMuitos as $Model => $modelName) {
						$rel=array();
						if(is_array($modelName)) {
							$rel=$modelName;
							$modelName= $Model;
						} 
						importModel($modelName);
						$m=new $modelName;
						$chaveEstrangeira="";
						if(!isset($rel['chaveEstrangeira'])) {
							$chaveEstrangeira = ($this->useTable{strlen($this->useTable)-1}=="s")?substr($this->useTable,0,-1):$this->useTable;
							$chaveEstrangeira .= "_id";
						} else {
							$chaveEstrangeira = $rel['chaveEstrangeira'];
						}
						$m->deletePor(array($chaveEstrangeira." ="=>$id),$cascata);
						
					}//temMuitos
					
					
			}
		}
		
		$condicoes= " WHERE 1 ";
		if($conditions != null) {
				foreach($conditions as $k => $v) {
					if(strpos($k,".")===false) {
						$k=$this->useTable.".".$k;
					}
					if(strpos($v,"(")!==false) {
						$condicoes .= " AND ".$k." ".$v." ";
					} else {
						$condicoes .= " AND ".$k." \"".$v."\" ";
					}
				}
		}
		$sql="DELETE FROM ".$this->useTable.$condicoes;
		return $this->query($sql);
		
	}
	
	/**
	 * 
	 * Valida os campos
	 */
	function validar() {
		$ok=true;
		foreach($this->validacao as $k=>$v) {
			$conteudo=$this->data[get_class($this)][$k];
			if(!is_array($v)) {
				if(!$this->$v($conteudo)) {
					$this->setErro($k ." invalido!");
					$ok=false;
				}
			} else {
				$f=$v['metodo'];
				$erro=$v['mensagem'];
				if(!$this->$f($conteudo)) {
					$this->setErro($erro);
					$ok=false;
				}
				
			}
		}
		return $ok;
	}
	
	/**
	 * 
	 * adiciona mensagem de erro a variavel erros
	 * @param $e
	 */
	function setErro($e) {
		$this->erros[]=$e;
	}
	
	/**
	 * 
	 * retorna as mensagens de erro
	 */
	function getErro() {
		return implode("<br />\n",$this->erros);
	}

	
}
?>