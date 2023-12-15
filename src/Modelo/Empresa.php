<?php
namespace App\Modelo;

use FMT\Logger;
use App\Helper\Validator;
use FMT\Configuracion;
use App\Helper\Conexiones;
use App\Helper\Curl;


class Empresa extends Modelo {

    /** @var int */
    public $id;
/** @var int */
	public $borrado;
/**@var int**/
	Public $cuit;
/**@var String**/
	Public $razon_social;
/**@var int**/
	Public $codigo_cnrt;
/**@var int**/
	Public $id_camara;
/**@var String**/
    public $direccion;
/**@var String**/
    public $nombre_apoderado;
/**@var int**/
    public $dni_apoderado;

	static public $FLAG   = false;


 	static public function obtener($id=null){
        if($id===null){
            return static::arrayToObject();
        }
        $sql_params = [
            ':id'   => $id,
        ];
        $campos = implode(',', [
            'cuit',
            'razon_social',
            'codigo_cnrt',
            'id_camara',
            'direccion',
            'nombre_apoderado',
            'dni_apoderado',
            'borrado'
        ]);
        $sql    = <<<SQL
            SELECT id, {$campos}
            FROM empresa
            WHERE id = :id
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(!empty($res)){
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

	static public function listar() {
        $campos = implode(',', [
            'cuit',
            'razon_social',
            'codigo_cnrt',
            'id_camara',
            'direccion',
            'nombre_apoderado',
            'dni_apoderado',
            'borrado'
        ]);
        $sql    = <<<SQL
            SELECT id, {$campos}
            FROM empresa
            WHERE borrado = 0
            ORDER BY id ASC
SQL;
        $resp   = (array)(new Conexiones())->consulta(Conexiones::SELECT, $sql);
        if(empty($resp[0])) { return []; }
        foreach ($resp as &$value) {
            $value  = static::arrayToObject($value);
        }
        return $resp;
    }

	public function alta(){
        if(!$this->validar()){
            return false;
        }
        $cnx = new Conexiones();
        $sql_params = [
            ':cuit'             => $this->cuit,
            ':razon_social'     => $this->razon_social,
            ':codigo_cnrt'      => $this->codigo_cnrt,
            ':id_camara'        => $this->id_camara,
            ':direccion'        => $this->direccion,
            ':nombre_apoderado' => $this->nombre_apoderado,
            ':dni_apoderado'    => $this->dni_apoderado,
        ];

        $sql = 'INSERT INTO empresa (cuit, razon_social, codigo_cnrt, id_camara, direccion, nombre_apoderado, dni_apoderado) VALUES (:cuit, :razon_social, :codigo_cnrt,:id_camara, :direccion, :nombre_apoderado, :dni_apoderado)';
        $res = $cnx->consulta(Conexiones::INSERT, $sql, $sql_params);
        if($res !== false){
            $this->id = $res;
            $datos = (array) $this;
            $datos['modelo'] = 'Empresa';
            Logger::event('alta', $datos);
        }
        return $res;
    }

	public function modificacion(){
		$campos	= [
			'cuit'			    => 'cuit = :cuit',
			'razon_social'	    => 'razon_social = :razon_social',
			'codigo_cnrt'	    => 'codigo_cnrt = :codigo_cnrt',
			'id_camara'		    => 'id_camara = :id_camara',
            ':direccion'        => 'direccion = :direccion',
            ':nombre_apoderado' => 'nombre_apoderado = :nombre_apoderado',
            ':dni_apoderado'    => 'dni_apoderado = :dni_apoderado',
		];

		$sql_params	= [
		':cuit'	            => $this->cuit,
		':razon_social'     => $this->razon_social,
		':id_camara'        => $this->id_camara,
		':id'	            => $this->id,
		':codigo_cnrt'      => $this->codigo_cnrt,
        ':direccion'        => $this->direccion,
        ':nombre_apoderado' => $this->nombre_apoderado,
        ':dni_apoderado'    => $this->dni_apoderado,
		];

		$sql	= 'UPDATE empresa SET '.implode(',', $campos).' WHERE id = :id';
		$res	= (new Conexiones())->consulta(Conexiones::UPDATE, $sql, $sql_params);

		if($res !== false){
			$datos = (array) $this;
			$datos['modelo'] = 'empresa';
			Logger::event('modificacion', $datos);
		}
		return $res;
	}

	public function baja(){

        $cnx    = new Conexiones();
        $sql_params = [
            ':id'       => $this->id,
        ];
        $sql    = 'UPDATE empresa SET borrado = 1 WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'Empresa';
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
        $reglas     = [
            'id'      => ['numeric'],
            'cuit'    => ['required','cuit', 'max_length(11)','CuitUnicoActivo()' => function($input) use ($campos){
                    $where  = '';
                    $input      = trim($input);
                    $sql_params = [
                        ':cuit'           => $input
                    ];
                    if(!empty($campos['id'])){
                        $where              = ' AND id != :id';
                        $sql_params[':id']  = $campos['id'];
                    }
                    $sql        = 'SELECT cuit FROM empresa WHERE cuit=:cuit AND borrado = 0'.$where;
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
                    $sql        = 'SELECT cuit FROM empresa WHERE cuit =:cuit AND borrado = 1'.$where;
                    $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
                    if(!empty($resp)){
                        static::$FLAG  = true;
                    }

                    return empty($resp);
            }],
            'razon_social'	    => ['required','texto','max_length(250)'],
            'codigo_cnrt'	    => ['required','numeric','max_length(8)'],
          	'id_camara'		    => ['numeric'],
            'direccion'         => ['required', 'texto', 'max_length(250)'],
            'nombre_apoderado'  => ['required', 'texto', 'max_length(250)'],
            'dni_apoderado'     => ['required','numeric', 'max_length(11)'],
            'borrado'   	    => ['numeric']
        ];
        $nombre= [
            'cuit'    			=> 'Cuit',
            'razon_social'  	=> 'Razón Social',
            'codigo_cnrt'  		=> 'Código CNRT',
            'id_camara'  		=> 'Cámara',
            'direccion'         => 'Dirección',
            'nombre_apoderado'  => 'Nombre del Apoderado',
            'dni_apoderado'     => 'Dni del Apoderado',
        ];

        $validator  = Validator::validate($campos, $reglas, $nombre);
        $validator->customErrors([
            'CuitUnicoActivo()'  	 => 'Ya existe una Empresa con el Cuit ingresado, por favor verifique.',
            'CuitUnicoInactivo()'	 => 'Ya existe una Empresa daba de baja con el Cuit ingresado, debe activarla.'
        ]);
        if ($validator->isSuccess()) {
            return true;
        }
        $this->errores = $validator->getErrors();
        return false;

	}


	static public function arrayToObject($res = []) {
		$campos	= [
			'id'                =>  'int',
            'cuit'              =>  'int',
            'razon_social'      =>  'string',
            'codigo_cnrt'       =>  'int',
            'id_camara'         =>  'int',
            'direccion'         =>  'string',
            'nombre_apoderado'  =>  'string',
            'dni_apoderado'     =>  'int',

		];
		$obj = new self();
		foreach ($campos as $campo => $type) {
			switch ($type) {
				case 'int':
					$obj->{$campo}	= isset($res[$campo]) ? (int)$res[$campo] : null;
					break;
                case 'json':
                    $obj->{$campo}	= isset($res[$campo]) ? json_decode($res[$campo], true) : null;
                    break;
                case 'datetime':
                    $obj->{$campo}	= isset($res[$campo]) ? \DateTime::createFromFormat('Y-m-d H:i:s', $res[$campo]) : null;
                    break;
                case 'date':
                    $obj->{$campo}	= isset($res[$campo]) ? \DateTime::createFromFormat('Y-m-d H:i:s', $res[$campo] . ' 0:00:00') : null;
                    break;
				default:
					$obj->{$campo}	= isset($res[$campo]) ? $res[$campo] : null;
					break;
			}
		}

		return $obj;
	}

	 static public function obtenerPorCuit($cuit=null){
        if($cuit===null){
            return static::arrayToObject();
        }
        $sql_params = [
            ':cuit'   => $cuit,
        ];
        $campos = implode(',', [
            'cuit',
            'razon_social',
            'codigo_cnrt',
            'id_camara',
            'direccion',
            'nombre_apoderado',
            'dni_apoderado',
            'borrado'
        ]);
        $sql    = <<<SQL
            SELECT id, {$campos}
            FROM empresa
            WHERE cuit = :cuit
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(!empty($res)){
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

    public function activar(){
        $cnx    = new Conexiones();
        $sql_params = [
            ':id'       => $this->id,
        ];
        $sql    = 'UPDATE empresa SET borrado = 0 WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'Empresa';
            if (is_numeric($res) && $res > 0) {
                $flag = true;
            } else {
                $datos['error_db'] = $cnx->errorInfo;
            }
            Logger::event('activar', $datos);
        }
        return $flag;
    }

    static public function lista_empresas() {
        $aux=[];
        $mbd = new Conexiones;
        $resultado = $mbd->consulta(Conexiones::SELECT,
            "SELECT
        id, cuit, concat(razon_social,' ','-',' ',cuit) as nombre, codigo_cnrt, id_camara, direccion, nombre_apoderado,dni_apoderado, borrado,razon_social
        FROM empresa
        WHERE borrado = 0
        ORDER BY id ASC");
        if(empty($resultado)) { return []; }
        foreach ($resultado as $value) {
            $aux[$value['id']] = $value;
        }
        return $aux;

    }

    public static function listar_empresas($params = array())
    {
        $campos    = 'id ,cuit, razon_social, codigo_cnrt, camara, direccion, nombre_apoderado, dni_apoderado';
        $sql_params = [];

        $params['order']['campo'] = (!isset($params['order']['campo']) || empty($params['order']['campo'])) ? 'tipo' : $params['order']['campo'];
        $params['order']['dir']   = (!isset($params['order']['dir'])   || empty($params['order']['dir']))   ? 'asc' : $params['order']['dir'];
        $params['start']  = (!isset($params['start'])  || empty($params['start']))  ? 0 :
        $params['start'];
        $params['lenght'] = (!isset($params['lenght']) || empty($params['lenght'])) ? 10 :
        $params['lenght'];
        $params['search'] = (!isset($params['search']) || empty($params['search'])) ? '' :
        $params['search'];

        $consulta = <<<SQL
        SELECT e.id, e.cuit, e.razon_social, e.codigo_cnrt, c.nombre as camara, e.direccion, e.nombre_apoderado, e.dni_apoderado
        FROM empresa e
        LEFT JOIN camara c ON
        e.id_camara = c.id
        WHERE e.borrado = 0
SQL;
        $data = self::listadoAjax($campos, $consulta, $params, $sql_params);
        return $data;
    }

}

