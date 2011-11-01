<?php

define('BASE_URL', dirname($_SERVER["SCRIPT_NAME"]));
define('ROOT',dirname($_SERVER["SCRIPT_NAME"]));

/* 
 * declaracoes nao mexer
 */
$controlador=""; 
$acao="";
$parametros=array();
$url=array();

include "../config/config.php";
include "../config/dbconfig.php";


include "../lib/debug.php";
include "../lib/basico.php";
include "../lib/object.php";
include "../lib/sessao.php";

//include "../lib/DB.php";
include "../lib/html.php";
//include "../lib/form.php";
include "../lib/controller.php";
include "../lib/model.php";
include "../lib/log.php";


/**
 * Inicia a sessao
 */
session_name(Config::nomeSessao);
session_cache_expire(Config::tempoSessao);
session_start();


$logar = new Log();

/**
 * Mudar para Doctrine 2.1
 */
include "../lib/".$dbtype .".php";
//$dbclass=ucfirst($dbtype);
$db = DB::singleton($dbtype);
$db->dbuser=$dbuser;
$db->dbpass=$dbpass;
$db->dbhost=$dbhost;
$db->dbname=$dbname;
$db->dbtype=$dbtype;

if($db->conectar() === false) {
	echo "nao foi possivel conectar ao banco de dados";
	exit(1);
}


$controlador ="";
$acao="";


/**
 * Se usar url o sistema entende que eh esta usando o mod rewrite do apache ou similar 
 * caso contrario ele entenderah que foi passado uma url com os campos 
 * 
 * index.php?controlador=nome_controller&acao=nome_acao&paramentros=urlencode(value1:value2:valueN)
 */
if(isset($_GET['url'])) {
	$url=explode("/",$_GET['url']);
	//var_dump($url);
	$controlador=array_shift($url);
	$acao=array_shift($url);
	$parametros=$url;
} else {

	if(isset($_GET['controlador'])) {
		$controlador = $_GET['controlador'];
	}
	if(isset($_GET['acao'])) {
		$acao = $_GET['acao'];
	}
	
	if(isset($_GET['parametros'])) {
		$parametros=explode(":",urldecode($_GET['parametros']));
	}
	
	if($controlador == "") {
		$controlador="inicio";
	}
	
	if($acao == "") {
		$acao="index";
	}
}
if(file_exists("../app_controller.php")) {
	include("../app_controller.php");
} else {
	include("../lib/app_controller.php");
}

if(file_exists("../app_model.php")) {
	include("../app_model.php");
} else {
	include("../lib/app_model.php");
}


$in_controller=$controllers_folder. $controlador ."_controller.php";
if(!file_exists($in_controller)) {
	echo "<b>Arquivo ".$in_controller." n&atilde;o foi encontrado</b>";
	exit(1);
}

include $in_controller;


$Uname=ucfirst($controlador);
$c_name=$Uname."Controller";

$controller = new $c_name();

/*
 * Adciona data no controller
 */

if(isset($_POST['data'])) {
	$controller->data = $_POST['data'];
}

$uses=$controller->uses;
if($uses !== false) {
	if(count($uses) != 0) {
		
		foreach($uses as $m) {
			$in_model=$models_folder. strtolower($m) .".php";
			if(!file_exists($in_model)) {
				echo "<b>Arquivo ".$in_model." n&atilde;o foi encontrado</b>";
				exit(1);
			}
			$Uname = ucfirst($m);
			include $in_model;
			$mo = new $Uname();
			if($mo->useTable !== false) {
				$mo->db=$db;
				if($mo->useTable == NULL) {
					 $mo->useTable = strtolower($m)."s";
				}
			} 
			
			
			$controller->addModel($mo);
		}//foreach
	
	} else {
		$in_model=$models_folder. $controlador .".php";
		if(!file_exists($in_model)) {
			echo "<b>Arquivo ".$in_model." n&atilde;o foi encontrado</b>";
			exit(1);
		}
	
		include $in_model;
		$m = new $Uname();
		if($m->useTable !== false) {
				$m->db=$db;
				if($m->useTable == NULL) {
					 $m->useTable = $controlador;
				}
		} 
		$controller->addModel($m);
		
	}
	
}

//var_dump($controller);
//var_dump($parametros);
//echo "<br>";

/*
 * seta nome do controller
 */
$controller->name = $controlador;

/*
 * seta action chamada
 */
$controller->action = $acao;


/*
 * executa antes de qualquer acao
 */

$controller->beforeAction();

/*
 * Executa a acao
 */
if(!method_exists($controller,$acao)) {
		echo "<b>Acao <i>". $acao ."</i> nao encontrada</b>";
		exit(1);
}

if(count($parametros) == 0) {
	
	$controller->$acao();
	
} else {
	$par="(";
	$c=count($parametros);
	for($i=0;$i<$c;$i++) {
		$par.= "'".$parametros[$i]."'";
		if($i+1!=$c) $par.=",";
	}
	$par.=")";
	
	eval("\$controller->".$acao.$par. ";");
}

/*
 * Executa depois da acao
 */

$controller->afterAction();

/*
 * cria as variaveis para a view
 */

foreach($controller->vars_for_view as $n => $v) {
	${$n} = $v;
}

/*
 * instancia a classe sessao para a view
 */

$sessao = new Sessao();

$flash_mensagem = $sessao->readFlash();

/*
 * instancia o helper $html e $form
 */
$html = new Html();
$form = new Form(get_class($controller));

/*
 * DebugSQL
 */
$d = Debug::singleton();
$sqlDebug = $d->sql;

/*
 * chama a view
 */

$view_file=$views_folder.$controlador."/".$acao.".phtml";
if(!file_exists($view_file)) {
	echo "<b>View nao encontrado. checar arquivo ".$view_file."</b>";
	exit(1);
}
//$view=file_get_contents($view_file);
ob_start();
include $view_file;
$page_for_layout = ob_get_contents();
ob_end_clean();


/*
 * chama o layout
 */
$layout_file=$layout_folder.$controller->layout.".phtml";
if(!file_exists($layout_file)) {
	echo "<b>Layout nao encontrado. checar arquivo ".$layout_file."</b>";
	exit(1);
}

//$layout=file_get_contents($layout_file);
ob_start();
include $layout_file;
$saida_geral = ob_get_contents();
ob_end_clean();

echo $saida_geral;





?>