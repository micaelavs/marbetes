<?php
namespace App\Controlador;

use FMT\Controlador;
use App\Modelo;
use App\Helper;
use FMT\Configuracion;
use App\Helper\Vista;
use App\Helper\Util;

class Tipo_marbetes extends Base
{

    protected function accion_index(){
        $tipo_marbetes = Modelo\Tipo_marbete::listar();
        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'tipo_marbetes')))->pre_render();
    }

    protected function accion_alta(){
        $tipo_marbetes = Modelo\Tipo_marbete::obtener($this->request->query('id'));

        if ($this->request->post('boton_tipo') == 'alta') {
            $tipo_marbetes->tipo = !empty($this->request->post('tipo')) ? $this->request->post('tipo') : null;
            $tipoCargado = $tipo_marbetes->tipo;
            $tipo_marbetes->descripcion = !empty($this->request->post('descripcion')) ? $this->request->post('descripcion') : null;

            if ($tipo_marbetes->validar()) {
                if ($tipo_marbetes->alta()) {
                    $this->mensajeria->agregar(
                        "El tipo de marbete <strong>{$tipo_marbetes->tipo}</strong> fue cargado correctamente",
                        \FMT\Mensajeria::TIPO_AVISO,
                        $this->clase
                    );
                    $redirect = Vista::get_url("index.php/tipo_marbetes/index");
                    $this->redirect($redirect);
                } else {
                    $this->mensajeria->agregar(
                        "No se pudo cargar el tipo de marbete <strong>{$tipo_marbetes->tipo}</strong>",
                        \FMT\Mensajeria::TIPO_ERROR,
                        $this->clase
                    );
                }
            } else {
                foreach ($tipo_marbetes->errores as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }

                if (Modelo\Tipo_marbete::$FLAG) {
                    $tipo_marbetesCargada =  Modelo\Tipo_marbete::obtenerPorTipo($tipoCargado);
                    $redirect = Vista::get_url("index.php/tipo_marbetes/activar/{$tipo_marbetesCargada->id}");
                    $this->redirect($redirect);
                }
            }
        }

        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'tipo_marbetes')))->pre_render();
    }

   protected function accion_modificar(){
        $tipo_marbetes = Modelo\Tipo_marbete::obtener($this->request->query('id'));
        if ($this->request->post('boton_tipo') == 'modificacion') {
            $tipo_marbetes->tipo = !empty($this->request->post('tipo')) ? $this->request->post('tipo') : $tipo_marbetes->tipo;
            $tipo_marbetes->descripcion    = !empty($this->request->post('descripcion')) ? $this->request->post('descripcion') : $tipo_marbetes->descripcion;
            if ($tipo_marbetes->validar()) {
                if ($tipo_marbetes->modificacion()) {
                    $this->mensajeria->agregar(
                        "El tipo de marbete <strong>{$tipo_marbetes->tipo}</strong> ha sido modificado correctamente",
                        \FMT\Mensajeria::TIPO_AVISO,
                        $this->clase
                    );
                    $redirect = Vista::get_url("index.php/tipo_marbetes/index");
                    $this->redirect($redirect);
                } else {
                    $this->mensajeria->agregar(
                        "No se pudo modificar el tipo de marbete <strong>{$tipo_marbetes->tipo}</strong>.",
                        \FMT\Mensajeria::TIPO_ERROR,
                        $this->clase
                    );
                }
            } else {
                foreach ((array)$tipo_marbetes->errores as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }
            }

        }

        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'tipo_marbetes')))->pre_render();
    }

    protected function accion_baja(){
        $tipo_marbetes = Modelo\Tipo_marbete::obtener($this->request->query('id'));
        if (!empty($tipo_marbetes->id)) {
            if ($this->request->post('confirmar')) {
                if ($tipo_marbetes->id) {
                    if ($tipo_marbetes->baja()) {
                        $this->mensajeria->agregar(
                            "El tipo de marbete <strong>{$tipo_marbetes->tipo}</strong> ha sido eliminado correctamente.",
                            \FMT\Mensajeria::TIPO_AVISO,
                            $this->clase
                        );
                    } else {
                        $this->mensajeria->agregar(
                            "No se ha podido eliminar tipo de marbete <strong>{$tipo_marbetes->tipo}</strong>.",
                            \FMT\Mensajeria::TIPO_ERROR,
                            $this->clase
                        );
                    }
                    $redirect = Vista::get_url("index.php/tipo_marbetes/index/");
                    $this->redirect($redirect);
                }
            }
        }

        $vista = $this->vista;
        (new Helper\Vista($this->vista_default, compact('vista', 'tipo_marbetes')))->pre_render();
    }

    protected function accion_activar(){
        $tipo_marbetes    = Modelo\Tipo_marbete::obtener($this->request->query('id'));
        if (!empty($tipo_marbetes->id)) {
            if ($this->request->post('confirmar')) {
                if ($tipo_marbetes->id) {
                    if ($tipo_marbetes->activar()) {
                        $this->mensajeria->agregar(
                            " El tipo de marbete <strong>{$tipo_marbetes->tipo}</strong> se ha reactivado correctamente.",
                            \FMT\Mensajeria::TIPO_AVISO,
                            $this->clase
                        );
                    } else {
                        $this->mensajeria->agregar(
                            "No se ha podido reactivar el tipo de marbete <strong>{$tipo_marbetes->tipo}</strong>.",
                            \FMT\Mensajeria::TIPO_ERROR,
                            $this->clase
                        );
                    }
                    $redirect = Vista::get_url("index.php/tipo_marbetes/index/");
                    $this->redirect($redirect);
                }
            }
        }

        $vista = $this->vista;
        (new Helper\Vista($this->vista_default, compact('vista', 'tipo_marbetes')))->pre_render();
    }

}