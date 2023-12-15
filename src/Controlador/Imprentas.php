<?php

namespace App\Controlador;

use App\Modelo;
use App\Helper;
use App\Helper\Vista;


class Imprentas extends Base
{

    protected function accion_index(){
        $imprentas = Modelo\Imprenta::listar();
        (new Helper\Vista($this->vista_default, ['imprentas' => $imprentas, 'vista' => $this->vista]))
            ->pre_render();
    }

    protected function accion_alta(){
        $imprenta = Modelo\Imprenta::obtener($this->request->query('id'));

        if ($this->request->post('boton_imprenta') == 'alta') {
            $imprenta->cuit = !empty($temp = $this->request->post('cuit')) ?  $temp : null;
            $imprenta->razon_social = !empty($temp = $this->request->post('razon_social')) ?  $temp : null;
            $imprenta->direccion = !empty($temp = $this->request->post('direccion')) ?  $temp : null;
            $imprenta->fecha_ultima_revision = !empty($temporal = $this->request->post('fecha_ultima_revision')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
            $imprenta->inscripcion_en_afip        = !empty($temp = $this->request->post('inscripcion_afip')) ?  $temp : null;
            $imprenta->modelo_de_marbete          = !empty($temp = $this->request->post('modelo_marbete')) ?  $temp : null;
            $imprenta->observacion = !empty($temp = $this->request->post('observacion')) ?  $temp : null;

            if ($imprenta->validar()) {
                if($imprenta->alta()){
                    $this->mensajeria->agregar(
                    "La Imprenta <strong>{$imprenta->razon_social}</strong> fue cargada correctamente",
            \FMT\Mensajeria::TIPO_AVISO,
            $this->clase);
                    $redirect = Vista::get_url("index.php/Imprentas/index");
                    $this->redirect($redirect);
                }else{
                    $this->mensajeria->agregar(
                    "No se pudo cargar la Imprenta <strong>{$imprenta->razon_social}</strong>",
            \FMT\Mensajeria::TIPO_ERROR,
            $this->clase);
                }
            } else {
                $err    = $imprenta->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }

                    if(Modelo\Imprenta::$FLAG){
                    $imprentaCargada =  Modelo\Imprenta::obtenerPorCuit($imprenta->cuit);
                    $redirect =Vista::get_url("index.php/imprentas/activar/{$imprentaCargada->id}");
                    $this->redirect($redirect);

                }
            }
        }

        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista','imprenta')))->pre_render();
    }

    protected function accion_modificar(){
        $imprenta = Modelo\Imprenta::obtener($this->request->query('id'));
        if ($this->request->post('boton_imprenta') == 'modificacion') {
            $imprenta->cuit = !empty($temp = $this->request->post('cuit')) ?  $temp : null;
            $imprenta->razon_social = !empty($temp = $this->request->post('razon_social')) ?  $temp : null;
            $imprenta->direccion = !empty($temp = $this->request->post('direccion')) ?  $temp : null;
            $imprenta->fecha_ultima_revision = !empty($temporal = $this->request->post('fecha_ultima_revision')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
            $imprenta->inscripcion_en_afip        = !empty($temp = $this->request->post('inscripcion_afip')) ?  $temp : null;
            $imprenta->modelo_de_marbete          = !empty($temp = $this->request->post('modelo_marbete')) ?  $temp : null;
            $imprenta->observacion = !empty($temp = $this->request->post('observacion')) ?  $temp : null;

            if ($imprenta->validar()) {
                if($imprenta->modificacion()){
                        $this->mensajeria->agregar(
                            "La Imprenta <strong>{$imprenta->razon_social}</strong> ha sido modificada correctamente",
                            \FMT\Mensajeria::TIPO_AVISO,
                        $this->clase);
                        $redirect = Vista::get_url("index.php/Imprentas/index");
                        $this->redirect($redirect);
                }else{
                    $this->mensajeria->agregar(
                        "No se pudo modificar la Imprenta <strong>{$imprenta->razon_social}</strong>.",
                        \FMT\Mensajeria::TIPO_ERROR,
                    $this->clase);
                }
            } else {
                $err    = $imprenta->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }
            }
        }

        $vista = $this->vista;
        (new Vista($this->vista_default, compact('imprenta', 'vista')))->pre_render();
    }

    protected function accion_baja() {
        $imprenta                 = Modelo\Imprenta::obtener($this->request->query('id'));
        if (!empty($imprenta->id)){
            if($this->request->post('confirmar')){
                if($imprenta->id){
                    if ($imprenta->baja()){
                            $this->mensajeria->agregar(
                            "La Imprenta <strong>{$imprenta->razon_social}</strong> ha sido eliminada correctamente.",
                            \FMT\Mensajeria::TIPO_AVISO,
                            $this->clase);
                    } else {
                        $this->mensajeria->agregar(
                        "No se ha podido eliminar la Imprenta <strong>{$imprenta->razon_social}</strong>.",
                        \FMT\Mensajeria::TIPO_ERROR,
                        $this->clase);
                    }
                    $redirect =Vista::get_url("index.php/imprentas/index/");
                    $this->redirect($redirect);
                }
            }
        }

        $vista = $this->vista;
        (new Helper\Vista($this->vista_default,compact('vista', 'imprenta')))->pre_render();
    }

    protected function accion_activar() {
        $imprenta = Modelo\Imprenta::obtener($this->request->query('id'));
        if (!empty($imprenta->id)){
            if($this->request->post('confirmar')){
                if($imprenta->id){
                    if ($imprenta->activar()){
                            $this->mensajeria->agregar(
                            " La Imprenta <strong>{$imprenta->razon_social}</strong> se ha reactivada correctamente.",
                            \FMT\Mensajeria::TIPO_AVISO,
                            $this->clase);
                    } else {
                        $this->mensajeria->agregar(
                        "No se ha podido reactivar la Imprenta <strong>{$imprenta->razon_social}</strong>.",
                        \FMT\Mensajeria::TIPO_ERROR,
                        $this->clase);
                    }
                    $redirect =Vista::get_url("index.php/imprentas/index/");
                    $this->redirect($redirect);
                }
            }
        }
        $vista = $this->vista;
        (new Helper\Vista($this->vista_default,compact('vista', 'imprenta')))->pre_render();
    }
}
