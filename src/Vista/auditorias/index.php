<?php

use App\Controlador\Auditorias;
use \FMT\Helper\Arr;
use \App\Helper\Vista;

$config    = \FMT\Configuracion::instancia();
$vars_template = [];
$vars_vista['SUBTITULO']    = 'Reporte';

$vars_template['EMPRESA']				= \FMT\Helper\Template::select_block($empresas);
$vars_template['IMPRENTA']				= \FMT\Helper\Template::select_block($imprentas);
$vars_template['TIPO_MARBETE']			= \FMT\Helper\Template::select_block($tipos_marbetes);
$vars_template['CAMARA']				= \FMT\Helper\Template::select_block($camaras);
$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('script.js');
$vars_vista['JS_FOOTER'][]['JS_SCRIPT']    = Vista::get_url('auditorias/auditoria.js');
$vars_vista['CSS_FILES'][]  = ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'].'/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]   = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]   = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/defaults.js"];

$base_url = Vista::get_url('index.php');

$vars_vista['JS'][]['JS_CODE'] = <<<JS
	var \$endpoint_cdn = '{$config['app']['endpoint_cdn']}';
    var \$base_url = "{$base_url}";
 	var \$data_table_init = 'tabla';
JS;

$vars_template['BOTON_EXCEL'] = \App\Helper\Vista::get_url("index.php/auditorias/exportar_pedidos_csv");
$vars_template['URL_BASE'] = \App\Helper\Vista::get_url();
$vars_template['TABLA'][] = new \FMT\Template(TEMPLATE_PATH . '/tabla.html', $vars_template, ['CLEAN' => false]);
$auditorias = new \FMT\Template(TEMPLATE_PATH . '/auditorias/index.html', $vars_template, ['CLEAN' => false]);


$vars_vista['CONTENT'] = "{$auditorias}";

$vista->add_to_var('vars', $vars_vista);
return true;