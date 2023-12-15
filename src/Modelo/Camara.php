<?php
namespace App\Modelo;

use FMT\Logger;
use App\Helper\Validator;
use App\Helper\Conexiones;


class Camara extends Modelo {

    /** @var int */
    public $id;
/** @var int */
	public $borrado;
/**@var String**/
	Public $nombre;
/**@var String**/
	Public $descripcion;

	static public $FLAG   = false;	


 	static public function obtener($id=null){
        if($id===null){
            return static::arrayToObject();
        }
        $sql_params = [
            ':id'   => $id,
        ];
        $campos = implode(',', [
            'id',
            'nombre',
            'descripcion',
            'borrado'
        ]);
        $sql    = <<<SQL
            SELECT id, {$campos}
            FROM camara
            WHERE id = :id
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(!empty($res)){
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

  	static public function obtenerPorNombre($nombre=null){ 
        if($nombre===null){
            return static::arrayToObject();
        }
        $sql_params = [
            ':nombre'   => $nombre,
        ];
        $campos = implode(',', [
            'id',
            'nombre',
            'descripcion',
            'borrado'
        ]);
        $sql    = <<<SQL
            SELECT id, {$campos}
            FROM camara
            WHERE nombre = :nombre
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(!empty($res)){
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

	 static public function listar() {
        $campos = implode(',', [
            'nombre',
            'descripcion',
            'borrado'
        ]);
        $sql    = <<<SQL
            SELECT id, {$campos}
            FROM camara
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
            ':nombre' => $this->nombre,
            ':descripcion' => $this->descripcion
        ];

        $sql = 'INSERT INTO camara (nombre, descripcion) VALUES (:nombre, :descripcion)';
        $res = $cnx->consulta(Conexiones::INSERT, $sql, $sql_params);
        if($res !== false){
            $this->id = $res;
            $datos = (array) $this;
            $datos['modelo'] = 'Camara';
            Logger::event('alta', $datos);
        }
        return $res;
    }

    public function modificacion(){
        if(!$this->validar()){
            return false;
        }
        $cnx = new Conexiones();
        $campos = [
            'nombre'    => 'nombre = :nombre',
            'descripcion'  => 'descripcion = :descripcion'
        ];
        $sql_params = [
            ':id'       => $this->id,
            ':nombre'   => $this->nombre,
            ':descripcion' => $this->descripcion
        ];
        $sql = 'UPDATE camara SET '.implode(',', $campos).' WHERE id = :id';
        $res = $cnx->consulta(Conexiones::INSERT, $sql, $sql_params);
        if($res !== false){
            $datos = (array) $this;
            $datos['modelo'] = 'Camara';
            Logger::event('modificacion', $datos);
            return true;
        }
        return false;
    }

    public function baja(){
        $cnx    = new Conexiones();
        $sql_params = [
            ':id'       => $this->id,
        ];
        $sql    = 'UPDATE camara SET borrado = 1 WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'Camara';
            if (is_numeric($res) && $res > 0) {
                $flag = true;
            } else {
                $datos['error_db'] = $cnx->errorInfo;
            }
            Logger::event('baja', $datos);
        }
        return $flag;
    }

	public function validar() {
        static::$FLAG  = false;
        $campos = (array)$this;
        $reglas     = [
            'id'        => ['numeric'],
            'descripcion'       => ['required','texto','max_length(250)'],
            //'nombre'    => ['required','texto', 'max_length(45)'], parto la validación en dos partes
            'borrado'   => ['numeric']
        ];
        
        if(!is_null($campos['nombre'])){
            $reglas     += [
                'nombre'  => ['texto', 'max_length(45)', 'UnicoRegistroActivo()' => function($input) use ($campos){
                    $where  = '';
                    $input      = trim($input);
                    $sql_params = [
                        ':nombre'           => '%'.$input.'%',
                        ':nombre_uppercase' => '%'.strtoupper($input).'%',
                        ':nombre_lowercase' => '%'.strtolower($input).'%',
                    ];
                    if(!empty($campos['id'])){
                        $where              = ' AND id != :id';
                        $sql_params[':id']  = $campos['id'];
                    }
                    $sql        = 'SELECT nombre FROM camara WHERE (nombre LIKE :nombre OR nombre LIKE :nombre_uppercase OR nombre LIKE :nombre_lowercase) AND borrado = 0'.$where;
                    $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
                 
                    return empty($resp);
            },
            'UnicoRegistroInactivo()' => function($input) use ($campos){
                    $where  = '';
                    $input      = trim($input);
                    $sql_params = [
                        ':nombre'           => '%'.$input.'%',
                        ':nombre_uppercase' => '%'.strtoupper($input).'%',
                        ':nombre_lowercase' => '%'.strtolower($input).'%',
                    ];
                    if(!empty($campos['id'])){
                        $where              = ' AND id != :id';
                        $sql_params[':id']  = $campos['id'];
                    }
                    $sql        = 'SELECT nombre FROM camara WHERE (nombre LIKE :nombre OR nombre LIKE :nombre_uppercase OR nombre LIKE :nombre_lowercase) AND borrado = 1'.$where;
                    $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
                    if(!empty($resp)){
                        static::$FLAG  = true;
                    }
                
                    return empty($resp);
                }]
            ];
        }else{
            $reglas     += [
                'nombre'  => ['required']
            ];
        }

        $nombre= [
            'nombre'    	=> 'Nombre',
            'descripcion'  	=> 'Descripción'
        ];

        $validator  = Validator::validate($campos, $reglas, $nombre);
        $validator->customErrors([
            'UnicoRegistroActivo()'   => 'Ya existe una Cámara con el Nombre ingresado, por favor verifique.',
            'UnicoRegistroInactivo()' => 'Ya existe una Cámara con el Nombre, debe activarla.'
        ]);
        if ($validator->isSuccess()) {
            return true;
        }
        $this->errores = $validator->getErrors();
        return false;
    }
	
  
	static public function arrayToObject($res = []) {
		$campos	= [
		'id' =>  'int',
		'nombre' =>  'string',
		'descripcion' =>  'string',
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

	public function activar(){
        $cnx    = new Conexiones();
        $sql_params = [
            ':id'       => $this->id,
        ];
        $sql    = 'UPDATE camara SET borrado = 0 WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'Camara';
            if (is_numeric($res) && $res > 0) {
                $flag = true;
            } else {
                $datos['error_db'] = $cnx->errorInfo;
            }
            Logger::event('activar', $datos);
        }
        return $flag;
    }

    static public function lista_camaras() {
        $aux=[];
        $mbd = new Conexiones;
        $resultado = $mbd->consulta(Conexiones::SELECT,
        "SELECT
        id, nombre, descripcion, borrado 
        FROM camara
        WHERE borrado = 0 
        ORDER BY id ASC");
        if(empty($resultado)) { return []; }
        foreach ($resultado as $value) {
            $aux[$value['id']] = $value;
        }
        return $aux;
        

    }
}

