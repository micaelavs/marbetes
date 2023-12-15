<?php
namespace App\Modelo;

use FMT\Logger;
use App\Helper\Validator;
use App\Helper\Conexiones;
//use App\Modelo;


class Pedido_marbete extends Modelo {

/** @var int */
    public $id;
/** @var int */
    public $id_empresa;
/** @var int */
    public $id_imprenta;
/** @var int */
    public $id_tipo_marbete;
/** @var Date */
    public $fecha_solicitud;
/** @var int */
    public $cantidad;
/** @var int */
    public $estado = 1;
/** @var Date */
    public $fecha_autorizacion;
/** @var int */
    public $cantidad_autorizada;
/** @var string */
    public $observaciones = null;
/** @var int */
    public $borrado;

    const PEDIDO_SOLICITADO = 1;
    const PEDIDO_AUTORIZADO = 2;
    const PEDIDO_FIRMADO = 3;
    const PEDIDO_RECHAZADO = 4;
    const PEDIDO_IMPRESO_ENTREGADO = 5;
    const PEDIDO_ANULADO = 6;

    /*Máximo digito permitido en la secuencia de asignación de marbetes*/
    const MAXIMO_DIGITO_PERMITIDO = 999999;


    static public $TIPO_PEDIDOS = [
        self::PEDIDO_SOLICITADO   => ['id' => self::PEDIDO_SOLICITADO, 'nombre' => 'Solicitado'],
        self::PEDIDO_AUTORIZADO   => ['id' => self::PEDIDO_AUTORIZADO, 'nombre' => 'Autorizado'],
        self::PEDIDO_FIRMADO      => ['id' => self::PEDIDO_FIRMADO, 'nombre' => 'Firmado'],
        self::PEDIDO_RECHAZADO      => ['id' => self::PEDIDO_RECHAZADO, 'nombre' => 'Rechazado'],
        self::PEDIDO_IMPRESO_ENTREGADO  => ['id' => self::PEDIDO_IMPRESO_ENTREGADO, 'nombre' => 'Impreso/Entregado'],
        self::PEDIDO_ANULADO  => ['id' => self::PEDIDO_ANULADO, 'nombre' => 'Anulado']
    ];

    static public function obtener($id=null){ 
        if($id===null){
            return static::arrayToObject();
        }
        $sql_params = [
            ':id'   => $id,
        ];
        $campos = implode(',', [
            'p_m.id_empresa',
            'p_m.id_imprenta',
            'p_m.id_tipo_marbete',
            'p_m.fecha_solicitud',
            'p_m.cantidad',
            'p_m.estado',
            'p_m.observaciones',
            'aut.fecha_autorizacion',
            'aut.cantidad_autorizada',
            'p_m.borrado'
        ]);
        $sql    = <<<SQL
            SELECT p_m.id, {$campos}
            FROM pedido_marbete p_m
            LEFT JOIN autorizacion aut ON
            p_m.id = aut.id_pedido
            WHERE p_m.id = :id
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(!empty($res)){
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

    static public function listar() {
       
    }

    public static function listar_pedidos($params = array())
    {
        $campos    = 'id, empresa, tipo_marbete, imprenta, fecha_solicitud, cantidad_solicitada, cantidad_autorizada, estado, observaciones';
        $sql_params = [];
        $where = [];

        $condicion = "AND p.borrado = 0 AND e.borrado = 0 AND tm.borrado = 0 AND i.borrado = 0";

        $params['order']['campo'] = (!isset($params['order']['campo']) || empty($params['order']['campo'])) ? 'tipo' : $params['order']['campo'];
        $params['order']['dir']   = (!isset($params['order']['dir'])   || empty($params['order']['dir']))   ? 'asc' : $params['order']['dir'];
        $params['start']  = (!isset($params['start'])  || empty($params['start']))  ? 0 :
        $params['start'];
        $params['lenght'] = (!isset($params['lenght']) || empty($params['lenght'])) ? 10 :
        $params['lenght'];
        $params['search'] = (!isset($params['search']) || empty($params['search'])) ? '' :
        $params['search'];

        $default_params = [
            'filtros'   => [

            ]
        ];

        $params['filtros']  = array_merge($default_params['filtros'], $params['filtros']);
        $params = array_merge($default_params, $params);

        /*Sin Filtros */
        
        $condicion .= !empty($where) ? ' WHERE ' . \implode(' AND ',$where) : '';

        if(!empty($params['search'])){
            $indice = 0;
            $search[]   = <<<SQL
            (p.id like :search{$indice} OR e.razon_social like :search{$indice} OR tm.tipo like :search{$indice} OR i.razon_social like :search{$indice} OR p.fecha_solicitud like :search{$indice} OR p.cantidad like :search{$indice} OR aut.cantidad_autorizada like :search{$indice}) 
SQL;
            $texto = $params['search'];
            $sql_params[":search{$indice}"] = "%{$texto}%";

            $buscar =  implode(' AND ', $search);
            $condicion .= empty($condicion) ? "{$buscar}" : " AND {$buscar} ";

        }

        $consulta = <<<SQL
        SELECT p.id, e.razon_social as empresa, tm.tipo as tipo_marbete, i.razon_social as imprenta, p.fecha_solicitud, p.cantidad as cantidad_solicitada, aut.cantidad_autorizada, p.estado, p.observaciones
        FROM pedido_marbete p
        INNER JOIN empresa e ON
        p.id_empresa = e.id
        INNER JOIN tipo_marbete tm ON
        p.id_tipo_marbete = tm.id
        INNER JOIN imprenta i ON
        p.id_imprenta = i.id
        LEFT JOIN autorizacion aut ON
        aut.id_pedido = p.id     
        $condicion
SQL;
        
        $data = self::listadoAjax($campos, $consulta, $params, $sql_params);
        return $data;
    }

    public static function listar_pedidos_excel($params){
        $cnx    = new Conexiones();
        $sql_params = [];
        $where = [];
        $condicion = '';
        $order = '';
        $search = [];

        $default_params = [
            'order'     => [
                [
                    'campo' => 'id',
                    'dir'   => 'ASC',
                ],
            ],
            'start'     => 0,
            'lenght'    => 10,
            'search'    => '',
            'filtros'   => [

            ],
            'count'     => false
        ];

        $params['filtros']  = array_merge($default_params['filtros'], $params['filtros']);
        $params = array_merge($default_params, $params);

        $sql= <<<SQL
        SELECT p.id, e.razon_social as empresa, tm.tipo as tipo_marbete, i.razon_social as imprenta, DATE_FORMAT(p.fecha_solicitud,'%d/%m/%y') as fecha_solicitud, p.cantidad as cantidad_solicitada, aut.cantidad_autorizada, p.estado, p.observaciones
SQL;

    $from = <<<SQL
        FROM pedido_marbete p
        INNER JOIN empresa e ON
        p.id_empresa = e.id
        INNER JOIN tipo_marbete tm ON
        p.id_tipo_marbete = tm.id
        INNER JOIN imprenta i ON
        p.id_imprenta = i.id
        LEFT JOIN autorizacion aut ON
        aut.id_pedido = p.id  
SQL;

    $condicion = <<<SQL
        WHERE
        p.borrado = 0 AND e.borrado = 0 AND tm.borrado = 0 AND i.borrado = 0
SQL;

    /**SIN Filtros*/

    $counter_query  = "SELECT COUNT(p.id) AS total {$from}";

    $recordsTotal   =  $cnx->consulta(Conexiones::SELECT, $counter_query . $condicion, $sql_params )[0]['total'];

        //Los campos que admiten en el search (buscar) para concatenar al filtrado de la consulta
        if(!empty($params['search'])){
            $indice = 0;
            $search[]   = <<<SQL
            (p.id like :search{$indice} OR e.razon_social like :search{$indice} OR tm.tipo like :search{$indice} OR i.razon_social like :search{$indice} OR p.fecha_solicitud like :search{$indice} OR p.cantidad like :search{$indice} OR aut.cantidad_autorizada like :search{$indice}) 
SQL;
            $texto = $params['search'];
            $sql_params[":search{$indice}"] = "%{$texto}%";

            $buscar =  implode(' AND ', $search);
            $condicion .= empty($condicion) ? "{$buscar}" : " AND {$buscar} ";

        }

        /**Orden de las columnas */
        $orderna = [];
        foreach ($params['order'] as $i => $val) {
            $orderna[]  = "{$val['campo']} {$val['dir']}";
        }

        $order .= implode(',', $orderna);

        $limit = (isset($params['lenght']) && isset($params['start']) && $params['lenght'] != '')
            ? " LIMIT  {$params['start']}, {$params['lenght']}" : ' ';

        $recordsFiltered= $cnx->consulta(Conexiones::SELECT, $counter_query.$condicion, $sql_params)[0]['total'];

        $order .= (($order =='') ? '' : ', ').'p.fecha_solicitud desc';

        $order = ' ORDER BY '.$order;

        $lista = $cnx->consulta(Conexiones::SELECT,  $sql .$from.$condicion.$order.$limit,$sql_params);

        if ($lista) {
            foreach ($lista as $key => &$array) {
                foreach ($array as $k => &$v) {
                    if ($k == 'estado') {
                        foreach (Pedido_marbete::$TIPO_PEDIDOS as $key => $value) {
                            if($value['id'] == $v){
                                $array[$k] = $value['nombre'];
                            }

                        }
                    }
                }
            }
        }

        return ($lista) ? $lista : [];
    }

    public function alta(){
        if(!$this->validar()){
            return false;
        }
        $cnx = new Conexiones();
        $sql_params = [
            ':id_empresa'   => $this->id_empresa,
            ':id_imprenta'   => $this->id_imprenta,
            ':id_tipo_marbete' => $this->id_tipo_marbete,
            ':cantidad'        => $this->cantidad
        ];
         if($this->fecha_solicitud instanceof \DateTime){
             $sql_params[':fecha_solicitud'] = $this->fecha_solicitud->format('Y-m-d');
        }

        $sql = 'INSERT INTO pedido_marbete (id_empresa, id_imprenta, id_tipo_marbete,fecha_solicitud, cantidad) VALUES (:id_empresa,:id_imprenta, :id_tipo_marbete, :fecha_solicitud, :cantidad)';
        $res = $cnx->consulta(Conexiones::INSERT, $sql, $sql_params);
        if($res !== false){
            self::loguear_estado($res, self::PEDIDO_SOLICITADO);
            $this->id = $res;
            $datos = (array) $this;
            $datos['modelo'] = 'Pedido_marbete';
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
            'fecha_solicitud'  => 'fecha_solicitud = :fecha_solicitud',
            'id_empresa'       => 'id_empresa = :id_empresa',
            'id_imprenta'       => 'id_imprenta = :id_imprenta',
            'id_tipo_marbete'  => 'id_tipo_marbete = :id_tipo_marbete',
            'cantidad'         => 'cantidad = :cantidad',
            'estado'           => 'estado = :estado'
        ];
        $sql_params = [
            ':cantidad'         => $this->cantidad,
            ':id_empresa'       => $this->id_empresa,
            ':id_imprenta'      => $this->id_imprenta,
            ':id_tipo_marbete'  => $this->id_tipo_marbete,
            ':estado'           => $this->estado,
            ':id'               => $this->id


        ];

          if($this->fecha_solicitud instanceof \DateTime){
            $sql_params[':fecha_solicitud'] = $this->fecha_solicitud->format('Y-m-d');
            }

        $sql = 'UPDATE pedido_marbete SET '.implode(',', $campos).' WHERE id = :id';
        $res = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);
        if($res !== false){
            $datos = (array) $this;
            $datos['modelo'] = 'Pedido_marbete';
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
        $sql    = 'UPDATE pedido_marbete SET borrado = 1 WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'Pedido_marbete';
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
        $campos = (array)$this;
        $reglas     = [
            'id'      => ['numeric'],
            'id_empresa'        => ['required','numeric'],
            'id_imprenta'       => ['required','numeric'],
            'id_tipo_marbete'   => ['required','numeric'],
            'fecha_solicitud'   => ['required', 'fecha'],
            'cantidad'          => ['required','max_length(7)', 'numeric',
            'rango_valido' => function($input) {
                return ((int)$input > 0);
            }],
            'borrado'           => ['numeric']
        ];
        if(!is_null($campos['fecha_autorizacion']) && !is_null($campos['cantidad_autorizada'])){
            $reglas     += [
                'fecha_autorizacion'  => ['required','fecha','despuesDe(:fecha_solicitud)','maxDate' => function($value){
                                            if (!empty($value)) {
                                                  $hoy =  \DateTime::createFromFormat('d/m/Y H:i:s', gmdate('d/m/Y').'0:00:00');
                                                  return $value <= $hoy;
                                            }
                                        }],
                'cantidad_autorizada' => ['required', 'numeric', 'rango_valido_bis' => function($input) {
                return ((int)$input > 0);
                },
                'menor_que(:cantidad)' =>
                function($input,$cantidad){
                    if(empty($input)) return false;
                    if($input > $cantidad){
                        return false;
                    }
                    return true;
                }]
             ];
        }

        if(!empty($campos['observaciones'])){
            $reglas     += [
                'observaciones'  => ['required','texto']
            ];
        }

        $nombre= [
            'id_empresa'            => 'Empresa',
            'id_imprenta'           => 'Imprenta',
            'id_tipo_marbete'       => 'Tipo de Marbete',
            'fecha_solicitud'       => 'Fecha de solicitud',
            'cantidad'              => 'cantidad',
            'fecha_autorizacion'    => 'Fecha de Autorizacion',
            'cantidad_autorizada'   => 'Cantidad Autorizada'

        ];

        $validator  = Validator::validate($campos, $reglas, $nombre);

        $validator->customErrors([
            'rango_valido'          => 'La cantidad solicitada ingresada debe ser mayor a cero.',
            'rango_valido_bis'      => 'La cantidad autorizada ingresada debe ser mayor a cero.',
            'menor_que'             => 'La cantidad autorizada debe ser menor o igual que la cantidad solicitada.',
            'maxDate'               => 'La fecha de autorización NO puede ser posterior a la fecha actual.'
        ]);

        if ($validator->isSuccess()) {
            return true;
        }
        $this->errores = $validator->getErrors();
        return false;

    }


    static public function arrayToObject($res = []) {
        $campos = [
        'id'                  => 'int',
        'id_empresa'          => 'int',
        'id_imprenta'         => 'int',
        'id_tipo_marbete'     => 'int',
        'fecha_solicitud'     => 'datetime',
        'cantidad'            => 'int',
        'fecha_autorizacion'  => 'datetime',
        'cantidad_autorizada' => 'int',
        'estado'              => 'int',
        'observaciones'       => 'string'
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

    static public function lista_pedidos() {
        $aux=[];
        $mbd = new Conexiones;
        $resultado = $mbd->consulta(Conexiones::SELECT,
        "SELECT
        id, id_empresa, id_imprenta, id_tipo_marbete, fecha_solicitud, cantidad, estado, observaciones, borrado
        FROM pedido_marbete
        WHERE borrado = 0
        ORDER BY id ASC");
        if(empty($resultado)) { return []; }
        foreach ($resultado as $value) {
            $aux[$value['id']] = $value;
        }
        return $aux;

    }

    public function autorizacion_alta(){
         if(!$this->validar()){
            return false;
        }
        $cnx = new Conexiones();
        $sql_params = [
            ':id_pedido'            => $this->id,
            ':cantidad_autorizada'  => $this->cantidad_autorizada
        ];
         if($this->fecha_autorizacion instanceof \DateTime){
             $sql_params[':fecha_autorizacion'] = $this->fecha_autorizacion->format('Y-m-d');
        }

        $sql = 'INSERT INTO autorizacion (id_pedido, fecha_autorizacion, cantidad_autorizada) VALUES (:id_pedido,:fecha_autorizacion,:cantidad_autorizada)';
        $res = $cnx->consulta(Conexiones::INSERT, $sql, $sql_params);
       
        if($res !== false){
            self::update_estado($this->id,$this->estado);
            self::armar_registro_marbete($this->id);
            $datos = (array) $this;
            $datos['modelo'] = 'Pedido_marbete';
            Logger::event('alta', $datos);
        }
        return $res;
    }

     public function rechazar(){
         if(!$this->validar()){
            return false;
        }
        $cnx = new Conexiones();

        $sql_params = [
            ':estado'           => $this->estado,
            ':observaciones'    => $this->observaciones,
            ':id'               => $this->id
        ];

        $sql = 'UPDATE pedido_marbete SET estado =:estado, observaciones=:observaciones WHERE id = :id';
        $res = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        if($res !== false){
            self::update_estado($this->id,$this->estado);
            $datos = (array) $this;
            $datos['modelo'] = 'Pedido_marbete';
            Logger::event('modificacion', $datos);
            return true;
        }
        return false;
    }

    public function update_estado($id=null, $estado=null){
        $cnx = new Conexiones();
        $sql_params = [
            ':estado'           => $estado,
            ':id'               => $id
        ];

        $sql = 'UPDATE pedido_marbete SET estado = :estado WHERE id = :id';
        $res = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        if($res !== false){
            self::loguear_estado($id,$estado);
            $datos = (array) $this;
            $datos['modelo'] = 'Pedido_marbete';
            Logger::event('modificacion', $datos);
            return true;
        }
        return false;
    }

    public function loguear_estado($id=null, $estado=null){
        if(!$this->validar()){
            return false;
        }
        $cnx = new Conexiones();
        $sql_params = [
            ':id_pedido'      => $id,
            ':estado'         => $estado,
        ];

        $sql = 'INSERT INTO log_estado_pedido (id_pedido_marbete, estado) VALUES (:id_pedido,:estado)';
        $res = $cnx->consulta(Conexiones::INSERT, $sql, $sql_params);
        if($res !== false){
           // $this->id = $res;
            $datos = (array) $this;
            $datos['modelo'] = 'Pedido_marbete';
            Logger::event('log_estado_pedido', $datos);
        }
        return $res;
    }

     public function firmar(){
         if(!$this->validar()){
            return false;
        }
        $cnx = new Conexiones();

        $sql_params = [
            ':estado'           => $this->estado,
            ':id'               => $this->id
        ];

        $sql = 'UPDATE pedido_marbete SET estado =:estado WHERE id = :id';
        $res = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        if($res !== false){
            self::update_estado($this->id,$this->estado);
            $datos = (array) $this;
            $datos['modelo'] = 'Pedido_marbete';
            Logger::event('modificacion', $datos);
            return true;
        }
        return false;
    }

    ///DATOS ENTRANTES DE EJEMPLO
    /*$rango = ['A','F'];
    $max_dig = 999999;
    $cantidad_pedida = 100;
    #$cantidad_pedida = 34344;
    #$ultimo_asignado = ['AZZ','999959'];
    #$ultimo_asignado = ['FZZ','999991'];
    $ultimo_asignado = ['ABZ','999991'];*/
    /////////////////////////////////////////

    static public function armar_registro_marbete($id_pedido = null){
        
        $pedido = Pedido_marbete::obtener($id_pedido);
        $datos_pedido = self::traer_datos_pedido_marbete($id_pedido);
        $imprenta   = \App\Modelo\Imprenta::obtener($pedido->id_imprenta);
        $rango = json_decode($imprenta->rango);
        $max_dig = static::MAXIMO_DIGITO_PERMITIDO;
        $cantidad_pedida = $datos_pedido['cantidad_autorizada']-1;    
        $algun_pedido = self::buscar_pedido_empresa($pedido->id_empresa, $pedido->id_imprenta, $pedido->id_tipo_marbete);
        $registro = self::traer_ultima_asignacion_marbete($algun_pedido);
       
        $ultimo_asignado[0] = !empty($registro) ? $registro['letras_fin'] : null;
        $ultimo_asignado[1] = !empty($registro) ? $registro['digitos_fin'] : null;
   
        $flag_letras =false;
        if(!empty($ultimo_asignado[1])){
            if ($ultimo_asignado[1]+ 1 <= $max_dig){
                $digitosinicio = str_pad($ultimo_asignado[1]+ 1, 6, "0", STR_PAD_LEFT); 
                } else {
                    $digitosinicio = '000000';
                    $flag_letras  = true;
                }
            $codigo = self::evalua_codigo($ultimo_asignado[0],$rango,$flag_letras);
        }else{
            $codigo = $rango[0].$rango[0].$rango[0];
            $digitosinicio = '000000';
        }
       
        //echo $codigo.$digitosinicio."\n"; patron: letras (codigo) + digitos numéricos inicio
        $letrasinicio = $codigo;
        $flag_letras =false;
        
        if ($digitosinicio + $cantidad_pedida <= $max_dig){
                $digitosfin = str_pad($digitosinicio+$cantidad_pedida, 6, "0", STR_PAD_LEFT); 
        } else {
                $digitosfin = str_pad($digitosinicio + $cantidad_pedida - $max_dig, 6, "0", STR_PAD_LEFT);
                $flag_letras = true;
        } 
        
        $codigo = self::evalua_codigo($codigo,$rango,$flag_letras);
        $letrasfin = $codigo;
        //echo $codigo.$digitosfin."\n"; // dejar esto para saber como es el patron letras (codigo) + digitos numericos fin
        self::insertar_asignacion_marbete($pedido->id, $letrasinicio, $letrasfin, $digitosinicio, $digitosfin);

    }

    static public function evalua_codigo($codigo,$rango,$flag_letras){
        $codigo = ($flag_letras) ? Pedido_marbete::incremento_letra( $codigo,3,$rango,$flag_letras ) : $codigo;
        $codigo = ($flag_letras) ? Pedido_marbete::incremento_letra( $codigo,2,$rango,$flag_letras): $codigo;
        $codigo = ($flag_letras) ? Pedido_marbete::incremento_letra( $codigo,1,$rango,$flag_letras) : $codigo;
        return $codigo;
    }

    static public function incremento_letra($codigo,$posicion,$rango,&$flag){
            $rango_aplicado = ($posicion == 1) ? $rango : [$rango[0],'Z'];
            $caracteres = str_split($codigo);
            $caracter = ord($caracteres[$posicion-1]);
            if($caracter+1 > ord($rango_aplicado[1])){
                //Si la primera posición sobrepasa su limite definido se lanza un ERROR. 
                if($posicion ==1)
                    trigger_error('Se excedio la maxima definición', E_USER_ERROR);
                $flag=true;
                $caracteres[$posicion-1] = $rango_aplicado[0];
            } else{
                $caracteres[$posicion-1] = chr($caracter+1);
                $flag = false;
            }
            return implode('',$caracteres);
    }

    static public function insertar_asignacion_marbete($pedido_id, $letras_inicio, $letras_fin,$digitos_inicio,$digitos_fin){
        $cnx = new Conexiones();
        $sql_params = [
            ':id_pedido'      => $pedido_id,
            ':letras_inicio'  => $letras_inicio,
            ':letras_fin'     => $letras_fin,
            ':digitos_inicio' => $digitos_inicio,
            ':digitos_fin'    => $digitos_fin    
        ];
      
        $sql = 'INSERT INTO asignacion_marbete (id_pedido,letras_inicio,letras_fin,digitos_inicio,digitos_fin) VALUES (:id_pedido,:letras_inicio,:letras_fin, :digitos_inicio,:digitos_fin)';
        $res = $cnx->consulta(Conexiones::INSERT, $sql, $sql_params);
       
        if($res !== false){
            //$datos = (array) $this;
            $datos['modelo'] = 'Pedido_marbete';
            Logger::event('alta', $datos);
        }
        return $res;
    }
    
    static public function traer_datos_pedido_marbete($id_pedido = null){
         if(!is_numeric($id_pedido)) {
            return [];
        }

         $sql_params = [
            ':id_pedido'    => $id_pedido
        ];
        $sql    = <<<SQL
        SELECT codigo_cnrt, t_m.id as id_marbete, im.id as id_imprenta, emp.id as id_empresa, a.cantidad_autorizada
            FROM empresa emp
            INNER JOIN pedido_marbete p_m ON
            p_m.id_empresa = emp.id 
            INNER JOIN tipo_marbete t_m ON
            t_m.id = p_m.id_tipo_marbete
            INNER JOIN imprenta im ON
            im.id = p_m.id_imprenta
            INNER JOIN autorizacion a ON
            a.id_pedido = p_m.id
            WHERE p_m.id = :id_pedido;
          
SQL;

        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(!empty($res)){
            return $res[0];
        }

        return [];

    }

    static public function buscar_pedido_empresa($id_empresa,$id_imprenta , $id_marbete ){
        $sql_params = [
            ':id_empresa'       => $id_empresa,
            ':id_imprenta'      => $id_imprenta,
            ':id_tipo_marbete'  => $id_marbete,
            ':autorizado'       => static::PEDIDO_AUTORIZADO,
            ':firmado'          => static::PEDIDO_FIRMADO

        ];
       
        $sql    = <<<SQL
            SELECT MAX(id) as id
            FROM pedido_marbete
            WHERE id_empresa = :id_empresa AND id_imprenta = :id_imprenta AND id_tipo_marbete = :id_tipo_marbete
            AND (estado = :autorizado OR estado = :firmado);

SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(!empty($res)){
            return $res[0]['id'];
        }
        return (int)'0';
    }

    static public function traer_ultima_asignacion_marbete($id_pedido=null){ 
         if(empty($id_pedido)){
            return [];
        }

        $pedido         = Pedido_marbete::obtener($id_pedido);
        $empresa        = \App\Modelo\Empresa::obtener($pedido->id_empresa);
        $imprenta       = \App\Modelo\Imprenta::obtener($pedido->id_imprenta);
        $tipo_marbete   = \App\Modelo\Tipo_marbete::obtener($pedido->id_tipo_marbete);
       
        $sql_params = [
            ':id_empresa'          => $empresa->id,
            ':id_imprenta'         => $imprenta->id,
            'id_tipo_marbete'      => $tipo_marbete->id  
        ];

        $sql    = <<<SQL
            SELECT a_m.id, id_pedido, letras_inicio, letras_fin, digitos_inicio, digitos_fin
            FROM asignacion_marbete  a_m
            INNER JOIN pedido_marbete p_m ON 
            p_m.id = a_m.id_pedido
            WHERE p_m.id_empresa = :id_empresa
            AND p_m.id_tipo_marbete = :id_tipo_marbete 
            AND p_m.id_imprenta = :id_imprenta       
            ORDER BY a_m.id DESC
            LIMIT 1;
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(!empty($res)){
            return $res[0];
        }
        return [];

    }

    /* Por el momento no se usa
     * zero_fill
     *
     * Rellena con ceros a la izquierda
     *
     * @param $valor valor a rellenar
     * @param $long longitud total del valor
     * @return valor rellenado
     */

    static public function zero_fill($valor, $long = 0){
        return str_pad($valor, $long, '0', STR_PAD_LEFT);
    }

    static public function obtener_asignacion_marbete_de_pedido($id_pedido = null){
          if(!is_numeric($id_pedido)) {
            return [];
        }

         $sql_params = [
            ':id_pedido'    => $id_pedido
        ];
        $sql    = <<<SQL
        SELECT letras_inicio, letras_fin, digitos_inicio, digitos_fin
            FROM asignacion_marbete a_m
            INNER JOIN pedido_marbete p_m ON
            p_m.id = a_m.id_pedido
            WHERE p_m.id = :id_pedido;
          
SQL;

        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(!empty($res)){
            return $res[0];
        }

        return [];

    }

    public function anular(){
         if(!$this->validar()){
            return false;
        }
        $cnx = new Conexiones();

        $sql_params = [
            ':estado'           => static::PEDIDO_ANULADO,
            ':id'               => $this->id
        ];

        $sql = 'UPDATE pedido_marbete SET estado =:estado WHERE id = :id';
        $res = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        if($res !== false){
            self::update_estado($this->id,$this->estado);
            $datos = (array) $this;
            $datos['modelo'] = 'Pedido_marbete';
            Logger::event('modificacion', $datos);
            return true;
        }
        return false;
    }
    
    public function imprimir_entregar(){
         if(!$this->validar()){
            return false;
        }
        $cnx = new Conexiones();

        $sql_params = [
            ':estado'           => static::PEDIDO_IMPRESO_ENTREGADO,
            ':id'               => $this->id
        ];

        $sql = 'UPDATE pedido_marbete SET estado =:estado WHERE id = :id';
        $res = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        if($res !== false){
            self::update_estado($this->id,$this->estado);
            $datos = (array) $this;
            $datos['modelo'] = 'Pedido_marbete';
            Logger::event('modificacion', $datos);
            return true;
        }
        return false;
    }

 }   



