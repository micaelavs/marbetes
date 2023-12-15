<?php
namespace App\Modelo;

use FMT\Logger;
use App\Helper\Validator;
use FMT\Configuracion;
use App\Helper\Conexiones;

class Imprenta extends Modelo {

	/**@var int */
    public $id;
	/**@var int**/
	Public $cuit;
	/**@var String**/
	Public $razon_social;
	/**@var Varchar**/
	public $direccion;
	/**@var String**/
	public $rango;
	/** @var Date */
	public $fecha_ultima_revision;
	/** @var int */
	public $inscripcion_en_afip;
	/** @var int */
	public $modelo_de_marbete;
	/**@var Varchar**/
	public $observacion;
	/**@var int */
	public $borrado;

	//esto es para indicar el valor que va a guardar el input, checkeado = 1 no chequeado = 0.
	const INSCRIPCION_AFIP = 1;
	const MODELO_DE_MARBETE = 1;

	static public $FLAG   = false;

 	public static function obtener($id = null){
    	$obj	= new static;
		if($id===null){
			return static::arrayToObject();
		}
		$sql_params	= [
			':id'	=> $id,
		];
		$campos	= implode(',', [
			'im.id',
			'im.cuit',
			'im.razon_social',
			'im.direccion',
			'im.fecha_ultima_revision',
			'im.inscripcion_en_afip',
			'im.modelo_de_marbete',
			'im.observacion',
			'r_i.rango'
		]);
		$sql	= <<<SQL
			SELECT {$campos}
			FROM imprenta im
			LEFT JOIN rango_imprenta r_i ON
			im.id = r_i.id_imprenta
			WHERE im.id = :id
SQL;
		$res	= (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
		if(!empty($res)){
			return static::arrayToObject($res[0]);
		}
		return static::arrayToObject();
  }

	public static function listar(){
		$conexion = new Conexiones();
		/*$resultado = $conexion->consulta(Conexiones::SELECT,
		'SELECT imp.id,imp.cuit,imp.razon_social,imp.direccion, r_i.rango 
		FROM imprenta imp
		LEFT JOIN rango_imprenta r_i ON
		imp.id = r_i.id_imprenta
		WHERE borrado=0');*/
		$resultado = $conexion->consulta(Conexiones::SELECT,
		'SELECT imp.id,imp.cuit,imp.razon_social,imp.direccion, imp.fecha_ultima_revision, imp.inscripcion_en_afip, imp.modelo_de_marbete, imp.observacion   
		FROM imprenta imp
		WHERE borrado=0');
        if(empty($resultado)){
            return [];
        }
        foreach ($resultado as &$value) {

            $value	= static::arrayToObject($value);
        }
		return $resultado;
	}

	public function alta(){

		$sql_params	= [
			':cuit' 		=> $this->cuit,
			':razon_social' => $this->razon_social,
			':direccion' 	=>	$this->direccion,
			':inscripcion_en_afip' 	=> $this->inscripcion_en_afip,
			':modelo_de_marbete'	=> $this->modelo_de_marbete,
			':observacion'			=> $this->observacion

		];
		if($this->fecha_ultima_revision instanceof \DateTime){
             $sql_params[':fecha_ultima_revision'] = $this->fecha_ultima_revision->format('Y-m-d');
        }

        $sql = 'INSERT INTO imprenta (cuit, razon_social, direccion,fecha_ultima_revision, inscripcion_en_afip, modelo_de_marbete, observacion) VALUES (:cuit,:razon_social, :direccion,:fecha_ultima_revision,:inscripcion_en_afip, :modelo_de_marbete, :observacion)';
     
		$res	= (new Conexiones())->consulta(Conexiones::INSERT, $sql, $sql_params);
		if($res !== false){
			$datos = (array) $this;
			$datos['modelo'] = 'imprenta';
			Logger::event('alta', $datos);
		}
		return $res;
	}

	public function modificacion(){
		
		$campos = [
            'cuit'  => 'cuit = :cuit',
            'razon_social'     => 'razon_social = :razon_social',
            'direccion'        => 'direccion = :direccion',
            'fecha_ultima_revision' => 'fecha_ultima_revision = :fecha_ultima_revision',
            'inscripcion_en_afip'  	=> 'inscripcion_en_afip = :inscripcion_en_afip',
            'modelo_de_marbete'   	=> 'modelo_de_marbete = :modelo_de_marbete',
            'observacion'           => 'observacion = :observacion'
        ];

		$sql_params	= [
			':cuit' 		=> $this->cuit,
			':razon_social' => $this->razon_social,
			':direccion' 	=>	$this->direccion,
			':inscripcion_en_afip' 	=> $this->inscripcion_en_afip,
			':modelo_de_marbete'	=> $this->modelo_de_marbete,
			':observacion'			=> $this->observacion,
			':id'					=> $this->id		

		];

		if($this->fecha_ultima_revision instanceof \DateTime){
             $sql_params[':fecha_ultima_revision'] = $this->fecha_ultima_revision->format('Y-m-d');
        }
		

		$sql = 'UPDATE imprenta SET '.implode(',', $campos).' WHERE id = :id';
		$res	= (new Conexiones())->consulta(Conexiones::UPDATE, $sql, $sql_params);
		if($res !== false){
			$datos = (array) $this;
			$datos['modelo'] = 'imprenta';
			Logger::event('modificacion', $datos);
		}
		return $res;
	}

	public function baja(){
        $cnx    = new Conexiones();
        $sql_params = [
            ':id'       => $this->id,
        ];
        $sql    = 'UPDATE imprenta SET borrado = 1 WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);
        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'Imprenta';
            if (is_numeric($res) && $res > 0) {
                $flag = true;
            } else {
                $datos['error_db'] = $cnx->errorInfo;
            }
            Logger::event('baja', $datos);
        }
        return $flag;
    }

	public function validar(){
		static::$FLAG  = false;
		$campos = (array)$this;
		$reglas = [
			'id'        => ['numeric'],
			'cuit'			=> ['required', 'cuit','CuitUnicoActivo()' => function($input) use ($campos){
                    $where  = '';
                    $input      = trim($input);
                    $sql_params = [
                        ':cuit'           => $input
                    ];
                    if(!empty($campos['id'])){
                        $where              = ' AND id != :id';
                        $sql_params[':id']  = $campos['id'];
                    }
                    $sql        = 'SELECT cuit FROM imprenta WHERE cuit=:cuit AND borrado = 0'.$where;
                    $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);

                    return empty($resp);
            },
            'CuitUnicoInactivo()' => function($input) use ($campos){
                    $where  = '';
                    $input      = trim($input);
                    $sql_params = [
                        ':cuit'           => $input
                    ];

                    if(!empty($campos['id'])){
                        $where              = ' AND id != :id';
                        $sql_params[':id']  = $campos['id'];
                    }
                    $sql        = 'SELECT cuit FROM imprenta WHERE cuit =:cuit AND borrado = 1'.$where;
                    $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
                    if(!empty($resp)){
                        static::$FLAG  = true;
                    }

                    return empty($resp);
            }],
			'razon_social'  => ['required', 'texto', 'max_length(250)'],
			'direccion'  => ['required', 'texto', 'max_length(250)'],
			'fecha_ultima_revision' => ['required', 'fecha'],
			'observacion'			=>['max_length(150)']


		];
		$nombres    = [
			'cuit'          => 'Cuit',
			'razon_social'  => 'Razon Social',
			'direccion'		=> 'Dirección',
			'fecha_ultima_revision' => 'Fecha de última revisión',
			'observacion'	=> 'Observación'
		];
		$validator = Validator::validate((array)$this, $reglas, $nombres);

		  $validator->customErrors([
            'CuitUnicoActivo()'  	 => 'Ya existe una Imprenta con el Cuit ingresado, por favor verifique.',
            'CuitUnicoInactivo()'	 => 'Ya existe una Imprenta dada de baja con el Cuit ingresado, debe activarla.'
        ]);

		if ($validator->isSuccess()) {
			return true;
		}else {
			$this->errores = $validator->getErrors();
			return false;
		}
	}


	static public function arrayToObject($res = []) {
		$campos	= [ 'id' 			=>  'int',
					'cuit' 			=>  'int',
					'razon_social'  =>  'string',
					'direccion'		=> 	'string',
					'fecha_ultima_revision'	=> 'datetime',
					'inscripcion_en_afip'	=> 'int',
					'modelo_de_marbete'		=> 'int',
					'observacion'			=> 'string',
					'rango' 		=> 	'string'
		];
		$obj = new self();
        foreach ($campos as $campo => $type) {
            switch ($type) {
                case 'int':
                    $obj->{$campo}  = isset($res[$campo]) ? (int)$res[$campo] : null;
                    break;
                case 'json':
                    $obj->{$campo}  = isset($res[$campo]) ? json_decode($res[$campo], true) : null;
                    break;
                case 'datetime':
                    $obj->{$campo}  = isset($res[$campo]) ? \DateTime::createFromFormat('Y-m-d H:i:s', $res[$campo]) : null;
                    break;
                case 'date':
                    $obj->{$campo}  = isset($res[$campo]) ? \DateTime::createFromFormat('Y-m-d H:i:s', $res[$campo] . ' 0:00:00') : null;
                    break;
                default:
                    $obj->{$campo}  = isset($res[$campo]) ? $res[$campo] : null;
                    break;
            }
        }
		return $obj;
	}


	static public function lista_imprentas() {
        $aux=[];
        $mbd = new Conexiones;
        $resultado = $mbd->consulta(Conexiones::SELECT,
        "SELECT
        id, cuit, razon_social as nombre, direccion, borrado
        FROM imprenta
        WHERE borrado = 0
        ORDER BY id ASC");
        if(empty($resultado)) { return []; }
        foreach ($resultado as $value) {
            $aux[$value['id']] = $value;
        }
        return $aux;
    }

	public function activar(){
        $cnx    = new Conexiones();
        $sql_params = [
            ':id'       => $this->id,
        ];
        $sql    = 'UPDATE imprenta SET borrado = 0 WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'Imprenta';
            if (is_numeric($res) && $res > 0) {
                $flag = true;
            } else {
                $datos['error_db'] = $cnx->errorInfo;
            }
            Logger::event('activar', $datos);
        }
        return $flag;
    }

    static public function obtenerPorCuit($cuit=null){
        if($cuit===null){
            return static::arrayToObject();
        }
        $sql_params = [
            ':cuit'   => $cuit,
        ];
        $campos = implode(',', [
          	'im.id',
			'im.cuit',
			'im.razon_social',
			'im.direccion',
			'im.fecha_ultima_revision',
			'im.inscripcion_en_afip',
			'im.modelo_de_marbete',
			'im.observacion',
			'r_i.rango',
			'im.borrado'
        ]);
        $sql    = <<<SQL
           SELECT {$campos}
			FROM imprenta im
			LEFT JOIN rango_imprenta r_i ON
			im.id = r_i.id_imprenta
			WHERE im.cuit = :cuit
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(!empty($res)){
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();

    }
}