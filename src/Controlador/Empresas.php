<?php
namespace App\Controlador;

use FMT\Controlador;
use App\Modelo;
use App\Helper;
use FMT\Configuracion;
use App\Helper\Vista;
use App\Helper\Util;


class Empresas extends Base {

	protected function accion_index() {
	   	$vista = $this->vista;
	   	 (new Vista($this->vista_default, compact('vista')))->pre_render();
	}


	protected function accion_alta(){

    	$empresa = Modelo\Empresa::obtener($this->request->query('id'));
    	$camaras	= Modelo\Camara::lista_camaras();
    	$camaras[99999] = ['id'=>99999, 'nombre' =>'Sin cámara', 'descripcion'=>'Sin descripción', 'borrado' =>0];

      	if($this->request->post('boton_empresa') == 'alta') {
      	    	$empresa->cuit= !empty($temp=$this->request->post('cuit')) ?  $temp : null;
				$empresa->razon_social= !empty($temp=$this->request->post('razon_social')) ?  $temp : null;
				$empresa->codigo_cnrt= !empty($temp=$this->request->post('codigo_cnrt')) ?  $temp : null;
				$empresa->id_camara= !empty($temp=$this->request->post('id_camara')) ?  $temp : null;
				$empresa->direccion = !empty($temp = $this->request->post('direccion')) ?  $temp : null;
				$empresa->nombre_apoderado = !empty($temp = $this->request->post('nombre_apoderado')) ?  $temp : null;
				$empresa->dni_apoderado = !empty($temp = $this->request->post('dni_apoderado')) ?  $temp : null;

			if($empresa->validar()){
				if($empresa->alta()){
					$this->mensajeria->agregar(
					"La Empresa <strong>{$empresa->razon_social}</strong> fue cargada correctamente",
			\FMT\Mensajeria::TIPO_AVISO,
			$this->clase);
					$redirect = Vista::get_url("index.php/Empresas/index");
					$this->redirect($redirect);
				}else{
					$this->mensajeria->agregar(
					"No se pudo cargar la Empresa <strong>{$empresa->razon_social}</strong>",
			\FMT\Mensajeria::TIPO_ERROR,
			$this->clase);
				}

			}else {
					$err	= $empresa->errores;
					foreach ($err as $text) {
						$this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
				  }

				  if(Modelo\Empresa::$FLAG){
					$empresaCargada =  Modelo\Empresa::obtenerPorCuit($empresa->cuit);
					$redirect =Vista::get_url("index.php/empresas/activar/{$empresaCargada->id}");
					$this->redirect($redirect);

				}
			}

		}


		 $vista = $this->vista;
		(new Vista($this->vista_default,compact('vista', 'empresa', 'camaras')))->pre_render();
	}

	protected function accion_modificar() {
		$empresa		= Modelo\Empresa::obtener($this->request->query('id'));
		$camaras	= Modelo\Camara::lista_camaras();
    	$camaras[99999] = ['id'=>99999, 'nombre' =>'Sin cámara', 'descripcion'=>'Sin descripción', 'borrado' =>0];

		if($this->request->post('boton_empresa') == 'modificacion') {
			$empresa->cuit = !empty($this->request->post('cuit')) ? $this->request->post('cuit') : $empresa->cuit;
			$empresa->razon_social	= !empty($this->request->post('razon_social')) ? $this->request->post('razon_social') : $empresa->razon_social;
			$empresa->codigo_cnrt = !empty($this->request->post('codigo_cnrt')) ? $this->request->post('codigo_cnrt') : $empresa->codigo_cnrt;
			$empresa->id_camara= !empty($this->request->post('id_camara')) ? $this->request->post('id_camara') : $empresa->id_camara;
			$empresa->direccion = !empty($this->request->post('direccion')) ? $this->request->post('direccion') :$empresa->direccion;
			$empresa->nombre_apoderado = !empty($this->request->post('nombre_apoderado')) ?  $this->request->post('nombre_apoderado') : $empresa->nombre_apoderado;
			$empresa->dni_apoderado = !empty($this->request->post('dni_apoderado')) ?  $this->request->post('dni_apoderado') : $empresa->dni_apoderado;
			if($empresa->validar()){
				if($empresa->modificacion()){
						$this->mensajeria->agregar(
							"La Empresa <strong>{$empresa->razon_social}</strong> ha sido modificada correctamente",
							\FMT\Mensajeria::TIPO_AVISO,
						$this->clase);
						$redirect = Vista::get_url("index.php/Empresas/index");
						$this->redirect($redirect);
				}else{
					$this->mensajeria->agregar(
						"No se pudo modificar la Empresa <strong>{$empresa->razon_social}</strong>.",
						\FMT\Mensajeria::TIPO_ERROR,
					$this->clase);
				}
			} else {
				foreach ((array)$empresa->errores as $text) {
					$this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
				}
			}
		}

		$vista = $this->vista;
		(new Vista($this->vista_default,compact('vista','empresa', 'camaras')))->pre_render();

	}

	protected function accion_baja() {
	 	$empresa					= Modelo\Empresa::obtener($this->request->query('id'));
		if (!empty($empresa->id)){
			if($this->request->post('confirmar')){
				if($empresa->id){
					if ($empresa->baja()){
							$this->mensajeria->agregar(
							"La Empresa <strong>{$empresa->razon_social}</strong> ha sido eliminado correctamente.",
							\FMT\Mensajeria::TIPO_AVISO,
							$this->clase);
					} else {
						$this->mensajeria->agregar(
						"No se ha podido eliminar la Empresa <strong>{$empresa->razon_social}</strong>.",
						\FMT\Mensajeria::TIPO_ERROR,
						$this->clase);
					}
					$redirect =Vista::get_url("index.php/empresas/index/");
					$this->redirect($redirect);
				}
			}
		}

		$vista = $this->vista;
		(new Helper\Vista($this->vista_default,compact('vista', 'empresa')))->pre_render();
	}

	protected function accion_buscarEmpresa(){
        if($this->request->is_ajax()){
        	$data =  Modelo\CNRTApi::getEmpresa($this->request->post('cuit'));
            $this->json->setData($data);
            $this->json->render();

        }
    }

    protected function accion_activar() {
	 	$empresa					= Modelo\Empresa::obtener($this->request->query('id'));
	 	if (!empty($empresa->id)){
			if($this->request->post('confirmar')){
				if($empresa->id){
					if ($empresa->activar()){
							$this->mensajeria->agregar(
							"La Empresa <strong>{$empresa->razon_social}</strong> se ha reactivado correctamente.",
							\FMT\Mensajeria::TIPO_AVISO,
							$this->clase);
					} else {
						$this->mensajeria->agregar(
						"No se ha podido reactivar la Empresa <strong>{$empresa->razon_social}</strong>.",
						\FMT\Mensajeria::TIPO_ERROR,
						$this->clase);
					}
					$redirect =Vista::get_url("index.php/empresas/index/");
					$this->redirect($redirect);
				}
			}
		}

		$vista = $this->vista;
		(new Helper\Vista($this->vista_default,compact('vista', 'empresa')))->pre_render();
	}

	protected function accion_ajax_empresas(){

		$dataTable_columns    = $this->request->query('columns');
		$orders    = [];
		foreach ($orden = (array)$this->request->query('order') as $i => $val) {
			$orders[]    = [
				'campo'    => (!empty($tmp = $orden[$i]) && !empty($dataTable_columns) && is_array($dataTable_columns[0]))
					? $dataTable_columns[(int)$tmp['column']]['data']    :    'id',
				'dir'    => !empty($tmp = $orden[$i]['dir'])
				? $tmp    :    'desc',
			];
		}
		$date  = [];
		if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $this->request->query('search')['value'], $date)) {
			$el_resto = \preg_replace('/^\d{2}\/\d{2}\/\d{4}/', '', $this->request->query('search')['value']);
			$search = \DateTime::createFromFormat('d/m/Y', $date[0])->format('Y-m-d') . $el_resto;
		} else {
			$search = $this->request->query('search')['value'];
		}
		$params    = [
			'order'        => $orders,
			'start'        => !empty($tmp = $this->request->query('start'))
			? $tmp : 0,
			'lenght'    => !empty($tmp = $this->request->query('length'))
			? $tmp : 10,
			'search'    => !empty($search)
				? $search : '',
			'filtros'   => [],
		];
		$data =  Modelo\Empresa::listar_empresas($params); 
		$datos['draw']    = (int) $this->request->query('draw');
		(new Vista(VISTAS_PATH . '/json_response.php', compact('data')))->pre_render();
	}

}

