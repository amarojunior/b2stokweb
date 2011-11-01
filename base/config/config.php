<?php


class Config {
	
	/**
	 * 
	 * nivel de DEBUG 
	 * 0 = no debug 1 = debug habilitado 
	 * @var int
	 */
	const debug = 0; 
	
	/**
	 * 
	 * Tempo em minutos de duracao da sessao
	 * @var int
	 */
	const tempoSessao=15;
	
	/**
	 * 
	 * Nome da sessao criada no browser
	 * @var string
	 */
	const nomeSessao="B2STOKWEB";
	
	/**
	 * 
	 * Diretorios base para encontrar os arquivos da aplicao
	 * Normalmente nao eh necessario mecher
	 */
	const controllersFolder="../controllers/"; //diretorio onde se encontra os controllers
	const modelsFolder="../models/"; //diretorio onde se encontra os models
	const viewsFolder="../views/"; //diretorio onde se encontra as views
	const layoutFolder="../layout/"; //direotrion onde se encontra os layouts 
	const tema = "default"; //tema a ser usado é um subdiretorio de layouts
	
	/**
	 * 
	 * Diretorio da biblioteca padrao do
	 * framework 
	 * @var string
	 */
	const lib = "../lib";
	
}

?>