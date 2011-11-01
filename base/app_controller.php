<?php
class AppController extends Controller {
	
	
	function beforeAction() {
		if($this->name == "Voto" or $this->action == "login" or $this->action == "logout" ) {
			return true;
		} else {
			if(!$this->sessao->check("operador")) {
				$this->redirect(array("controlador"=>"admin","acao"=>"login"));
			}
		}
	}
}
?>