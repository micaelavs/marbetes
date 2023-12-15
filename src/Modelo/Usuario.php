<?php
    namespace App\Modelo;

    use FMT\Logger;
    use FMT\Usuarios;
    use App\Helper\Validator;
    use App\Modelo\AppRoles;
    use App\Helper\Conexiones;

    /**
     * Class Usuario
     */
    class Usuario extends \FMT\Modelo {
    	/** @var int */
    	public $id = 0;
    	/** @var string */
    	public $username = null;
    	/** @var string */
    	public $nombre = null;
    	/** @var string */
    	public $apellido = null;
    	/** @var string */
    	public $email = null;
    	/** @var array[id, nombre] */
    	public $area = [];
    	/** @var integer */
    	public $rol_id;
    	public $rol_nombre;
    	public $metadata_nombre;
    	public $metadata = [];
    	protected static $cache = [];
    	/**
    	 * Regresa lista de usuarios
    	 * @return array
    	 */
    	public static function listar() {
    		$config				= \FMT\Configuracion::instancia();
    		$lista 				= Usuarios::getUsuarios();
    		$roles_permitidos	= \FMT\Helper\Arr::get(AppRoles::$rol,'roles_permitidos',[]);
    		foreach( $lista as &$usuario) {
    			$usuario		= (object) $usuario;
    			$usuario->rol_id= $usuario->permiso;
    			if (empty($roles_permitidos) || in_array($usuario->rol_id , $roles_permitidos)) {
    				$usuario->rol_nombre		= AppRoles::$permisos[$usuario->rol_id]['nombre'];
    				$meta						= Usuarios::getMetadata($usuario->idUsuario);
    				$usuario->metadata			= (!is_null($meta['metadata'])) ? json_decode($meta['metadata'],1) : [];
					$usuario->metadata_nombre	= $config['metadata_nombre'];
    			} else {
    				unset($usuario);
    			}
    		}
    		return $lista;
    	}

    	/**
    	 * @param int|string $user_id
    	 * @return Usuario
    	 */
    	public static function obtener($user_id = 0) {
    		$usuario	= new static();
    		if (!empty($user_id)) {
    			if (isset(static::$cache[$user_id])) {
    				$usuario	= static::$cache[$user_id];
    			} else {
    				$user	= Usuarios::getUsuario($user_id);
    				if (isset($user['idUsuario'])) {
    					$usuario		= new static();
    					$usuario->id	= (int)$user['idUsuario'];
    					$usuario->rol_id= Usuarios::getPermiso($usuario->id)['permiso'];
    					if (empty($usuario->rol_id)) {
    						$usuario->rol_id	= 0;
    					}
    					$usuario->rol_nombre	= AppRoles::$permisos[$usuario->rol_id]['nombre'];
    					$usuario->username		= isset($user['user']) ? (string)$user['user'] : null;
    					$usuario->nombre		= isset($user['nombre']) ? (string)$user['nombre'] : null;
    					$usuario->apellido		= isset($user['apellido']) ? (string)$user['apellido'] : null;
    					$usuario->email			= isset($user['email']) ? (string)$user['email'] : null;
    					$meta					= Usuarios::getMetadata($usuario->id);
    					$usuario->metadata		= (!is_null($meta['metadata'])) ? json_decode($meta['metadata'], 1) : [];
						static::$cache[$user_id]= $usuario;
    				}
    			}
    		}

    		return $usuario;
    	}

    	/**
    	 * @return Usuario
    	 */

    	public static function obtenerUsuarioLogueado() {
    		if (isset($_SESSION['iu']) && is_numeric($_SESSION['iu'])) {
    			return static::obtener($_SESSION['iu']);
    		} else {
    			return null;
    		}
    	}

    	public function fullName() {
    		return trim("{$this->nombre} {$this->apellido}");
    	}

    	public function modificacion() {
    		$rta	= false;
    		$operacion = 'M';
    		if($this->id) {
    			if (Usuarios::getPermiso($this->id) != $this->rol_id) {
    				Usuarios::eliminarMetadata($this->id);
    			}
    			Usuarios::setPermiso($this->id, $this->rol_id);

    			$metadata[] = ['area'=> $this->metadata];
    			Usuarios::setMetadata($this->id, json_encode($metadata));

    			$rta	= true;
    		}
    		if($rta){
    			static::log_usuarios($this, $operacion);
    		}
    		return $rta;
    	}

    	public function validar(){
    		$rules	= [
    			'username'   => ['required'],
    			'rol_id'     => ['required']
    		];
			$nombres	= [
				'username'	=> 'Nombre Usuario',
				'rol_id'	=> 'Rol'
			];
    		$validacion = Validator::validate((array)$this, $rules, $nombres);
    		$validacion->customErrors([
    			'required'      => 'Campo :attribute es requerido',
    			'requerido'     => 'Campo :attribute es requerido',
    		]);
    		if ($validacion->isSuccess() == true) {
    		  return true;
    		} else {
    			$this->errores	= $validacion->getErrors();
    			return false;
    		}
    	}

    	/**
    	 * Elimina el permiso del Usuario en la API y elimina la relación entre el usuario y el empleado.
    	 * @return bool
    	 */
    	public function baja() {
    		$rta	= false;
    		if (!empty($this->id)) {
    			Usuarios::eliminarPermiso($this->id);
    			Usuarios::eliminarMetadata($this->id);
    			$rta	= true;
    			static::log_usuarios($this,'B');
    		}
    		return $rta;
    	}

    	public function alta() {
    		$rta	= false;
    		$operacion = 'A';
    		if($this->id) {
    			if (Usuarios::getPermiso($this->id) != $this->rol_id) {
    				Usuarios::eliminarMetadata($this->id);
    			}
    			Usuarios::setPermiso($this->id, $this->rol_id);

    			$metadata[] = ['area'=> $this->metadata];
    			Usuarios::setMetadata($this->id, json_encode($metadata));

    			$rta	= true;
    		}
    		if($rta){
    			static::log_usuarios($this,$operacion);
    		}
    		return $rta;
    	}

    	/**
    	 * Regresa lista de usuarios
    	 * @return array
    	 */
    	public static function listar_administradores() {
    		$config				= \FMT\Configuracion::instancia();
    		$lista 				= Usuarios::getUsuarios();
    		foreach( $lista as $key => $usuario) {
    			$usuario		= (object) $usuario;
    			$usuario->rol_id= $usuario->permiso;
    			if ($usuario->rol_id == AppRoles::ROL_ADMINISTRACION) {
    				$usuario->rol_nombre = AppRoles::$permisos[$usuario->rol_id]['nombre'];
    			} else {
    				unset($lista[$key]);
    			}
    		}
    		return $lista;
    	}

    	/* public static function listar_solicitantes() {
    		$config				= \FMT\Configuracion::instancia();
    		$lista 				= Usuarios::getUsuarios();
    		foreach( $lista as $key => $usuario) {
    			$usuario		= (object) $usuario;
    			$usuario->rol_id= $usuario->permiso;
    			if ($usuario->rol_id == AppRoles::ROL_USUARIO) {
    				$usuario->rol_nombre = AppRoles::$permisos[$usuario->rol_id]['nombre'];
    			} else {
    				unset($lista[$key]);
    			}
    		}
    		return $lista;
    	} */

    	public static function log_usuarios($data, $operacion){
    		$metadata = $operacion != 'B' ? $data->metadata : $data->metadata[0]['area'];
    		$campos	= [
    			'id_usuario',
    			'fecha_operacion',
    			'tipo_operacion',
    			'id_usuario_panel',
    			'id_rol',
    			'username',
    			'metadata'
    		];

    		$sql_params	= [
    			':id_usuario'		=> static::obtenerUsuarioLogueado()->id,
    			':fecha_operacion'	=> date_format(date_create('now'), 'Y-m-d H:i:s'),
    			':tipo_operacion'	=> $operacion,
    			':id_usuario_panel'	=> $data->id,
    			':id_rol'			=> $data->rol_id,
    			':username'			=> $data->username,
    			':metadata'			=> json_encode([['area'=> $metadata]]),
    		];

    		$sql	= 'INSERT INTO usuarios('.implode(',', $campos).') VALUES (:'.implode(',:', $campos).')';
    		$res	= (new Conexiones('db_log'))->consulta(Conexiones::INSERT, $sql, $sql_params);
    		if($res !== false){
    			$data_log	= [
    				'id_usuario'		=> static::obtenerUsuarioLogueado()->id,
    				'fecha_operacion'	=> gmdate('Y-m-d'),
    				'tipo_operacion'	=> $operacion,
    				'id_usuario_panel'	=> $data->id,
    				'id_rol'			=> $data->rol_id,
    				'username'			=> $data->username,
    				'metadata'			=> json_encode([['area'=> $metadata]]),
    			];
    			$datos = $data_log;
    			$datos['modelo'] = 'Usuario';
    			Logger::event('alta', $datos);
    		}
    		return $res;
    	}
    }