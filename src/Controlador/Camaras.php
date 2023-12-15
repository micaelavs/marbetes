<?php
namespace App\Controlador;

use FMT\Controlador;
use App\Modelo;
use App\Helper;
use FMT\Configuracion;
use App\Helper\Vista;
use App\Helper\Util;

class Camaras extends Base {
	
	protected function accion_index() {	
		$camaras    = Modelo\Camara::listar();
		$vista = $this->vista;
		(new Vista($this->vista_default,compact('vista', 'camaras')))->pre_render();
	}

	protected function accion_alta(){
		$camara = Modelo\Camara::obtener($this->request->query('id'));
		
		if($this->request->post('boton_camara') == 'alta') {
			$camara->nombre = !empty($this->request->post('nombre')) ? $this->request->post('nombre') : null;
			$nombreCargado = $camara->nombre;
			$camara->descripcion = !empty($this->request->post('descripcion')) ? $this->request->post('descripcion') : null;

			if($camara->validar()){
				if($camara->alta()){
					$this->mensajeria->agregar(
					"La Camara <strong>{$camara->nombre}</strong> fue cargada correctamente",
			\FMT\Mensajeria::TIPO_AVISO,
			$this->clase);
					$redirect = Vista::get_url("index.php/Camaras/index");
					$this->redirect($redirect);	
				}else{
					$this->mensajeria->agregar(
					"No se pudo cargar la Cámara <strong>{$camara->nombre}</strong>",
			\FMT\Mensajeria::TIPO_ERROR,
			$this->clase);
				}
			} else {
				foreach ($camara->errores as $text) {
					$this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);

				}

				if(Modelo\Camara::$FLAG){ 
					$camaraCargada =  Modelo\Camara::obtenerPorNombre($nombreCargado);
					$redirect =Vista::get_url("index.php/camaras/activar/{$camaraCargada->id}");
					$this->redirect($redirect);
			
				}
			}

		}	
		
		$vista = $this->vista;
		(new Vista($this->vista_default,compact('vista','camara')))->pre_render();
	}

	protected function accion_modificar() {
		$camara		= Modelo\Camara::obtener($this->request->query('id'));
		if($this->request->post('boton_camara') == 'modificacion') {
			$camara->nombre = !empty($this->request->post('nombre')) ? $this->request->post('nombre') : $camara->nombre;
			$camara->descripcion	= !empty($this->request->post('descripcion')) ? $this->request->post('descripcion') : $camara->descripcion;
			if($camara->validar()){
				if($camara->modificacion()){
						$this->mensajeria->agregar(
							"La Cámara <strong>{$camara->nombre}</strong> ha sido modificada correctamente",
							\FMT\Mensajeria::TIPO_AVISO,
						$this->clase);
						$redirect = Vista::get_url("index.php/Camaras/index");
						$this->redirect($redirect);	
				}else{
					$this->mensajeria->agregar(
						"No se pudo modificar la Cámara <strong>{$camara->nombre}</strong>.",
						\FMT\Mensajeria::TIPO_ERROR,
					$this->clase);
				}
			} else {
				foreach ((array)$camara->errores as $text) {
					$this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
				}
			}
		}

		$vista = $this->vista;
		(new Vista($this->vista_default,compact('vista','camara')))->pre_render();

	}
		
	protected function accion_baja() {
	 	$camara					= Modelo\Camara::obtener($this->request->query('id'));
		if (!empty($camara->id)){
			if($this->request->post('confirmar')){
				if($camara->id){
					if ($camara->baja()){
							$this->mensajeria->agregar(
							"La Cámara <strong>{$camara->nombre}</strong> ha sido eliminada correctamente.",
							\FMT\Mensajeria::TIPO_AVISO,
							$this->clase);
					} else {
						$this->mensajeria->agregar(
						"No se ha podido eliminar la Cámara <strong>{$camara->nombre}</strong>.",
						\FMT\Mensajeria::TIPO_ERROR,
						$this->clase);
					}
					$redirect =Vista::get_url("index.php/camaras/index/");
					$this->redirect($redirect);
				}
			} 
		}

		$vista = $this->vista;
		(new Helper\Vista($this->vista_default,compact('vista', 'camara')))->pre_render();
	}

	protected function accion_activar() {
	 	$camara	= Modelo\Camara::obtener($this->request->query('id'));
	 	if (!empty($camara->id)){
			if($this->request->post('confirmar')){
				if($camara->id){
					if ($camara->activar()){
							$this->mensajeria->agregar(
							" La Cámara <strong>{$camara->nombre}</strong> se ha reactivada correctamente.",
							\FMT\Mensajeria::TIPO_AVISO,
							$this->clase);
					} else {
						$this->mensajeria->agregar(
						"No se ha podido reactivar la Cámara <strong>{$camara->nombre}</strong>.",
						\FMT\Mensajeria::TIPO_ERROR,
						$this->clase);
					}
					$redirect =Vista::get_url("index.php/camaras/index/");
					$this->redirect($redirect);
				}
			} 
		}

		$vista = $this->vista;
		(new Helper\Vista($this->vista_default,compact('vista', 'camara')))->pre_render();
	}

}

