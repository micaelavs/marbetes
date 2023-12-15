<?php
namespace App\Modelo;
use FMT\Roles;
use FMT\Usuarios;

class AppRoles extends Roles {
	const PADRE_BASE				= 0;
	const ROL_ADMINISTRACION		= 1;
	const CARGA						= 2;
	const AUTORIZANTE				= 3;
	const FIRMANTE					= 4;



	static $rol;
	static $permisos	= [
		self::PADRE_BASE => [
			'nombre'	=> 'Padre',
			'inicio'	=> ['control' => 'error','accion' => 'index'],
			'atributos' => [
				'campos' => [],
			],
			'permisos'	=> [
				'Error' => [
					'index'				=> true
				],
			]
		],

		self::ROL_ADMINISTRACION => [
			'nombre'	=> 'Administrador del sistema',
			'padre'		=> self::PADRE_BASE,
			'inicio'	=> ['control' => 'usuarios','accion' => 'index'],
			'roles_permitidos' => [
				self::ROL_ADMINISTRACION,
				self::CARGA,
				self::AUTORIZANTE,
				self::FIRMANTE,
			],
			'atributos' => [
				self::ROL_ADMINISTRACION,
				self::FIRMANTE,
			],
			'permisos'	=> [
				'Usuarios' => [
					'index'		=> true,
					'alta'		=> true,
					'modificar'	=> true,
					'baja'		=> true,
				],
				'Imprentas' => [
					'index'		=> true,
					'alta'		=> true,
					'modificar'	=> true,
					'baja'		=> true,
					'activar'	=>true,

				],
				'Camaras' => [
					'index'		=> true,
					'alta'		=> true,
					'modificar'	=> true,
					'baja'		=> true,
					'activar'	=> true

				],

				'Empresas' => [
					'index'		=> true,
					'alta'		=> true,
					'modificar'	=> true,
					'baja'		=> true,
					'activar'	=> true,
					'buscarEmpresa' => true,
					'ajax_empresas' => true
				],
				'Tipo_marbetes' => [
					'index'		=> true,
					'alta'		=> true,
					'modificar'	=> true,
					'baja'		=> true,
					'activar'	=> true,
					'buscarEmpresa' => true

				],
				'Pedidos_marbete' => [
					'index'			=> true,
					'ajax_pedidos'  => true,
					'exportar_excel'=> true
				],
				'Auditorias' => [
					'index'			 		 => true,
					'ajax_auditoria' 		 => true,
					'exportar_pedidos_csv'	 => true
				]
			]
		],
		self::CARGA => [
			'nombre'	=> 'Carga',
			'padre'		=> self::PADRE_BASE,
			'inicio'	=>	['control' => 'pedidos_marbete', 'accion' => 'index'],
			'atributos' => [],
			'permisos'	=> [
				'Imprentas' => [
					'index'		=> true,
					'alta'		=> true,
					'modificar'	=> true,
					'baja'		=> true,
					'activar'	=> true,

				],
				'Empresas' => [
					'index'		=> true,
					'alta'		=> true,
					'modificar'	=> true,
					'baja'		=> true,
					'activar'	=> true,
					'buscarEmpresa' => true,
					'ajax_empresas' => true
				],
				'Tipo_marbetes' => [
					'index'		=> true,
					'alta'		=> true,
					'modificar'	=> true,
					'baja'		=> true,
					'activar'	=> true,
				],
				'Pedidos_marbete' => [
					'index'			=> true,
					'alta'			=> true,
					'modificar'		=> true,
					'baja'			=> true,
					'buscarEmpresa' => true,
					'autorizacion_alta'	=> true,
					'rechazar' 			=> true,
					'ajax_pedidos'   => true,
					'exportar_excel' => true,
					'imprimir_entregar' => true,
					'pedido_autorizado' => true
				],
				'Auditorias' => [
					'index'			 		 => true,
					'ajax_auditoria' 		 => true,
					'exportar_pedidos_csv'	 => true
				]
			]
		],
		self::AUTORIZANTE => [
			'nombre'	=> 'Autorizante',
			'padre'		=> self::PADRE_BASE,
			'inicio'	=> ['control' => 'pedidos_marbete', 'accion' => 'index'],
			'atributos' => [],
			'permisos'	=> [
				'Imprentas' => [
					'index'		=> true,
					'alta'		=> true,
					'modificar'	=> true,
					'baja'		=> true,
					'activar'	=> true,


				],
				'Empresas' => [
					'index'		=> true,
					'alta'		=> true,
					'modificar'	=> true,
					'baja'		=> true,
					'activar'	=> true,
					'buscarEmpresa' => true,
					'ajax_empresas' => true
				],
				'Tipo_marbetes' => [
					'index'		=> true,
					'alta'		=> true,
					'modificar'	=> true,
					'baja'		=> true,
					'activar'	=> true,
				],
				'Pedidos_marbete' => [
					'index'			=> true,
					'alta'			=> true,
					'modificar'		=> true,
					'baja'			=> true,
					'buscarEmpresa' => true,
					'autorizacion_alta'	=> true,
					'rechazar' 			=> true,
					'ajax_pedidos'   => true,
					'exportar_excel' => true,
					'imprimir_entregar' => true,
					'pedido_autorizado' => true
				],
				'Auditorias' => [
					'index'			 		 => true,
					'ajax_auditoria' 		 => true,
					'exportar_pedidos_csv'	 => true
				]
			]
		],
		self::FIRMANTE => [
			'nombre'	=> 'Firmante',
			'padre'		=> self::PADRE_BASE,
			'inicio'	=> ['control' => 'pedidos_marbete', 'accion' => 'index'],
			'atributos' => [],
			'permisos'	=> [
				'Pedidos_marbete' => [
					'index'				=> true,
					'pedido_autorizado' => true,
					'firmar'			=> true,
					'ajax_pedidos'   => true,
					'exportar_excel' => true,
					'anular'		 => true
				],
				'Auditorias' => [
					'index'			 		 => true,
					'ajax_auditoria' 		 => true,
					'exportar_pedidos_csv'	 => true
				]
			]
		],
	];

	public static function sin_permisos($accion){
		$vista = include (VISTAS_PATH.'/widgets/acceso_denegado_accion.php');
		return $vista;
	}

    public static function obtener_rol() {
    	return static::$rol;
    }

	public static function obtener_inicio() {
    	static::$rol= Usuarios::$usuarioLogueado['permiso'];
		static::$rol= (is_null(static::$rol))? self::PADRE_BASE : static::$rol ;
    	$inicio		= static::$permisos[static::$rol]['inicio'];
    	return $inicio;
    }

    public static function obtener_nombre_rol() {
    	$nombre	= static::$permisos[static::$rol]['nombre'];
    	return $nombre;
    }

 	public static function obtener_manual() {
    	$manual	= static::$permisos[static::$rol]['manual'];
    	return $manual;
    }

 	public static function obtener_atributos_visibles() {
		$atributo_visible	= static::$permisos[static::$rol]['atributos_visibles'];
		return $atributo_visible;
    }

    public static function obtener_atributos_select() {
		$atributos_select	= static::$permisos[static::$rol]['atributos_select'];
		return $atributos_select;
    }


/**
 * @param string $cont			- Controlador
 * @param string $accion		- Accion que se aplica sobre el atributo
 * @param string $atributo		- Tipo de atributo
 * @param string $id_atributo	- Indice del atributo
*/
    public static function puede_atributo($cont, $accion, $atributo, $id_atributo) {
		$flag = true;
		$rol = static::$rol;
	    while ($flag) {
		    if (isset(static::$permisos[$rol]['atributos'][$atributo][$id_atributo])) {
		        if(isset(static::$permisos[$rol]['atributos'][$atributo][$id_atributo][$cont][$accion])) {
		            $puede = static::$permisos[$rol]['atributos'][$atributo][$id_atributo][$cont][$accion];
		            $flag = false;
		        }
		    }

		    if ($flag && isset(static::$permisos[$rol]['padre'])) {
                $rol = static::$permisos[$rol]['padre'];
            } else {
                $flag = false;
            }
        }
	    if (!isset($puede)) {
	        $puede = static::puede($cont, $accion);
	    }
	    return $puede;
	}

    public static function puede($cont, $accion) {
		$rol	=  Usuarios::$usuarioLogueado['permiso'];
		if($rol) {
			$puede	= parent::puede($cont, $accion);
		} else {
			$rol	= self::PADRE_BASE;
			$puede	= false;
            if (isset(static::$permisos[$rol]['permisos'][$cont][$accion])) {
                $puede	= static::$permisos[$rol]['permisos'][$cont][$accion];
			}
		}
		return $puede;
	}

/**
 * Se usa para consultar si un usuario logueado tiene permisos sobre el rol de otro.
 *
 * @param int $rol_externo El rol de un usuario distinto al logueado
 * @return boolean
*/
	public static function tiene_permiso_sobre($rol_externo=null){
		return in_array($rol_externo, (array)static::$permisos[static::$rol]['roles_permitidos']);
	}

	public static function obtener_listado() {
		$roles_permitidos	= static::$permisos[static::$rol]['roles_permitidos'];
		$permisos			= static::$permisos;
		foreach ($permisos as $key => $permiso) {
			if(!in_array( $key, $roles_permitidos )){
				unset($permisos[$key]);
			}
		}

		return $permisos;
	}
}
