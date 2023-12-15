<?php
namespace App\Helper;

class Vista extends \FMT\Vista {
	static $url;
	static $app;
	static $vista;

	/** @var Array - contiene el resultado de \FMT\Configuracion::instancia()*/
	static private $SYSTEM_CONFIG;

	public function add_to_var($var,$val) {
		if(!empty($this->vars[$var])) {
			if(is_array($this->vars[$var]) && is_array($val)) {
				$this->vars[$var] = array_merge_recursive($val,$this->vars[$var]);
			}
		} else {
			$this->set($var,$val);
		}
	}

	public static function get_url($file = false) {
		if (is_null(static::$url)) {
				$prot           = isset($_SERVER['HTTP_X_PROTO'])
						? $_SERVER['HTTP_X_PROTO'] : constant('REQUEST_SCHEME');
				$host           = isset($_SERVER['HTTP_HOST'])
						? $_SERVER['HTTP_HOST'] : constant('HTTP_HOST');
				$dir_app        = preg_replace('/\/[a-zA-Z_]*\.php$/', '', (empty(constant('SCRIPT_NAME')) ? $_SERVER['SCRIPT_NAME'] : constant('SCRIPT_NAME')));
				$url_base       = "{$prot}://{$host}";
				static::$url = $url_base;
				static::$app = $dir_app;
		}
		$url_final = static::$url . static::$app;
		if ($file) {
				if (!preg_match('/^\.\.\//', $file, $aa)) {
						$f = [];
						// $aux = preg_replace('/\/.*$/', '', $file);
						preg_match('/\.[\w]{1,3}$/', $file, $f);
						$ext_file       = !empty($f[0]) ? trim($f[0], '.') : '';
						$separador      = empty(preg_match('/^\//', $file, $aa)) ? '/' : '';
						$url_final = ($ext_file == 'php')       ? static::$url . static::$app . $separador . $file
								: static::$url . static::$app . (($ext_file) ? "/{$ext_file}" : $separador) . "{$separador}{$file}";
				} else {
						$url_final = static::$url . str_replace('..', '', $file);
				}
		}

		return $url_final;
	}

	public function pre_render() {
		$this->render();
	}

	/**
	 * Obtiene las configuraciones del sistema.
	 * @return array
	 */
	public function getSystemConfig()
	{
		if (empty(static::$SYSTEM_CONFIG)) {
			static::$SYSTEM_CONFIG	= \FMT\Configuracion::instancia();
		}
		return static::$SYSTEM_CONFIG;
	}
}
