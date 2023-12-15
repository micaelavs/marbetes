<?php
use \FMT\Helper\Template;
use \FMT\Helper\Arr;
use FMT\Vista;

	$config	= \FMT\Configuracion::instancia();
	$vars_vista['SUBTITULO']	= 'Modificar Empresa';
	$vars_vista['CSS_FILES'][]    = ['CSS_FILE'   => $config['app']['endpoint_cdn']."/js/select2/css/select2.min.css"];
    $vars_vista['JS_FILES'][]     = ['JS_FILE'    => $config['app']['endpoint_cdn']."/js/select2/js/select2.full.min.js"];
    $vars_template['OPERACION'] = 'modificacion';
	$vars_template['CUIT']				=  $empresa->cuit;
    $vars_template['RAZON_SOCIAL']		=  $empresa->razon_social;
    $vars_template['CODIGO_CNRT']		=  $empresa->codigo_cnrt;
    $vars_template['CAMARA']			= \FMT\Helper\Template::select_block($camaras,$empresa->id_camara);
	$vars_template['DIRECCION']			=  $empresa->direccion;
	$vars_template['NOMBRE_APODERADO']	=  $empresa->nombre_apoderado;
	$vars_template['DNI_APODERADO']		=  $empresa->dni_apoderado;
     $vars_template['DISABLED'] 		=  'disabled';
    $vars_template['CANCELAR'] = \App\Helper\Vista::get_url('index.php/empresas/index');
	$template = (new \FMT\Template(VISTAS_PATH.'/templates/empresas/alta.html', $vars_template,['CLEAN'=>false]));
	$vars_vista['CONTENT'] = "$template";
	$base_url = \App\Helper\Vista::get_url('index.php');
	$vars_vista['JS'][]['JS_CODE']	= <<<JS
	var \$base_url = "{$base_url}";
JS;
	$vista->add_to_var('vars',$vars_vista);

	return true;
?>
