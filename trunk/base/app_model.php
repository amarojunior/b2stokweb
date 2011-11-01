<?php
class AppModel extends Model {
	
	
	function verificaCpf($cpf) { 
			
			$nulos = array("12345678909","11111111111","22222222222","33333333333",
	               "44444444444","55555555555","66666666666","77777777777",
	               "88888888888","99999999999","00000000000");
			/* Retira todos os caracteres que nao sejam 0-9 */
			$cpf = ereg_replace("[^0-9]", "", $cpf);
			
			/*Retorna falso se houver letras no cpf */
			if (!(ereg("[0-9]",$cpf))) {
			    return false;
			}
			
			/* Retorna falso se o cpf for nulo */
			if( in_array($cpf, $nulos) )
			    return false;
			
			/*Calcula o penúltimo dígito verificador*/
			$acum=0;
			for($i=0; $i<9; $i++) {
			  $acum+= $cpf[$i]*(10-$i);
			}
			
			$x=$acum % 11;
			$acum = ($x>1) ? (11 - $x) : 0;
			/* Retorna falso se o digito calculado eh diferente do passado na string */
			if ($acum != $cpf[9]){
			  return false;
			}
			/*Calcula o último dígito verificador*/
			$acum=0;
			for ($i=0; $i<10; $i++){
			  $acum+= $cpf[$i]*(11-$i);
			}  
			
			$x=$acum % 11;
			$acum = ($x > 1) ? (11-$x) : 0;
			/* Retorna falso se o digito calculado eh diferente do passado na string */
			if ( $acum != $cpf[10]){
			  return false;
			}  
			/* Retorna verdadeiro se o cpf eh valido */
			return true;
	}
}