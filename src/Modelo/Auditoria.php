<?php

namespace App\Modelo;

use App\Modelo\Modelo;
use App\Helper\Conexiones;
use App\helper\Conexiones as HelperConexiones;
use App\Modelo\Pedido_marbete;

class Auditoria extends Modelo
{
    /** @var int */
    public $id;
    /** @var int */
    public $id_empresa;
    /** @var int */
    public $id_imprenta;
    /** @var int */
    public $id_tipo_marbete;
    /** @var int */
    public $cantidad;
    /** @var int */
    public $estado = 1;
    /** @var Date */
    public $fecha_solicitud;
    /** @var Date */
    public $fecha_autorizacion;
    /** @var Date */
    public $fecha_operacion;
    /** @var int */
    public $cantidad_autorizada;
    /** @var int */
    public $borrado;
    /**@var int**/
    public $razon_social;
    /**@var int**/
    public $id_camara;

    static public function obtener($id = null)
    {
        if ($id === null) {
            return static::arrayToObject();
        }
        $sql_params = [
            ':id'   => $id,
        ];
        $campos = implode(',', [
            'pm.id_empresa',
            'pm.id_imprenta',
            'pm.id_tipo_marbete',
            'e.id_camara',
            'pm.estado',
            'lg.estado estado_log',
            'lg.fecha_operacion',
            'pm.fecha_solicitud',
            'aut.fecha_autorizacion',
            'pm.cantidad',
            'aut.cantidad_autorizada',
            'pm.borrado'
        ]);
        $sql    = <<<SQL
            SELECT pm.id, {$campos}
            FROM pedido_marbete pm
            LEFT JOIN autorizacion aut ON pm.id = aut.id_pedido
            LEFT JOIN log_estado_pedido lg ON pm.id = lg.id_pedido_marbete
            LEFT JOIN empresa e ON pm.id_empresa = e.id
            WHERE pm.id = :id
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if (!empty($res)) {
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

    static public function listar()
    {
        
    }

    static public function listar_auditoria($params){
        $campos    = 'id, imprenta, empresa, camara, tipo_marbete, estado, fecha_solicitud, cantidad, cantidad_autorizada,numeracion_asignada';
        $sql_params = [];
        $where = [];
        $condicion = "AND pm.borrado = 0";
        
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
                'empresa'       => null,
                'fecha_desde'   => null,
                'fecha_hasta'   => null,
                'tipo_marbete'  => null,
                'imprenta'      => null,
                'camara'        =>null
            ]
        ];

        $params['filtros']  = array_merge($default_params['filtros'], $params['filtros']);
        $params = array_merge($default_params, $params);

         /*Filtros */
        if(!empty($params['filtros']['empresa'])){
            $where [] = "pm.id_empresa = :empresa";
            $sql_params[':empresa']    = $params['filtros']['empresa'];
        
        }

        if(!empty($params['filtros']['imprenta'])){
            $where [] = "pm.id_imprenta = :imprenta";
            $sql_params[':imprenta']    = $params['filtros']['imprenta'];
        
        }

        if(!empty($params['filtros']['camara'])){
            $where [] = "e.id_camara = :camara";
            $sql_params[':camara']    = $params['filtros']['camara'];
        
        }

        if(!empty($params['filtros']['fecha_desde'])){
            $where [] = "pm.fecha_solicitud >= :fecha_desde";
            $fecha = \DateTime::createFromFormat('d/m/Y', $params['filtros']['fecha_desde'])->format('Y-m-d');
            $sql_params[':fecha_desde']    = $fecha;

        }

        if(!empty($params['filtros']['fecha_hasta'])){
            $where [] = "pm.fecha_solicitud <= :fecha_hasta";
            $fecha = \DateTime::createFromFormat('d/m/Y', $params['filtros']['fecha_hasta'])->format('Y-m-d');
            $sql_params[':fecha_hasta']    = $fecha;

        }

        if(!empty($params['filtros']['tipo_marbete'])){
            $where [] = "pm.id_tipo_marbete = :tipo_marbete";
            $sql_params[':tipo_marbete']    = $params['filtros']['tipo_marbete'];
        
        }

        $condicion .= !empty($where) ? ' WHERE ' . \implode(' AND ',$where) : '';   

        if(!empty($params['search'])){
            $indice = 0;
            $search[]   = <<<SQL
            (pm.fecha_solicitud like :search{$indice} OR e.razon_social like :search{$indice} OR i.razon_social like :search{$indice} 
            OR c.nombre like :search{$indice} OR pm.cantidad like :search{$indice} OR aut.cantidad_autorizada like :search{$indice} or
            letras_inicio like :search{$indice} or letras_fin like :search{$indice} or digitos_inicio like :search{$indice} or digitos_fin like :search{$indice}) 
SQL;
            $texto = $params['search'];
            $sql_params[":search{$indice}"] = "%{$texto}%";

            $buscar =  implode(' AND ', $search);
            $condicion .= empty($condicion) ? "{$buscar}" : " AND {$buscar} ";

          
        }
       
        $consulta = <<<SQL
        SELECT pm.id,i.razon_social as imprenta,e.razon_social as empresa, c.nombre as camara, tp.tipo as tipo_marbete, pm.estado, pm.fecha_solicitud, pm.cantidad, aut.cantidad_autorizada,
        if(ISNULL(ab.digitos_fin),'-',CONCAT(e.codigo_cnrt," ",pm.id_tipo_marbete," ",ab.letras_inicio," ",ab.digitos_inicio,"-",e.codigo_cnrt," ",pm.id_tipo_marbete," ",ab.letras_fin," ",ab.digitos_fin)) as numeracion_asignada
        FROM pedido_marbete pm
        LEFT JOIN autorizacion aut ON pm.id = aut.id_pedido
        INNER JOIN empresa e ON pm.id_empresa = e.id
        INNER JOIN imprenta i ON pm.id_imprenta = i.id
        LEFT JOIN camara c ON e.id_camara = c.id
        INNER JOIN tipo_marbete tp ON pm.id_tipo_marbete = tp.id
        LEFT JOIN asignacion_marbete ab on pm.id = ab.id_pedido
        $condicion
        GROUP BY pm.id
SQL;    

        $data = self::listadoAjax($campos, $consulta, $params, $sql_params);
       
        if(!empty($data['data'])){
            foreach ($data['data'] as $key => $value) {
                $value->estado = Pedido_marbete::$TIPO_PEDIDOS[$value->estado]['nombre'];
            }
        }
      
        return $data;
     
    }

    public static function listar_auditoria_excel($params){
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
                'empresa'       => null,
                'fecha_desde'   => null,
                'fecha_hasta'   => null,
                'tipo_marbete'  => null,
                'imprenta'      => null,
                'camara'        => null
            ],
            
        ];

        $params['filtros']  = array_merge($default_params['filtros'], $params['filtros']);
        $params = array_merge($default_params, $params);

        $sql= <<<SQL
        SELECT pm.id,i.razon_social as imprenta,e.razon_social as empresa, c.nombre as camara, tp.tipo as tipo_marbete, pm.estado,  fecha_solicitud, pm.cantidad, aut.cantidad_autorizada,
        if(ISNULL(ab.digitos_fin),'-',CONCAT(e.codigo_cnrt," ",pm.id_tipo_marbete," ",ab.letras_inicio," ",ab.digitos_inicio,"-",e.codigo_cnrt," ",pm.id_tipo_marbete," ",ab.letras_fin," ",ab.digitos_fin)) as numeracion_asignada
SQL;
    $from = <<<SQL
        FROM pedido_marbete pm
        LEFT JOIN autorizacion aut ON pm.id = aut.id_pedido
        INNER JOIN empresa e ON pm.id_empresa = e.id
        INNER JOIN imprenta i ON pm.id_imprenta = i.id
        LEFT JOIN camara c ON e.id_camara = c.id
        INNER JOIN tipo_marbete tp ON pm.id_tipo_marbete = tp.id
        LEFT JOIN asignacion_marbete ab on pm.id = ab.id_pedido
SQL;

    $condicion = <<<SQL
        WHERE
        pm.borrado = 0
SQL;

$group = <<<SQL
        GROUP BY pm.id 

SQL;

    /**Filtros para la consulta 1*/
    if(!empty($params['filtros']['empresa'])){
        $condicion .= " AND pm.id_empresa = :empresa";
        $sql_params[':empresa']    = $params['filtros']['empresa'];
    }

    if(!empty($params['filtros']['imprenta'])){
        $condicion .= " AND pm.id_imprenta = :imprenta";
        $sql_params[':imprenta']   = $params['filtros']['imprenta'];
    }

    if(!empty($params['filtros']['camara'])){
        $condicion .= " AND e.id_camara = :camara";
        $sql_params[':camara']   = $params['filtros']['camara'];
    }

    if(!empty($params['filtros']['fecha_desde'])){
        $fecha = \DateTime::createFromFormat('d/m/Y', $params['filtros']['fecha_desde'])->format('Y-m-d');
        $condicion .=  " AND pm.fecha_solicitud >= :fecha_desde";
        $sql_params[':fecha_desde']   = $fecha;
    }

    if(!empty($params['filtros']['fecha_hasta'])){
        $fecha = \DateTime::createFromFormat('d/m/Y', $params['filtros']['fecha_hasta'])->format('Y-m-d');
        $condicion .=  " AND pm.fecha_solicitud <= :fecha_hasta";
        $sql_params[':fecha_hasta']   = $fecha;
    }

     if(!empty($params['filtros']['tipo_marbete'])){
        $condicion .= " AND pm.id_tipo_marbete = :tipo_marbete";
        $sql_params[':tipo_marbete']   = $params['filtros']['tipo_marbete'];
    }

    $counter_query  = "SELECT COUNT(pm.id) AS total {$from}";

    $recordsTotal   =  $cnx->consulta(Conexiones::SELECT, $counter_query . $condicion. $group, $sql_params )[0]['total'];

        //Los campos que admiten en el search (buscar) para concatenar al filtrado de la consulta
        if(!empty($params['search'])){
            $indice = 0;
            $search[]   = <<<SQL
            (pm.fecha_solicitud like :search{$indice} OR pm.cantidad like :search{$indice} OR aut.cantidad_autorizada like :search{$indice} or
            letras_inicio like :search{$indice} or letras_fin like :search{$indice} or digitos_inicio like :search{$indice} or digitos_fin like :search{$indice}) 
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

        $recordsFiltered= $cnx->consulta(Conexiones::SELECT, $counter_query.$condicion.$group, $sql_params)[0]['total'];

        $order .= (($order =='') ? '' : ', ').'fecha_solicitud desc';

        $order = ' ORDER BY '.$order;

        $lista = $cnx->consulta(Conexiones::SELECT,  $sql .$from.$condicion.$group.$order.$limit,$sql_params);

        if ($lista) {
            foreach ($lista as $key => &$array) {
                foreach ($array as $k => &$v) {
                    if ($k == 'estado') {
                        foreach (Pedido_marbete::$TIPO_PEDIDOS as $key => $value) {
                            if($value['id'] == $v){
                                $array[$k] = $value['nombre'];
                            }

                        }
                    }else{
                        if ($k == 'fecha_solicitud'){
                            $array[$k] = date('d/m/Y',strtotime($array['fecha_solicitud']));
                        }
                    }
                }
            }
        }

        return ($lista) ? $lista : [];
    }


    public function validar()
    {
        return true;
    }
    public function alta()
    {
        return false;
    }
    public function modificacion()
    {
        return false;
    }
    public function baja()
    {
        return false;
    }

    /**
     * Convierte un array en objetos.
     *
     * @param array $res
     * @return object
     */
    static public function arrayToObject($res = [])
    {
        $campos    = [
            'id'                  => 'int',
            'id_empresa'          => 'int',
            'id_imprenta'         => 'int',
            'id_tipo_marbete'     => 'int',
            'id_camara'           => 'int',
            'fecha_operacion'     => 'datetime',
            'fecha_solicitud'     => 'datetime',
            'fecha_autorizacion'  => 'datetime',
            'cantidad'            => 'int',
            'cantidad_autorizada' => 'int',
            'estado'              => 'int',
        ];
        $obj = new static;
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

    /**
     * Sirve para almacenar filtros adicionales que luego seran usados en las consultas realizadas por `::obtener` o `::listar`.
     * Si no se pasan parametros, funciona como Getter y limpia los filtros.
     *
     * @param array|string $campo	- Puede ser el string del filtro a usar o un array con su conjunto `clave => valor`
     * @param boolean $valor		- Valor del filtro
     * @return array|bool
     */
    static public function setFiltro($campo = false, $valor = false)
    {
    static $FILTRO    =  [];
        if ($campo === false && $valor === false) {
            $tmp    = $FILTRO;
            $FILTRO    = [];
            return $tmp;
        }
        if (!is_string($campo) && !is_array($campo)) {
            return false;
        }
        if (is_string($campo)) {
            $FILTRO[$campo]    = $valor;
        }
        if (is_array($campo)) {
            $FILTRO    = array_merge($FILTRO, $campo);
        }
    }

}
