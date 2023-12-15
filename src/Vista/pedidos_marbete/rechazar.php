<?php
use \FMT\Helper\Template;
use \FMT\Helper\Arr;
use FMT\Vista;
	$vars_vista['SUBTITULO'] = 'Rechazo de Pedido Marbetes';
	$vars_template['CONTROL'] = 'Pedido Marbete';
	$vars_template['ARTICULO'] = 'el';
	$vars_template['TEXTO_AVISO'] = 'Rechazar ';			
	$vars_template['NOMBRE'] = 'de la Empresa: '.$empresa->razon_social.'. Imprenta: ' .$imprenta->razon_social.'. Tipo de Marbete: '.$tipo_marbete->tipo;
	$vars_template['TEXTO_EXTRA'] = '.<br/>Al rechazarlo, podrá visualizarlo en el Listado de Pedidos con estado Rechazado';
	$vars_template['CANCELAR'] = \App\Helper\Vista::get_url("index.php/Pedidos_marbete/index/");
	$template = (new \FMT\Template(VISTAS_PATH.'/widgets/rechazo.html', $vars_template,['CLEAN'=>false]));
	$vars_vista['CONTENT'] = "$template";
	$vista->add_to_var('vars',$vars_vista);

	return true;
