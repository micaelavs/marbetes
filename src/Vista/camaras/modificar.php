<?php
use \FMT\Helper\Template;
use \FMT\Helper\Arr;
use FMT\Vista;

	$config	= \FMT\Configuracion::instancia();
	$vars_vista['SUBTITULO']	= 'Modificar Cámara';
	$vars_vista['CSS_FILES'][]    = ['CSS_FILE'   => $config['app']['endpoint_cdn']."/js/select2/css/select2.min.css"];
    $vars_vista['JS_FILES'][]     = ['JS_FILE'    => $config['app']['endpoint_cdn']."/js/select2/js/select2.full.min.js"];
   // $vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('camaras.js');
    $vars_template['OPERACION'] = 'modificacion';
	$vars_template['NOMBRE'] 		= !empty($camara->nombre) ? $camara->nombre : '';
	$vars_template['DESCRIPCION']	= !empty($camara->descripcion) ? $camara->descripcion : '';
    $vars_template['CANCELAR'] = \App\Helper\Vista::get_url('index.php/camaras/index'); 
	$template = (new \FMT\Template(VISTAS_PATH.'/templates/camaras/alta.html', $vars_template,['CLEAN'=>false]));
	$vars_vista['CONTENT'] = "$template";
	$base_url = \App\Helper\Vista::get_url('index.php');
	$vars_vista['JS'][]['JS_CODE']	= <<<JS
	var \$base_url = "{$base_url}";
JS;
	$vista->add_to_var('vars',$vars_vista);

	return true;
?>
