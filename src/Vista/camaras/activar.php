<?php
use \FMT\Helper\Template;
use \FMT\Helper\Arr;
use FMT\Vista;

	$vars_vista['SUBTITULO'] = 'Reactivar Camara';
	$vars_template['CONTROL'] = 'Camara';
	$vars_template['ARTICULO'] = 'La';
	$vars_template['TEXTO_AVISO'] = 'Reactivará';			
	$vars_template['NOMBRE'] = $camara->nombre;
	$vars_template['TEXTO_EXTRA'] = '.<br/>Al reactivarla volverá a visualizarla en el listado';
	$vars_template['CANCELAR'] = \App\Helper\Vista::get_url("index.php/camaras/index/{$camara->id}");
	$template = (new \FMT\Template(VISTAS_PATH.'/widgets/confirmacion.html', $vars_template,['CLEAN'=>false]));
	$vars_vista['CONTENT'] = "$template";
	$vista->add_to_var('vars',$vars_vista);

	return true;
