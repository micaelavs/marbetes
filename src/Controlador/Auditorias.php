<?php

namespace App\Controlador;

use App\Helper\Vista;
use App\Modelo;
use App\Modelo\Auditoria;
use FMT\Helper\Arr;
use App\Modelo\AppRoles;


class Auditorias extends Base
{

    protected function accion_index()
    {
        $lista_pedidos      = Modelo\Auditoria::listar();
        $empresas           = Modelo\Empresa::lista_empresas();
        $imprentas          = Modelo\Imprenta::lista_imprentas();
        $tipos_marbetes     = Modelo\Tipo_marbete::lista_tipo_marbetes_select();
        $camaras            = Modelo\Camara::lista_camaras();
        $fecha_desde        = \DateTime::createFromFormat('d/m/Y', $this->request->post('fecha_desde'));
        $fecha_hasta        = \DateTime::createFromFormat('d/m/Y', $this->request->post('fecha_hasta'));


        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'empresas', 'imprentas', 'tipos_marbetes', 'camaras', 'lista_pedidos', 'fecha_desde', 'fecha_hasta')))->pre_render();
    }

    public function accion_ajax_auditoria(){

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
                'empresa'      => $this->request->query('empresa'),
                'fecha_desde'  => $this->request->query('fecha_desde'),
                'fecha_hasta'  => $this->request->query('fecha_hasta'),
                'tipo_marbete' => $this->request->query('tipo_marbete'),
                'imprenta'     => $this->request->query('imprenta'),
                'camara'       => $this->request->query('camara')

            ],
        ];
        
        $data =  Modelo\Auditoria::listar_auditoria($params); 
        $datos['draw']    = (int) $this->request->query('draw');
        (new Vista(VISTAS_PATH . '/json_response.php', compact('data')))->pre_render();

    }

    public function accion_exportar_pedidos_csv(){
        $nombre = 'reporte_pedidos_marbetes_'.date('Ymd_His');
        $user = Modelo\Usuario::obtenerUsuarioLogueado();
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
                'empresa'       => $this->request->post('empresa'),
                'imprenta'      => $this->request->post('imprenta'),
                'tipo_marbete'  => $this->request->post('tipo_marbete'),
                'camara'        => $this->request->post('camara'),
                'fecha_desde'   => $this->request->post('fecha_desde'),
                'fecha_hasta'   => $this->request->post('fecha_hasta')
            ],
             
        ];
        $titulos = [
            'imprenta' => 'Imprenta',
            'empresa'  => 'Empresa',
            'camara'   => 'Cámara',
            'tipo_marbete' => 'Tipo Marbete',
            'estado'       => 'Estado',
            'fecha_solicitud' => 'Fecha Solicitud',
            'cantidad' => 'Marbetes Solicitados',
            'cantidad_autorizada' => 'Marbetes Autorizados',
            'numeracion_asignada' => 'Numeracion Asignada',
            
        ];

        $data = Auditoria::listar_auditoria_excel($params);
        array_walk($data, function (&$value) {
            unset($value['id']);
        
        });

        $usuario = $user->nombre.' '. $user->apellido;

    (new Vista(VISTAS_PATH.'/csv_response.php',compact('nombre', 'titulos', 'data', 'usuario')))->pre_render();
    }


}

