<?php
$controllers_folder="../controllers/";
$models_folder="../models/";
$views_folder="../views/";
$layout_folder="../layout/";
define('BASE_URL', dirname($_SERVER["SCRIPT_NAME"]));
define('ROOT',dirname($_SERVER["SCRIPT_NAME"]));




/* 
 * declaracoes nao mexer
 */
$controlador=""; //declaracao
$acao="";
$parametros=array();
$url=array();

class Config {
	
	const debug = 0;
	const tempoSessao=15;
	const nomeSessao="CITIUS";
	const modelsDir="../models/";
	
}

?>