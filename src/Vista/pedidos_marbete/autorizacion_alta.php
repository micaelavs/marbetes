<?php
use \FMT\Helper\Template;
use \FMT\Helper\Arr;
use FMT\Vista;

	$config	= \FMT\Configuracion::instancia();
	$vars_vista['SUBTITULO']		= 'Autorización Pedido de Marbetes';
	//$vars_vista['CSS_FILES'][]		= ['CSS_FILE'   => $config['app']['endpoint_cdn']."/js/select2/css/select2.min.css"];
    //$vars_vista['JS_FILES'][]		= ['JS_FILE'    => $config['app']['endpoint_cdn']."/js/select2/js/select2.full.min.js"];
    $vars_template['OPERACION']				= 'alta';
    $vars_template['EMPRESA']				= \FMT\Helper\Arr::get($empresas,$pedido->id_empresa) ? $empresas[$pedido->id_empresa]['nombre'] :'';
    $vars_template['IMPRENTA']				= \FMT\Helper\Arr::get($imprentas,$pedido->id_imprenta) ? $imprentas[$pedido->id_imprenta]['nombre'] :'';
    $vars_template['TIPO_MARBETE']			= \FMT\Helper\Arr::get($tipos_marbetes,$pedido->id_tipo_marbete) ? $tipos_marbetes[$pedido->id_tipo_marbete]['tipo'] :'';
    $vars_template['FECHA_SOLICITUD']		= !empty($temp = $pedido->fecha_solicitud) ? $temp->format('d/m/Y') : '';
    $vars_template['CANTIDAD_SOLICITADA']	= $pedido->cantidad;	
    $vars_template['FECHA_AUTORIZACION']	= !empty($temp = $pedido->fecha_autorizacion) ? $temp->format('d/m/Y') : '';
    $vars_template['CANTIDAD_AUTORIZADA']	= $pedido->cantidad_autorizada;	
    $vars_template['DISABLED'] 		=  '';
    $vars_template['CANCELAR'] = \App\Helper\Vista::get_url('index.php/Pedidos_marbete/index'); 
	$template = (new \FMT\Template(VISTAS_PATH.'/templates/pedidos_marbete/autorizacion_alta.html', $vars_template,['CLEAN'=>false]));
	$vars_vista['CONTENT'] = "$template";
	$vars_vista['JS_FOOTER'][]['JS_SCRIPT']     = \App\Helper\Vista::get_url('/pedidos_marbete/pedidos_marbete_autorizados.js');
	$base_url = \App\Helper\Vista::get_url('index.php');
	
	$vars_vista['JS'][]['JS_CODE']	= <<<JS
	var \$base_url = "{$base_url}";
JS;
	$vista->add_to_var('vars',$vars_vista);
	return true;
