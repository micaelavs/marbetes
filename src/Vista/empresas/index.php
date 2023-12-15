<?php
use \FMT\Template;
use \App\Helper\Vista;

$config = FMT\Configuracion::instancia();

$vars_template['URL_BASE'] = Vista::get_url();
$vars_template['LINK'] = Vista::get_url('index.php/empresas/alta');

$vars_vista['SUBTITULO'] = 'Gestión de Empresas.';
$empresas = new Template(TEMPLATE_PATH . '/empresas/index.html', $vars_template, ['CLEAN' => false]);

$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('script.js');
$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('/empresas/empresas.js');

$vars_vista['CSS_FILES'][]  = ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'].'/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][] = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][] = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/defaults.js"];
$endpoint_cdn = $config['app']['endpoint_cdn'];
$url_base = \App\Helper\Vista::get_url();

$vars_vista['JS'][]['JS_CODE']    = <<<JS
var \$endpoint_cdn    = "{$endpoint_cdn}";
var \$url_base        = "{$url_base}"
JS;

$vars_vista['CONTENT'] = "{$empresas}";
$vista->add_to_var('vars', $vars_vista);
