<?php
namespace App\Controlador;

use FMT\Controlador;
use App\Modelo;
use App\Helper;
use FMT\Configuracion;
use App\Helper\Vista;
use App\Helper\Util;
use App\Modelo\Usuario;

class Pedidos_marbete extends Base {

	protected function accion_index() {
		$vista = $this->vista;
		(new Vista($this->vista_default,compact('vista')))->pre_render();

	}

	protected function accion_ajax_pedidos(){
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
            'filtros'   => [
              
            ],
        ];

        $data =  Modelo\Pedido_marbete::listar_pedidos($params);
        $datos['draw']    = (int) $this->request->query('draw');
        (new Vista(VISTAS_PATH . '/json_response.php', compact('data')))->pre_render();


    }

    protected function accion_exportar_excel() {
        $user = Modelo\Usuario::obtenerUsuarioLogueado();
        $nombre = 'pedido_marbete'.date('Ymd_His');

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $this->request->post('search'), $date)) {
            $el_resto = \preg_replace('/^\d{2}\/\d{2}\/\d{4}/', '', $this->request->post('search'));
            $search = \DateTime::createFromFormat('d/m/Y', $date[0])->format('Y-m-d') . $el_resto;
        } else {
            $search = $this->request->post('search');
        }

        $params = [
            'order' => [!empty($this->request->post('campo_sort')) ? [
                'campo'=> $this->request->post('campo_sort'),
                'dir' => $this->request->post('dir')
            ] : ''],
            'search'    => !empty($search) ? $search : '',
            'start'     => '',
            'lenght'    => '',
            'filtros'   => [
            ],
        ];
     
        $titulos = [
            'id'   			=>'Nº Pedido',
            'empresa'       =>'Empresa',
            'tipo_marbete'  =>'Tipo Marbete',
            'imprenta'      =>'Imprenta',
            'fecha_solicitud' =>'Fecha de Solicitud',
            'cantidad_solicitada'	=>'Cantidad Solicitada',
            'cantidad_autorizada'   =>'Cantidad Autorizada',
            'estado'       			=>'Estado',
            'observaciones' 		=>'Observaciones'
        ];

        $data = Modelo\Pedido_marbete::listar_pedidos_excel($params);
        
        $data[] = ['Usuario' => 'Usuario Logueado: '. $user->nombre.' '. $user->apellido];

        (new Vista(VISTAS_PATH.'/csv_response.php',compact('nombre', 'titulos', 'data')))->render();
    }

	protected function accion_alta(){
		$pedido 		= Modelo\Pedido_marbete::obtener($this->request->query('id'));
		$empresas		= Modelo\Empresa::lista_empresas();
		$imprentas 		= Modelo\Imprenta::lista_imprentas();
		$tipos_marbetes = Modelo\Tipo_marbete::lista_tipo_marbetes();

		if($this->request->post('boton_pedido') == 'alta') {
			$pedido->id_empresa = !empty($this->request->post('id_empresa')) ? $this->request->post('id_empresa') : null;
			$pedido->id_imprenta = !empty($this->request->post('id_imprenta')) ? $this->request->post('id_imprenta') : null;
			$pedido->id_tipo_marbete = !empty($this->request->post('id_tipo_marbete')) ? $this->request->post('id_tipo_marbete') : null;
			$pedido->fecha_solicitud = !empty($temporal = $this->request->post('fecha_solicitud')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
			$pedido->cantidad = !empty($this->request->post('cantidad')) ? $this->request->post('cantidad') : null;

			if($pedido->validar()){
				if($pedido->alta()){
					$this->mensajeria->agregar(
					"El Pedido fue cargado correctamente.",
			\FMT\Mensajeria::TIPO_AVISO,
			$this->clase);
					$redirect = Vista::get_url("index.php/Pedidos_marbete/index");
					$this->redirect($redirect);
				}else{
					$this->mensajeria->agregar(
					"No se pudo cargar el Pedido.",
			\FMT\Mensajeria::TIPO_ERROR,
			$this->clase);
				}
			} else {
				foreach ($pedido->errores as $text) {
					$this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);

				}

			}

		}

		$vista = $this->vista;
		(new Vista($this->vista_default,compact('vista','pedido', 'empresas', 'imprentas', 'tipos_marbetes')))->pre_render();
	}

	protected function accion_modificar() {
		$pedido			= Modelo\Pedido_marbete::obtener($this->request->query('id'));
		$empresas		= Modelo\Empresa::lista_empresas();
		$imprentas 		= Modelo\Imprenta::lista_imprentas();
		$tipos_marbetes = Modelo\Tipo_marbete::lista_tipo_marbetes();
		if($this->request->post('boton_pedido') == 'modificacion') {
			$pedido->id_empresa = !empty($this->request->post('id_empresa')) ? $this->request->post('id_empresa') : $pedido->id_empresa;
			$pedido->id_imprenta	= !empty($this->request->post('id_imprenta')) ? $this->request->post('id_imprenta') : $pedido->id_imprenta;
			$pedido->id_tipo_marbete = !empty($this->request->post('id_tipo_marbete')) ? $this->request->post('id_tipo_marbete') : $pedido->id_tipo_marbete;
			$pedido->fecha_solicitud 	= !empty($temporal = $this->request->post('fecha_solicitud')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : $pedido->fecha_solicitud;
			$pedido->cantidad	= !empty($this->request->post('cantidad')) ? $this->request->post('cantidad') : $pedido->cantidad;
			$pedido->estado	= !empty($this->request->post('estado')) ? $this->request->post('estado') : $pedido->estado;

			if($pedido->validar()){
				if($pedido->modificacion()){
						$this->mensajeria->agregar(
							"El Pedido ha sido modificado correctamente.",
							\FMT\Mensajeria::TIPO_AVISO,
						$this->clase);
						$redirect = Vista::get_url("index.php/Pedidos_marbete/index");
						$this->redirect($redirect);
				}else{
					$this->mensajeria->agregar(
						"No se pudo modificar el Pedido.",
						\FMT\Mensajeria::TIPO_ERROR,
					$this->clase);
				}
			} else {
				foreach ((array)$pedido->errores as $text) {
					$this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
				}
			}
		}

		$vista = $this->vista;
		(new Vista($this->vista_default,compact('vista','pedido', 'empresas', 'imprentas', 'tipos_marbetes')))->pre_render();

	}

	protected function accion_baja() {
	 	$pedido			= Modelo\Pedido_marbete::obtener($this->request->query('id'));
	 	$empresa		= Modelo\Empresa::obtener($pedido->id_empresa);
		$imprenta 		= Modelo\Imprenta::obtener($pedido->id_imprenta);
		$tipo_marbete 	= Modelo\Tipo_marbete::obtener($pedido->id_tipo_marbete);
		if (!empty($pedido->id)){
			if($this->request->post('confirmar')){
				if($pedido->id){
					if ($pedido->baja()){
							$this->mensajeria->agregar(
							"El Pedido a sido eliminado correctamente.",
							\FMT\Mensajeria::TIPO_AVISO,
							$this->clase);
					} else {
						$this->mensajeria->agregar(
						"No se ha podido eliminar el Pedido.",
						\FMT\Mensajeria::TIPO_ERROR,
						$this->clase);
					}
					$redirect = Vista::get_url("index.php/Pedidos_marbete/index/");
					$this->redirect($redirect);
				}
			}
		}

		$vista = $this->vista;
		(new Helper\Vista($this->vista_default,compact('vista', 'pedido', 'empresa', 'imprenta', 'tipo_marbete')))->pre_render();
	}

    protected function accion_buscarEmpresa(){
		if($this->request->is_ajax()){
			$data = $this->_get_empresa($this->request->post('id_empresa'));
			$this->json->setData($data);
			$this->json->render();
			exit;
		}
	}

	private function _get_empresa($id = null) {
		if (!is_null($id)) {
			$empresa	=  Modelo\Empresa::obtener($id);
			}
		return $empresa;
	}

	protected function accion_pedido_autorizado(){
		$vista				= $this->vista;
		$pedido				= Modelo\Pedido_marbete::obtener($this->request->query('id'));
		$empresas			= Modelo\Empresa::lista_empresas();
		$imprentas 			= Modelo\Imprenta::lista_imprentas();
		$tipos_marbetes 	= Modelo\Tipo_marbete::lista_tipo_marbetes();
		$asignacion_marbete = Modelo\Pedido_marbete::obtener_asignacion_marbete_de_pedido($pedido->id);
		if(!is_null($pedido->fecha_autorizacion)){
			$file_nombre = 'Pedido_Marbete_'. $pedido->fecha_autorizacion->format('d-m-Y');
		}else{
			$file_nombre = 'Pedido_Marbete_Sin_Fecha_Autorización';
		}
		
			(new Vista($this->vista_default, compact('vista','pedido','empresas','imprentas','tipos_marbetes','asignacion_marbete','file_nombre')))->pre_render();
	}
	protected function accion_autorizacion_alta(){
		$empresas		= Modelo\Empresa::lista_empresas();
		$imprentas 		= Modelo\Imprenta::lista_imprentas();
		$tipos_marbetes = Modelo\Tipo_marbete::lista_tipo_marbetes();
		$pedido 		= Modelo\Pedido_marbete::obtener($this->request->query('id'));
		if($this->request->post('boton_autorizar') == 'alta') {
			$pedido->id_empresa = !empty($this->request->post('id_empresa')) ? $this->request->post('id_empresa') : $pedido->id_empresa;
			$pedido->id_imprenta	= !empty($this->request->post('id_imprenta')) ? $this->request->post('id_imprenta') : $pedido->id_imprenta;
			$pedido->id_tipo_marbete = !empty($this->request->post('id_tipo_marbete')) ? $this->request->post('id_tipo_marbete') : $pedido->id_tipo_marbete;
			$pedido->fecha_solicitud 	= !empty($temporal = $this->request->post('fecha_solicitud')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : $pedido->fecha_solicitud;
			$pedido->cantidad	= !empty($this->request->post('cantidad_solicitada')) ? $this->request->post('cantidad_solicitada') : $pedido->cantidad;
			$pedido->fecha_autorizacion	= !empty($temporal = $this->request->post('fecha_autorizacion'). ' 00:00:00') ?  \DateTime::createFromFormat('d/m/Y H:i:s', $temporal) : $pedido->fecha_autorizacion;
			$pedido->cantidad_autorizada	= !empty($this->request->post('cantidad_autorizada')) ? $this->request->post('cantidad_autorizada') : $pedido->cantidad_autorizada;
			$pedido->estado	=  \App\Modelo\Pedido_marbete::PEDIDO_AUTORIZADO;
			if($pedido->validar()){
				if($pedido->autorizacion_alta()){
					$this->mensajeria->agregar(
					"El Pedido fue autorizado correctamente.",
			\FMT\Mensajeria::TIPO_AVISO,
			$this->clase);
					$redirect = Vista::get_url("index.php/Pedidos_marbete/index");
					$this->redirect($redirect);
				}else{
					$this->mensajeria->agregar(
					"No se pudo autorizar el Pedido.",
			\FMT\Mensajeria::TIPO_ERROR,
			$this->clase);
				}
			} else {
				foreach ($pedido->errores as $text) {
					$this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
				}
			}
		}

		$vista = $this->vista;
		(new Vista($this->vista_default,compact('vista','pedido','empresas','imprentas','tipos_marbetes')))->pre_render();
	}

	protected function accion_rechazar() {
	 	$pedido					= Modelo\Pedido_marbete::obtener($this->request->query('id'));
	 	$empresa		= Modelo\Empresa::obtener($pedido->id_empresa);
		$imprenta 		= Modelo\Imprenta::obtener($pedido->id_imprenta);
		$tipo_marbete 	= Modelo\Tipo_marbete::obtener($pedido->id_tipo_marbete);
		if (!empty($pedido->id)){
			if($this->request->post('confirmar')){
				$pedido->observaciones = !empty($this->request->post('observaciones')) ? $this->request->post('observaciones') : null;
				$pedido->estado	=  \App\Modelo\Pedido_marbete::PEDIDO_RECHAZADO;
				if($pedido->id){
					if ($pedido->rechazar()){
							$this->mensajeria->agregar(
							"El Pedido ha sido rechazado correctamente.",
							\FMT\Mensajeria::TIPO_AVISO,
							$this->clase);
					} else {
						$this->mensajeria->agregar(
						"No se ha podido rechazar el Pedido.",
						\FMT\Mensajeria::TIPO_ERROR,
						$this->clase);
					}
					$redirect =Vista::get_url("index.php/Pedidos_marbete/index/");
					$this->redirect($redirect);
				}
			}
		}

		$vista = $this->vista;
		(new Helper\Vista($this->vista_default,compact('vista', 'pedido', 'empresa', 'imprenta', 'tipo_marbete')))->pre_render();
	}

	protected function accion_firmar() {
	 	$pedido					= Modelo\Pedido_marbete::obtener($this->request->query('id'));
	 	$empresa		= Modelo\Empresa::obtener($pedido->id_empresa);
		$imprenta 		= Modelo\Imprenta::obtener($pedido->id_imprenta);
		$tipo_marbete 	= Modelo\Tipo_marbete::obtener($pedido->id_tipo_marbete);
		if (!empty($pedido->id)){
			if($this->request->post('confirmar')){
				$pedido->estado	=  \App\Modelo\Pedido_marbete::PEDIDO_FIRMADO;
				if($pedido->id){
					if ($pedido->firmar()){
							$this->mensajeria->agregar(
							"El Pedido ha sido firmado correctamente.",
							\FMT\Mensajeria::TIPO_AVISO,
							$this->clase);
					} else {
						$this->mensajeria->agregar(
						"No se ha podido firmar el Pedido.",
						\FMT\Mensajeria::TIPO_ERROR,
						$this->clase);
					}
					$redirect =Vista::get_url("index.php/Pedidos_marbete/index/");
					$this->redirect($redirect);
				}
			}
		}

		$vista = $this->vista;
		(new Helper\Vista($this->vista_default,compact('vista', 'pedido', 'empresa', 'imprenta', 'tipo_marbete')))->pre_render();
	}

	protected function accion_anular() {
	 	$pedido			= Modelo\Pedido_marbete::obtener($this->request->query('id'));
	 	$empresa		= Modelo\Empresa::obtener($pedido->id_empresa);
		$imprenta 		= Modelo\Imprenta::obtener($pedido->id_imprenta);
		$tipo_marbete 	= Modelo\Tipo_marbete::obtener($pedido->id_tipo_marbete);
		if (!empty($pedido->id)){
			if($this->request->post('confirmar')){
				$pedido->estado	=  \App\Modelo\Pedido_marbete::PEDIDO_ANULADO;
				if($pedido->id){
					if ($pedido->anular()){
							$this->mensajeria->agregar(
							"El Pedido ha sido anulado correctamente.",
							\FMT\Mensajeria::TIPO_AVISO,
							$this->clase);
					} else {
						$this->mensajeria->agregar(
						"No se ha podido anular el Pedido.",
						\FMT\Mensajeria::TIPO_ERROR,
						$this->clase);
					}
					$redirect =Vista::get_url("index.php/Pedidos_marbete/index/");
					$this->redirect($redirect);
				}
			}
		}

		$vista = $this->vista;
		(new Helper\Vista($this->vista_default,compact('vista', 'pedido', 'empresa', 'imprenta', 'tipo_marbete')))->pre_render();
	}

	protected function accion_imprimir_entregar() {
	 	$pedido			= Modelo\Pedido_marbete::obtener($this->request->query('id'));
	 	$empresa		= Modelo\Empresa::obtener($pedido->id_empresa);
		$imprenta 		= Modelo\Imprenta::obtener($pedido->id_imprenta);
		$tipo_marbete 	= Modelo\Tipo_marbete::obtener($pedido->id_tipo_marbete);
		if (!empty($pedido->id)){
			if($this->request->post('confirmar')){
				$pedido->estado	=  \App\Modelo\Pedido_marbete::PEDIDO_IMPRESO_ENTREGADO;
				if($pedido->id){
					if ($pedido->imprimir_entregar()){
							$this->mensajeria->agregar(
							"El Pedido ha sido entregado para su impresión correctamente.",
							\FMT\Mensajeria::TIPO_AVISO,
							$this->clase);
					} else {
						$this->mensajeria->agregar(
						"El Pedido no se ha podido entregar para su impresión.",
						\FMT\Mensajeria::TIPO_ERROR,
						$this->clase);
					}
					$redirect =Vista::get_url("index.php/Pedidos_marbete/index/");
					$this->redirect($redirect);
				}
			}
		}

		$vista = $this->vista;
		(new Helper\Vista($this->vista_default,compact('vista', 'pedido', 'empresa', 'imprenta', 'tipo_marbete')))->pre_render();
	}

}

