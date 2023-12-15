<?php
namespace App\Modelo;

use FMT\Logger;
use App\Helper\Validator;
use App\Helper\Conexiones;


class Tipo_marbete extends Modelo
{

    /**@var int */
    public $id;
    /**@var varchar**/
    public $tipo;
    /**@var varchar**/
    public $descripcion;
    /**@var int */
    public $borrado;

    static public $FLAG   = false;


    public static function obtener($id = null){
        if ($id === null) {
            return static::arrayToObject();
        }
        $sql_params    = [
            ':id'    => $id,
        ];
        $campos    = implode(',', [
            'id',
            'tipo',
            'descripcion',
            'borrado'
        ]);
        $sql    = <<<SQL
			SELECT {$campos}
			FROM tipo_marbete
			WHERE id = :id
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if (!empty($res)) {
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

    static public function obtenerPorTipo($tipo = null)
    {
        if ($tipo === null) {
            return static::arrayToObject();
        }
        $sql_params = [
            ':tipo'   => $tipo,
        ];
        $campos = implode(',', [
            'id',
            'tipo',
            'descripcion',
            'borrado'
        ]);
        $sql    = <<<SQL
            SELECT id, {$campos}
            FROM tipo_marbete
            WHERE tipo = :tipo
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if (!empty($res)) {
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

    public static  function listar(){
        $campos = implode(',', [
            'tipo',
            'descripcion',
            'borrado'
        ]);
        $sql=
<<<SQL
            SELECT id, {$campos}
            FROM tipo_marbete
            WHERE borrado = 0
            ORDER BY id ASC
SQL;
        $resp   = (array)(new Conexiones())->consulta(Conexiones::SELECT, $sql);
        if (empty($resp[0])) {
            return [];
        }
        foreach ($resp as &$value) {
            $value  = static::arrayToObject($value);
        }
        return $resp;
    }

    public function alta(){
        if (!$this->validar()) {
            return false;
        }
        $cnx = new Conexiones();
        $sql_params = [
            ':tipo' => $this->tipo,
            ':descripcion' => $this->descripcion
        ];

        $sql = 'INSERT INTO  tipo_marbete (tipo, descripcion) VALUES (:tipo, :descripcion)';
        $res = $cnx->consulta(Conexiones::INSERT, $sql, $sql_params);
        if ($res !== false) {
            $this->id = $res;
            $datos = (array) $this;
            $datos['modelo'] = 'Tipo_marbete';
            Logger::event('alta', $datos);
        }
        return $res;
    }

    public function modificacion(){
        if (!$this->validar()) {
            return false;
        }
        $cnx = new Conexiones();
        $campos = [
            'tipo'          => 'tipo = :tipo',
            'descripcion'   => 'descripcion = :descripcion'
        ];
        $sql_params = [
            ':id'           => $this->id,
            ':tipo'         => $this->tipo,
            ':descripcion'  => $this->descripcion
        ];
        $sql = 'UPDATE tipo_marbete SET ' . implode(',', $campos) . ' WHERE id = :id';
        $res = $cnx->consulta(Conexiones::INSERT, $sql, $sql_params);
        if ($res !== false) {
            $datos = (array) $this;
            $datos['modelo'] = 'Tipo_marbete';
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
        $sql    = 'UPDATE tipo_marbete SET borrado = 1 WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'Tipo_marbete';
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
            'id'                => ['numeric'],
            'descripcion'       => ['required', 'texto', 'max_length(250)'],
            'borrado'           => ['numeric']
            ];

            if(!is_null($campos['tipo'])){
                $reglas     += [
                    'tipo' => ['texto', 'max_length(40)',
                    'UnicoRegistroActivo()' => function ($input) use ($campos) {
                        $where  = '';
                        $input      = trim($input);
                        $sql_params = [
                            ':tipo'           => '%' . $input . '%',
                            ':tipo_uppercase' => '%' . strtoupper($input) . '%',
                            ':tipo_lowercase' => '%' . strtolower($input) . '%',
                        ];
                        if (!empty($campos['id'])) {
                            $where              = ' AND id != :id';
                            $sql_params[':id']  = $campos['id'];
                        }
                        $sql        = 'SELECT tipo FROM tipo_marbete WHERE (tipo LIKE :tipo OR tipo LIKE :tipo_uppercase OR tipo LIKE :tipo_lowercase) AND borrado = 0' . $where;
                        $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);

                        return empty($resp);
                    }, 'UnicoRegistroInactivo()' => function ($input) use ($campos) {
                        $where  = '';
                        $input      = trim($input);
                        $sql_params = [
                            ':tipo'           => '%' . $input . '%',
                            ':tipo_uppercase' => '%' . strtoupper($input) . '%',
                            ':tipo_lowercase' => '%' . strtolower($input) . '%',
                        ];
                        if (!empty($campos['id'])) {
                            $where              = ' AND id != :id';
                            $sql_params[':id']  = $campos['id'];
                        }
                        $sql        = 'SELECT tipo FROM tipo_marbete WHERE (tipo LIKE :tipo OR tipo LIKE :tipo_uppercase OR tipo LIKE :tipo_lowercase) AND borrado = 1' . $where;
                        $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
                        if (!empty($resp)) {
                            static::$FLAG  = true;
                        }

                        return empty($resp);
                        }
                    ],
                ];

            }else{
                $reglas     += [
                'tipo'  => ['required']
                ];
            }   
          
        $nombres = [
            'tipo'             => 'Tipo',
            'descripcion'      => 'Descripción'
        ];

        $validator  = Validator::validate($campos, $reglas, $nombres);
        $validator->customErrors([
            'UnicoRegistroActivo()'   => 'Ya existe el tipo de Marbete ingresado, por favor verifique.',
            'UnicoRegistroInactivo()' => 'Ya existe el tipo de Marbete ingresado, debe activarla.'
        ]);
        if ($validator->isSuccess()) {
            return true;
        }
        $this->errores = $validator->getErrors();
        return false;
    }


    static public function arrayToObject($res = []){
        $campos    = [
            'id'             =>  'int',
            'tipo'           =>  'string',
            'descripcion'    =>  'string',
        ];
        $obj = new self();
        foreach ($campos as $campo => $type) {
            switch ($type) {
                case 'int':
                    $obj->{$campo}    = isset($res[$campo]) ? (int)$res[$campo] : null;
                    break;
                case 'json':
                    $obj->{$campo}    = isset($res[$campo]) ? json_decode($res[$campo], true) : null;
                    break;
                case 'datetime':
                    $obj->{$campo}    = isset($res[$campo]) ? \DateTime::createFromFormat('Y-m-d H:i:s', $res[$campo]) : null;
                    break;
                case 'date':
                    $obj->{$campo}    = isset($res[$campo]) ? \DateTime::createFromFormat('Y-m-d H:i:s', $res[$campo] . ' 0:00:00') : null;
                    break;
                default:
                    $obj->{$campo}    = isset($res[$campo]) ? $res[$campo] : null;
                    break;
            }
        }
        return $obj;
    }

    public function activar()
    {
        $cnx    = new Conexiones();
        $sql_params = [
            ':id'       => $this->id,
        ];
        $sql    = 'UPDATE tipo_marbete SET borrado = 0 WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'Tipo_marbete';
            if (is_numeric($res) && $res > 0) {
                $flag = true;
            } else {
                $datos['error_db'] = $cnx->errorInfo;
            }
            Logger::event('activar', $datos);
        }
        return $flag;
    }
    
     static public function lista_tipo_marbetes() {
        $aux=[];
        $mbd = new Conexiones;
        $resultado = $mbd->consulta(Conexiones::SELECT,
        "SELECT
        id, tipo, descripcion as nombre, borrado 
        FROM tipo_marbete
        WHERE borrado = 0 
        ORDER BY id ASC");
        if(empty($resultado)) { return []; }
        foreach ($resultado as $value) {
            $aux[$value['id']] = $value;
        }
        return $aux;
        

    }

    static public function lista_tipo_marbetes_select() {
        $aux=[];
        $mbd = new Conexiones;
        $resultado = $mbd->consulta(Conexiones::SELECT,
        "SELECT
        id, tipo as nombre, borrado 
        FROM tipo_marbete
        WHERE borrado = 0 
        ORDER BY id ASC");
        if(empty($resultado)) { return []; }
        foreach ($resultado as $value) {
            $aux[$value['id']] = $value;
        }
        return $aux;
        

    }

}