<?php
namespace App\Controlador;

use App\Modelo;
use App\Helper;


class Tipomarbetes extends Base {
	
	protected function accion_index() {	
		$tipomarbete = Modelo\tipomarbete::listar();
		$vista = $this->vista;
		(new Vista($this->vista_default,compact('vista', 'tipomarbete')))->pre_render();
	}

	public function accion_alta(){

    	$tipomarbete = Modelo\tipomarbete::obtener($this->request->query('id'));

        

      	if($this->request->post('tipomarbete') == 'alta') {
      	    		$tipomarbete->tipo= !empty($temp=$this->request->post('tipo')) ?  $temp : null;
				$tipomarbete->descripcion= !empty($temp=$this->request->post('descripcion')) ?  $temp : null;
		
			if($tipomarbete->validar()){
				$tipomarbete->alta();
				$this->mensajeria->agregar(
				"AVISO:El Registro fué ingresado de forma exitosa.",\FMT\Mensajeria::TIPO_AVISO,$this->clase);
				$redirect =Vista::get_url("index.php/tipomarbete/index");
				$this->redirect($redirect);	
			}else {
					$err	= $tipomarbete->errores;
					foreach ($err as $text) {
						$this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
				  }
			}

		}


		 $vista = $this->vista;
		(new Vista($this->vista_default,compact('vista', 'tipomarbete' )))->pre_render();
	}
	 public function accion_modificacion(){
         $tipomarbete = Modelo\tipomarbete::obtener($this->request->query('id'));

         

		if($this->request->post('tipomarbete') == 'modificacion') {
					$tipomarbete->tipo= !empty($temp=$this->request->post('tipo')) ?  $temp : null;
				$tipomarbete->descripcion= !empty($temp=$this->request->post('descripcion')) ?  $temp : null;
		
			if($tipomarbete->validar()){
				$tipomarbete->modificacion();
				$this->mensajeria->agregar(
				"AVISO:El Registro fué modificado de forma exitosa.",\FMT\Mensajeria::TIPO_AVISO,$this->clase);
				$redirect =Vista::get_url("index.php/tipomarbete/index");
				$this->redirect($redirect);	
			}else {
					$err	= $tipomarbete->errores;
					foreach ($err as $text) {
						$this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
				    }
			      }
		}

		$vista = $this->vista;
		(new Vista($this->vista_default,compact('vista', 'tipomarbete' )))->pre_render();
	}
		
	protected function accion_baja() {
	 	$tipomarbete = Modelo\tipomarbete::obtener($this->request->query('id'));
	 	if($tipomarbete->id){
			if ($this->request->post('confirmar')) {
					if($tipomarbete->validar()){
						$tipomarbete->baja();
						$this->mensajeria->agregar('AVISO:El Registro se eliminó de forma exitosa.',\FMT\Mensajeria::TIPO_AVISO,$this->clase,'index');
						$redirect = Helper\Vista::get_url('index.php/tipomarbete/index');
						$this->redirect($redirect);
					}
			}
		} else {
			$redirect = Helper\Vista::get_url('index.php/tipomarbete/index');
			$this->redirect($redirect);
		}
		$vista = $this->vista;
		(new Helper\Vista($this->vista_default,compact('tipomarbete', 'vista')))->pre_render();
	}

}

