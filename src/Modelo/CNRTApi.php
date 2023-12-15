<?php 

namespace App\Modelo; 

class CNRTApi extends \FMT\ApiCURL{

	static private $ERRORES = false; 
	

	static public function getEmpresa($cuit = null) {
		$api = static::getInstance(); 
		$return = $api->consulta('GET','/empresas?nroDocumento='.$cuit); 
		if($api->getStatusCode() != '200'){ 
			static::setErrores($return['mensajes']); 
			return false; 
		} 

		return $return['data']; 

	} 

	static protected function setErrores($data=false){ 

		static::$ERRORES = $data; 

	} 

	static public function getErrores(){ 

		return static::$ERRORES; 

	} 

} 

