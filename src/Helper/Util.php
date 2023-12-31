<?php
namespace App\Helper;

class Util {
	/**
	 * Devuelve el valor de la posición definida por $key en el arreglo super global $_POST.
	 * En caso de no esta definida la clave en el arreglo, se devolverá null, de otro modo,
	 * si el dato fuese de tipo arreglo, este se devuelve integro. Para datos de tipo
	 * string, se intentará eliminar cualquier etiqueta HTML que este contenga y
	 * se regresa la información contenida.
	 * @param string $key
	 * @return null|string
	 */
	public static function getPost($key) {
		if (array_key_exists($key, $_POST))
			if (!empty($_POST[$key])) {
				if (is_array($_POST[$key])) {

					return $_POST[$key];
				}
				$str = strip_tags(trim($_POST[$key]));
				if (mb_strlen($str) > 0) {
					return $str;
				}
			}

		return null;
	}

	/**
	 * Verifica si la clave requerida $key existe en el arreglo super global $_GET.
	 * De no existir se devuelve null, de otro modo, si fuese un arreglo se
	 * regresa integro, de lo contrario se intentará eliminar las etiquetas
	 * HTML y se regresa el contenido.
	 * @param string $key
	 * @return null|string
	 */
	public static function getGet($key) {
		if (array_key_exists($key, $_GET)) {
			if (is_array($_GET[$key])) {
				return $_GET[$key];
			}

			return strip_tags(trim($_GET[$key]));
		}

		return null;
	}

	/**
	 * Verifica si la fecha $date se encuentra en el rango comprendido entre las
	 * fechas $from y $to.
	 * @param \DateTime $from
	 * @param \DateTime $to
	 * @param string    $date
	 * @return bool
	 */
	public static function dateIsBetween($from, $to, $date = "now") {
		if (is_string($date)) {
			$date = new \DateTime($date);
		}
		if ($date >= $from && $date <= $to) {
			return true;
		}

		return false;
	}

	/**
	 * obtiene el intervalo de cada media hora, desde el rango de las 9:00 hasta las 19:00 para las reservas de salas que se muestran en el Datatable.
	 */
	public static function halfHourTimes() {
		$formatter = function ($time) {
			if ($time % 3600 == 0) {
				return date('G:00', $time);
			} else {
				return date('G:i', $time);
			}
		};
		$halfHourSteps = range(11*3600, 44*1800, 1800);

		return array_map($formatter, $halfHourSteps);
	}

	/**
	 * Obtiene el nombre de clase y se reemplazan las barras invertidas
	 * por slash.
	 * @param $objeto
	 * @return string
	 */
	public static function obtenerClaseDeObjeto($objeto) {
		return str_replace('\\', '/', get_class($objeto));
	}

	/**
	 * Retorna una URL parametrizada.
	 * @param string $uri Por ejemplo: '?c=NombreDeControlador&a=accion&id=5'
	 * @return string
	 */
	public static function getUrl($uri) {
		$url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI']);
		return $url.$uri;
	}

	/**
	 * Returns a human-readable string from a lower case and underscored word by replacing underscores
	 * with a space, and by upper-casing the initial characters.
	 *
	 * @param string String to make more readable.
	 * @return string Human-readable string.
	 *
	 * @see http://phpxref.free.fr/symfony/nav.html?lib/util/sfInflector.class.php.source.html
	 */
	public static function humanize($lower_case_and_underscored_word) {
		if (substr($lower_case_and_underscored_word, -3) === '_id'){
			$lower_case_and_underscored_word = substr($lower_case_and_underscored_word, 0, -3);
		}
		return ucfirst(str_replace('_', ' ', $lower_case_and_underscored_word));
	}

	/**
	 * Devuelve un string con_guiones_bajos o el propio string en CamelCase.
	 *
	 * @param string String a convertir.
	 * @return string String con guiones_bajos.
	 *
	 * @see http://phpxref.free.fr/symfony/nav.html?lib/util/sfInflector.class.php.source.html
	 */
	public static function underscore($camel_cased_word) {
		$tmp = $camel_cased_word;
		$tmp = str_replace('::', '/', $tmp);
		$tmp = static::pregtr($tmp, array('/([A-Z]+)([A-Z][a-z])/' => '\\1_\\2', '/([a-z\d])([A-Z])/'     => '\\1_\\2'));
		return strtolower($tmp);
	}

	/**
	 * Returns subject replaced with regular expression matchs
	 *
	 * @param mixed subject to search
	 * @param array array of search => replace pairs
	 *
	 * @see http://phpxref.free.fr/symfony/nav.html?lib/util/sfInflector.class.php.source.html
	 */
	public static function pregtr($search, $replacePairs) {
		return preg_replace(array_keys($replacePairs), array_values($replacePairs), $search);
	}

/**
 * Reemplaza todos los acentos por sus equivalentes sin ellos
 *
 * @param string $string	- la cadena a sanear
 * @return string	- $string saneada
 */
	public static function sanearString($string=''){
		$string = trim($string);
		$string = str_replace(
			array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
			array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
			$string
	    );
		$string = str_replace(
			array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
			array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
			$string
	    );
		$string = str_replace(
			array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
			array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
			$string
	    );
		$string = str_replace(
			array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
			array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
			$string
	    );
		$string = str_replace(
			array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
			array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
			$string
	    );
		$string = str_replace(
			array('ñ', 'Ñ', 'ç', 'Ç'),
			array('n', 'N', 'c', 'C',),
			$string
	    );
		return $string;
	}
}