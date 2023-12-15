<?php
namespace App\Modelo;

use App\Modelo\Usuario;
use App\Helper\Conexiones;
use FMT\Configuracion;

abstract class Modelo extends \FMT\Modelo {
	protected static $ASOCIACIONES	= array();
	public static $FLAG = false;

/**
 * Al hace "new CualquierModelo" se llama a "self::getConexion()"
*/
	final protected function __construct(){
		parent::__construct();
		static::setVarsConexion();
	}

/**
* Inicia conexion y setea variables para MySQL.
* Invoca a Conexiones:: para mantener la sesion viva con los parametros necesarios.
*
* @param string $database	- nombre alternativo DB
* @return Conexiones::
*/
	final static protected function setVarsConexion($database=''){
		if(!static::$FLAG ) {
			$config	= Configuracion::instancia();
			$cnx	= new Conexiones($database);

			$sql	= <<<SQL
	SET @id_usuario := :id_usuario
SQL;
			$cnx->consulta(Conexiones::SELECT, $sql, [
			':id_usuario'	=> Usuario::obtenerUsuarioLogueado()->id,
			]);
			static::$FLAG = true;
		}
	}

/**
 * Sirve para controlar la respuesta de metodos ::obtener o ::listar que implenten ::arrayToObject().
 * La idea es poder controlar las vinculaciones devueltas por un modelo.
 * USO:
 * - Invocar `::contiene()` para limitar el objeto a los atributos propios, antes de invocar un `obtener()` o `::listar()`.
 * - Invocar `::contiene(['attr1', 'attr2'])` para limitar el objeto a los atributos propios y los especificados en el array, antes de invocar un `::obtener()" o `::listar()`.
 * - Invocar `::contiene(['attr1', 'attr2', 'modelo' => ['attr1', 'attr2']])` para limitar el objeto a los atributos propios y los especificados en el array, antes de invocar un `::obtener()" o `::listar()`.
 *
 * NUNCA se debe facilitar este comportamiento dentro de un modelo sin aplicar el respectivo destructor "::borrarContiene();"
 *
 * @param bool|array $asociaciones - Recibe true o un array de objectos asociados
 * @return void
*/
	final public static function contiene($asociaciones=true){
		$modelo	= get_called_class();
		if($asociaciones === true || (is_array($asociaciones) && count($asociaciones) == 0)){
			static::$ASOCIACIONES[$modelo]	= true;
		}
		if(is_array($asociaciones)){
			static::$ASOCIACIONES[$modelo]	= [];
			foreach ($asociaciones as $indice => $attr) {
				if(is_string($indice) && !is_numeric($indice)){
					$modelo_alt	= 'App\\Modelo\\' . ucfirst($indice);
					if(class_exists('\\'.$modelo_alt) && method_exists('\\'.$modelo_alt, 'contiene')){
						$modelo_alt::contiene($attr);
					}
					$attr = $indice;
				}
				if(!array_key_exists($attr, get_class_vars($modelo))) {
					throw new \Exception("El atributo seteado para {$modelo}::contiene() no existe en la clase.", 1);
				} else {
					static::$ASOCIACIONES[$modelo][]	= $attr;
				}
			}
		}
	}

/**
 * Resetea la propiedad "::$ASOCIACIONES" a "false". Se debe llamar dentro de los metodos staticos "::obtener()" o "::listar()" antes de cualquier "return".
 * Este metodo se puede usar directamente en un "return" pasandole como argumento lo que se espera devolver.
 *
 * @param null|mixed	$return - Devuelve lo que recibe.
 * @return null|mixed
*/
	final protected static function borrarContiene(&$return=null){
		if(isset(static::$ASOCIACIONES[get_called_class()])){
			static::$ASOCIACIONES[get_called_class()]	= false;
		}

		return $return;
	}
/**
 * Obtiene las asociaciones.
 *
 * Cuando las asociaciones al modelo son un array y "$attribute" es false; devuelve false
 * Cuando las asociaciones al modelo son un array y "$attribute" es string; devuelve false|true
 * Cuando la asociacion al modelo es bool devuelve false|true
 *
 * @param bool|string	$attribute - Attributo a comprobar en el array de asociaciones
 * @return boolean
*/
	final protected static function getContiene($attribute=false){
		if(!isset(static::$ASOCIACIONES[get_called_class()]) && $attribute===false){
			return false;
		}
		if(!isset(static::$ASOCIACIONES[get_called_class()]) && $attribute!==false){
			return true;
		}
		$assoc	= static::$ASOCIACIONES[get_called_class()];

		if(is_bool($assoc)){
			return is_string($attribute) ? !$assoc : $assoc;
		}
		if(is_array($assoc) && $attribute===false){
			return false;
		}

		if(is_array($assoc) && is_string($attribute)){
			if(in_array($attribute, $assoc)){
				return true;
			}
			return false;
		}
		return $assoc;
	}

/**
 * Convierte un array a objeto usando los atributos del modelo.
 * Uso:
 * - Este metodo esta diseñado para ser reimplementado en cada Modelo extendido usando `parent::arrayToObject($respSQL,$campos);`
 * - Se le pasan por lo menos 2 argumentos, pero en la firma aqui escrita se especifica solo una por razones de retrocompatibilidad.
 * - Si no recibe ningun argumento devuelve la instancia del objeto vacio.
 * - Si recibe un solo argumento y no es un array, devuelve lo que recibe.
 * - El primer argumento normalmente es un array asociativo devuelto por `Conexiones::->consulta(Conexiones::SELECT,...)`, el segundo es un array asociativo cuyo indice es el atributo del objeto y el valor un tipo de dato: (`int`, `json`, `datetime`, `date`, `float`).
 * - Por razones de firma, compatibilidad o mera estetica, se le puede pasar todos los parametros que guste, siempre y cuendo el primero sea resultado de la consulta a la base de datos y el ultimo, el mapeo de campos.
 * - Solo los campos mapeados, seran cargados en los argumentos, el resto quedaran en vacios.
 *
 *
 * --- Ejemplo: `$respSQL` ---
 * ```php
 * [
 *  'id'        => '2',
 *  'fecha'     => '01-01-2020 08:00:00'
 *  'descricion'=> 'Texto'
 * ]
 * ```
 * --- Ejemplo: `$campos` ---
 *
 * ```php
 * [
 *  'id'        => 'int',
 *  'fecha'     => 'datetime'
 *  'descricion'=> 'string'
 * ]
 * ```
 * --- Ejemplo de implementacion ---
 *
 * ```php
 * static public function arrayToObject($res=[]) {
 *              $campos = [
 *                      'id'                    => 'int',
 *                      'fecha'                 => 'date',
 *                      'descripcion'   => 'string',
 *              ];
 *              return parent::arrayToObject($res, $campos);
 * }
 * ```
 *
 * @param array $respSQL - Normalmente la respuesta de una consulta a la base de datos.
 * @param array $campos - Se omite en la firma pero es requerido para el correcto funcionamiento.
 * @return static::
 */
    static public function arrayToObject($respSQL=null) {
        $child_obj    = new static;

        if($respSQL === null || (is_array($respSQL) && count($respSQL) == 0)){
            return $child_obj;
        }

        $argumentos = func_get_args();
        $campos     = count($argumentos)==null ? [] : $argumentos[count($argumentos)-1];
        $campos         = (!empty($child_obj->campos) && empty($campos))
            ? $child_obj->campos
            : $campos;

        // Si recibe un solo argumento y no es un array, devuelve lo que recibe.
        if(!is_array($respSQL) || $respSQL === $campos){
            return $respSQL;
        }
        if($respSQL !== $campos && is_array($respSQL) && count($respSQL) > 0 && (empty($campos) || !is_array($campos))){
            throw new \Exception('Esta intentando usar ::arrayToObject() sin el argumento $campos que mapea los campos con respecto a su tipo.', 1);
        }

        foreach ($campos as $campo => $type) {
            switch ($type) {
                case 'int':
                    $child_obj->{$campo}        = isset($respSQL[$campo]) ? (int)$respSQL[$campo] : null;
                    break;
                case 'float':
                    $child_obj->{$campo}        = isset($respSQL[$campo]) ? (float)$respSQL[$campo] : null;
                    break;
                case 'json':
                    $child_obj->{$campo}        = isset($respSQL[$campo]) ? json_decode($respSQL[$campo], true) : null;
                    break;
                case 'datetime':
                    $child_obj->{$campo}        = isset($respSQL[$campo]) ? \DateTime::createFromFormat('Y-m-d H:i:s.u', $respSQL[$campo].'.000000') : null;
                    break;
                case 'date':
                                        $child_obj->{$campo}    = isset($respSQL[$campo])
                                                ? (($respSQL[$campo] == '0000-00-00') ? null : \DateTime::createFromFormat('Y-m-d H:i:s.u', $respSQL[$campo].' 0:00:00.000000'))
                                                : null;
                    break;
                default:
                    $child_obj->{$campo}        = isset($respSQL[$campo]) ? $respSQL[$campo] : null;
                    break;
            }
        }
        return $child_obj;
    }

 
 /**
 * Abstrae el manejo de tablas via ajax.
 *
 * @param campos string	  - String que contiene los campos que se mostraran separadosp por comas.
 * @param consulta string - Query que se consultara para crear el listado. NO debe tener ";" al final
 * @param params array    - contiene los diferentes parametros que se utilizaran en la consulta.
 * ej. params	= [
 *			'order'		=> [
 *				[
 * 					'campo'	=> 'pedido_especifico',
 * 					'dir'	=> 'asc',
 *				],
 *			],
 *			'start'     => 0,
 *          'lenght'    => 10,
 *			'search'	=> '',
 *			'filtros'	=>  [],
 *			'count'		=> false
 *		];
 * @param sql_params array - Contiene los valores de reemplazo para los campos que seran parte de las condiciones de filtrado. 
 * @param especiales array - Contiene el valor de elementos especiales que podrian ser eliminados en los reemplazos usados para armar la query.
 * 							 El array contendra etiqueta=>valor ej {{{AA}}} =>'*'	
 * @param debug bool 	   - Activa debug del metodo.
 * Ej. ['nombre_campo' => valor];  	
 * @return array 
*/
	public static function listadoAjax($campos, $consulta, $params=array(), $sql_params=array(),$especiales=null,$debug=false) {

		$cnx    = new Conexiones();
        $condicion = '';
		$order= '';
		$consulta = "SELECT * FROM ({$consulta}) AS t WHERE id>=1";
        /*busca uno o cuando hubiere despues del where en $consulta y los guarda en $where*/
		if (preg_match('/ where.+;$/i',$consulta, $where)) {
            $consulta = str_replace($where[0], ' WHERE ', $consulta);

            $where = explode('AND', preg_replace(['/and/','/where/i'], ['AND', ''], $where[0]));

            if ($params['filtros']) {
                $flag = false;
                foreach ($where as $i => $filtro) {
                    foreach ($params['filtros'] as $key => $value) {
                        if (!is_null($value) && !empty($value) && preg_match('/' . $key . '/', $filtro)){
                            $flag= true;
                            $sql_params[":{$key}"] = $value;
                        }
                    }
                    if(preg_match("/\:/",$filtro) && !$flag){
                        unset($where[$i]);
                    }
                    $flag = false;
                }

            }
            $consulta .=  implode ('AND', $where);
        }
        $campos_array=explode(',', $campos);
		$counter_query  = str_replace('*', "COUNT(DISTINCT {$campos_array[0]})  AS total",$consulta);
		
	    if(!empty($params['search'])){
	        $search = [];
	        foreach (explode(' ', (string)$params['search']) as $indice => $texto) {
	            $search_campos = '';
	            foreach ($campos_array as $value) {
	                $search_campos .= ($search_campos=='') ?"$value LIKE :search{$indice}" : " OR $value LIKE :search{$indice}";
	            }
	            $search[] = $search_campos;
	            $sql_params[":search{$indice}"] = "%{$texto}%";
	        }
	        $buscar =  implode(' AND ', $search);
			$condicion .= (!preg_match('/where/i', $consulta)) ? " WHERE {$buscar}" : " AND {$buscar} ";
	    }
	    /*Orden de las columnas */
	    $orderna = [];
		foreach ($params['order'] as $i => $val) {
			if(isset($val['dir']) && in_array($val['dir'],['asc','desc']))
				$orderna[]  = "{$val['campo']} {$val['dir']}";
		}
		
		if (!empty($orderna) ) {
			$order  .=' ORDER BY '. implode(',', $orderna);
		}	

	    /**Limit: funcionalidad: desde-hasta donde se pagina */
	    $limit  = (isset($params['lenght']) && isset($params['start']))
	                        ? " LIMIT  {$params['start']}, {$params['lenght']}" :   ' ';
		
		$consulta = str_replace(['*',';'], [$campos,''],$consulta);
		if($especiales){
			foreach($especiales as $etiqueta => $valor) {
				$counter_query = str_replace($etiqueta,$valor,$counter_query);
				$consulta = str_replace($etiqueta,$valor,$consulta);
			}
		}
		
		$recordsTotal   =  $cnx->consulta(Conexiones::SELECT, $counter_query, $sql_params)[0]['total'];
		$counter_query = str_replace(';', '',$counter_query);
		$recordsFiltered = $cnx->consulta(Conexiones::SELECT, $counter_query. $condicion,  $sql_params)[0]['total'];
	    $lista           = $cnx->consulta(Conexiones::SELECT, $consulta.$condicion. $order.$limit, $sql_params);
		
		if($lista){
	        foreach ($lista as $key => &$value) {
	            foreach ($value as $ke => $val) {
	                if (preg_match('/^\d{4}\-\d{2}\-\d{2}.*/', $val)) {
	                	/*quito hora minuto y segundo*/
	                    $value[$ke] = \DateTime::createFromFormat('Y-m-d H:i:s',$val)->format('d/m/Y H:i');

	                }
	            }
	            $value  = (object)$value;
	        }
	    }
		if($debug){
			print_r([$sql_params,$campos, $consulta, $params, $sql_params]);
			print_r(['recordsTotal', $recordsTotal,$counter_query]);
			print_r(['recordsFiltered', $recordsFiltered,$counter_query. $condicion]);
			print_r(['data', $lista,str_replace(['*',';'], [$campos,''],$consulta).$condicion. $order.$limit]);
			die;
		}
	    return [
	        'recordsTotal'    => !empty($recordsTotal) ? $recordsTotal : 0,
	        'recordsFiltered' => !empty($recordsFiltered) ? $recordsFiltered : 0,
	        'data'            => $lista ? $lista : [],
	    ];
	} 

}