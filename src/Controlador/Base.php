<?php
namespace App\Controlador;

use App\Helper\Util;
use App\Helper\Vista;
use App\Modelo\Usuario;
use App\Modelo\AppRoles;
use FMT\Controlador;

class Base extends Controlador {
/** @var array */
	public static $errores = [];
/** @var string */
	public static $metodo;
/** @var Usuario */
	protected $_user;
/** @var Usuario */
	protected $vista_default;

    public function procesar()
    {
        if($this->existe_accion()) {
	        if($this->antes()){
	            $this->{'accion_'.$this->accion}();
	        }
    	}
        $this->despues(); 
        $this->mostrar_vista();
    }

/** @return bool */
	 protected function antes() {
	 	$this->vista_default	= VISTAS_PATH . "/".Util::underscore($this->clase)."/{$this->accion}.php";
	 	$this->vista = new Vista(VISTAS_PATH.'/base.php',['vars'=>[]]);
	 	static::$metodo	= $_SERVER['REQUEST_METHOD'];
		$this->_user	= Usuario::obtenerUsuarioLogueado();
	 	return parent::antes();
	}

	protected function despues() {
		(new Vista(VISTAS_PATH.'/widgets/head.php',['user' => $this->_user,'vista' =>$this->vista]))->pre_render(); 		
		(new Vista(VISTAS_PATH.'/widgets/menu.php',['vista' =>$this->vista]))->pre_render();
		$this->mostrar_errores();
	}

	protected function existe_accion() {
		$metodos = get_class_methods($this);
        if(!in_array('accion_'.$this->accion, $metodos)) {
			$this->_user	= Usuario::obtenerUsuarioLogueado();
			$vista = new Vista(VISTAS_PATH.'/widgets/error_no_encontrado.php');
			$this->vista = new Vista(VISTAS_PATH.'/base.php',['vars'=>['CONTENT' => "$vista"]]);
			return false;
		} else {
			return true;
		}
	}


	protected function mostrar_errores(){
		$vars = [];
	    $error = $this->mensajeria->obtener();
	    $errors = '';
	    $avisos = '';
	    $advertencias = '';
	    foreach ($error as $value) {
	    	switch ($value['tipo']) {
	    		case \FMT\Mensajeria::TIPO_ERROR:
	    			$errors .= (empty($errors)) ? $value['mensaje'] : '<br> <i class="fa fa-times-circle" aria-hidden="true"></i>
 '.$value['mensaje'];
	    			break;
	    		case \FMT\Mensajeria::TIPO_AVISO:
	    			$avisos .= (empty($avisos)) ? $value['mensaje'] : '<br> <i class="fa fa-check" aria-hidden="true"></i>
 '.$value['mensaje'];
	    			break;
	    		case 3:
	    			$advertencias .= (empty($advertencias)) ? $value['mensaje'] : '<br> <i class="fa fa-warning" aria-hidden="true"></i>
 '.$value['mensaje'];
	    			break;	    			
	    	}
		}
		if($errors)
			$vars['ERROR'][]['MSJ'] = $errors;
		if($avisos)
			$vars['AVISO'][]['MSJ'] = $avisos;
		if($advertencias) {
			$vars['AVISO_GENERICO'][] = ['MSJ' => $advertencias, 'TIPO_ALERT' => 'warning', 'FA_ICON' => 'fa fa-warning'];
		}
		$this->vista->add_to_var('vars', $vars);
	}


	public function set_query($key,$value) {
		$this->request->query($key,$value);
	}

/**
 * Obtiene, settea y destruye datos de session para manejar contenido de variables entre controladores.
 * Si "$variable_value == null" obtiene el valor
 * Si "$variable_value == false" destruye la variable y devuelve el ultimo valor
 * Si "$variable_value" es caualquier valor distinto, setea el valor en la variable
 * 
 * @param string	$variable_name - Nombre de la variable a setear, obtener, o destruir.
 * @param mixed		$variable_value	- El valor de la variable, null o false.
 * @return mixed|null
*/
	protected function setGetVarSession($variable_name=null, $variable_value=null){
		static $config = null;
		if($config == null){
			$config	= \FMT\Configuracion::instancia();
		}
		$id_modulo	= $config['app']['modulo'];
		$id_user	= $this->_user->id;
		$id			= "modulo_{$id_modulo}_user_{$id_user}";

		if($variable_value	=== false && !empty($_SESSION[$id][$variable_name])){
			$aux = $_SESSION[$id][$variable_name];
			unset($_SESSION[$id][$variable_name]);
			return $aux;
		}
		if(!empty($_SESSION[$id][$variable_name]) && $variable_value === null){
			return $_SESSION[$id][$variable_name];
		}
		if(!empty($variable_value) && $variable_name !== null) {
			$_SESSION[$id][$variable_name]	= $variable_value;
			return $variable_value;
		}
		return null;
	}
}