<?php

use \FMT\Template;
use \App\Helper\Vista;
$rol = App\Modelo\AppRoles::obtener_rol();
$config = FMT\Configuracion::instancia();

$estados_pedido  = json_encode([
  'solicitado' => App\Modelo\Pedido_marbete::PEDIDO_SOLICITADO,
  'autorizado' =>  App\Modelo\Pedido_marbete::PEDIDO_AUTORIZADO,
  'firmado'    =>  App\Modelo\Pedido_marbete::PEDIDO_FIRMADO,
  'rechazado'  => App\Modelo\Pedido_marbete::PEDIDO_RECHAZADO,
  'impreso_entregado'  => App\Modelo\Pedido_marbete::PEDIDO_IMPRESO_ENTREGADO,
  'anulado'  => App\Modelo\Pedido_marbete::PEDIDO_ANULADO,

], JSON_UNESCAPED_UNICODE);

$roles = json_encode([
  'administracion'  => App\Modelo\AppRoles::ROL_ADMINISTRACION,
  'carga'           => App\Modelo\AppRoles::CARGA,
  'autorizante'     => App\Modelo\AppRoles::AUTORIZANTE,
  'firmante'        => App\Modelo\AppRoles::FIRMANTE
], JSON_UNESCAPED_UNICODE);

$vars_template['URL_BASE'] = Vista::get_url();

$vars_template['LINK'] = Vista::get_url('index.php/Pedidos_marbete/alta');

$vars_vista['SUBTITULO'] = 'Gestión de Pedidos de Marbetes.';
$vars_template['BOTON_EXCEL'] = \App\Helper\Vista::get_url("index.php/Pedidos_marbete/exportar_excel");

$pedidos = new Template(TEMPLATE_PATH . '/pedidos_marbete/index.html', $vars_template, ['CLEAN' => false]);
$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('script.js');
$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('/pedidos_marbete/pedidos_marbete.js');

$vars_vista['CSS_FILES'][]  = ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'].'/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]   = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]   = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/defaults.js"];
$endpoint_cdn = $config['app']['endpoint_cdn'];
$base_url = \App\Helper\Vista::get_url();

$vars_vista['JS'][]['JS_CODE']    = <<<JS
var \$endpoint_cdn    = "{$endpoint_cdn}";
var \$base_url        = "{$base_url}";
var \$rol_actual    = {$rol};
var \$estados_pedido    ={$estados_pedido};
var \$roles    = {$roles};
JS;

$vars_vista['CONTENT'] = "{$pedidos}";
$vista->add_to_var('vars', $vars_vista);
