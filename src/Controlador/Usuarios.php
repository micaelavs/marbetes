<?php
namespace App\Controlador;
use App\Modelo;
use App\Helper;

class Usuarios extends Base {

	protected function accion_index() {
		$usuarios = Modelo\Usuario::listar();

		(new Helper\Vista($this->vista_default,['usuarios' => $usuarios,'vista' => $this->vista ]))
			->pre_render();
	}

	protected function accion_alta() {
		$usuario = Modelo\Usuario::obtener($this->request->post('username'));
		if( $this->request->post('buscar')) {
			$usuario->area = '';

			if(!$usuario->id) {
				$this->mensajeria->agregar("El nombre de usuario <strong>{$this->request->post('username')}</strong> no existe",\FMT\Mensajeria::TIPO_ERROR,$this->clase);
			}

			if( $usuario->rol_id != 0) {
				$this->mensajeria->agregar("El nombre de usuario <strong>{$usuario->username}</strong> ya tiene el rol  \"<strong>{$usuario->rol_nombre}</strong>\"",\FMT\Mensajeria::TIPO_AVISO,$this->clase);
				$redirect = Helper\Vista::get_url('index.php/usuarios/index');
				$this->redirect($redirect);
			}
		}

		if($this->request->post('guardar')) {
			$usuario->area		= '';
			$usuario->rol_id	= $this->request->post('rol');

			if ($usuario->validar()) {
				if ($usuario->alta()) {
					$this->mensajeria->agregar('AVISO: Se dió de alta de forma exitosa un nuevo usuario.',\FMT\Mensajeria::TIPO_AVISO,$this->clase,'index');
					$redirect = Helper\Vista::get_url('index.php/usuarios/index');
					$this->redirect($redirect);
				} else {
					$this->mensajeria->agregar('ERROR: Hubo un error en el alta.',\FMT\Mensajeria::TIPO_ERROR,$this->clase,'index');
					$redirect = Helper\Vista::get_url('index.php/usuarios/index');
					$this->redirect($redirect);
				}
			} else {
				foreach ($usuario->errores as $value) {
					$this->mensajeria->agregar($value ,\FMT\Mensajeria::TIPO_ERROR,$this->clase,$this->accion);
				}
			}
		}
		$usuario->rol_id = $this->request->post('rol');
		$roles = \App\Modelo\AppRoles::obtener_listado();
		$vista = $this->vista;
		$operacion = 'Alta';
		(new Helper\Vista($this->vista_default,compact('usuario', 'vista', 'roles', 'operacion')))->pre_render();
	}

	protected function accion_modificar(){
		$usuario = Modelo\Usuario::obtener($this->request->query('id'));
		if($usuario->id) {
			$usuario->area = '';
			$usuario->rol_id = ($temp = $this->request->post('rol')) ? $temp : $usuario->rol_id;
			if($this->request->post('guardar')) {
				$usuario->metadata = $this->request->post('areas');
				if ($usuario->validar()) {
					if ($usuario->modificacion()) {
						$this->mensajeria->agregar('AVISO: Se modificó de forma exitosa el usuario.',\FMT\Mensajeria::TIPO_AVISO,$this->clase,'index');
						$redirect = Helper\Vista::get_url('index.php/usuarios/index');
						$this->redirect($redirect);
					} else {
						$this->mensajeria->agregar('ERROR: Hubo un error en la modificación.',\FMT\Mensajeria::TIPO_ERROR,$this->clase,'index');
						$redirect = Helper\Vista::get_url('index.php/usuarios/index');
						$this->redirect($redirect);
					}
				} else {

					foreach ($usuario->errores as $value) {
						$this->mensajeria->agregar($value ,\FMT\Mensajeria::TIPO_ERROR,$this->clase,$this->accion);
					}
				}
			}
		} else {
			$this->mensajeria->agregar("El usuario que intenta modificar no existe.",\FMT\Mensajeria::TIPO_ERROR,$this->clase);
			$redirect = Helper\Vista::get_url('index.php/usuarios/index');
			$this->redirect($redirect);
		}

		$roles = \App\Modelo\AppRoles::obtener_listado();
		$vista = $this->vista;
		$operacion = 'Modificación';
		(new Helper\Vista(VISTAS_PATH.'/usuarios/alta.php',compact('usuario', 'vista', 'roles', 'operacion')))->pre_render();
	}

	protected function accion_baja() {
		$usuario = Modelo\Usuario::obtener($this->request->query('id'));
		if($usuario->id) {
			if ($this->request->post('confirmar')) {
				$res = $usuario->baja();
				if ($res) {
					$this->mensajeria->agregar('AVISO: Se eliminó un usuario de forma exitosa.',\FMT\Mensajeria::TIPO_AVISO,$this->clase,'index');
					$redirect = Helper\Vista::get_url('index.php/usuarios/index');
					$this->redirect($redirect);
				}
			}
		} else {
			$redirect = Helper\Vista::get_url('index.php/usuarios/index');
			$this->redirect($redirect);
		}

		$vista = $this->vista;
		(new Helper\Vista($this->vista_default,compact('usuario', 'vista')))->pre_render();
	}
}