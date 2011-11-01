<?php

/**
 * Retorna o caminho para um arquivo css
 *
 * @param string
 * @return string
 */
function getCss($nome) {
	
	$u=BASE_URL."/css/".$nome.".css";
	$css="<link rel=\"stylesheet\" type=\"text/css\" href=\"".$u . "\" />";
	return $css;
}

/**
 * Exibe detalhes de uma variavel
 * inspirado no codigo do cakephp
 * 
 * @param mixed $str
 */
function debug($str) {
	if(Config::debug > 0) {
		$calledFrom = debug_backtrace();
		echo "\n<div id=\"debug\">\n";
		echo '<strong>' . substr(str_replace(ROOT, '', $calledFrom[0]['file']), 1) . '</strong>';
		echo ' (linha <strong>' . $calledFrom[0]['line'] . '</strong>)';
		echo "\n<pre>".print_r($str,true)."</pre>\n";
		echo "\n</div>\n";
	}
}

/**
 * 
 * Debug para as querys
 * @param string $sql
 */
function debugSQL($sql) {
	if(Config::debug>=2) {
		$d = Debug::singleton();
		$d->addSql($sql);
	}
}
/**
 * 
 * Importa novas models
 * @param string $modelName
 */

function importModel($modelName) {
	if(!class_exists($modelName)) {
		include Config::modelsDir."/".strtolower($modelName).".php";
	}
	if(!class_exists($modelName)) {
		debug("Erro ao importar modelo ".$modelName);
	}
}

?>