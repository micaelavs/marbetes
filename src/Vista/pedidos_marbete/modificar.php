<?php
use \FMT\Helper\Template;
use \FMT\Helper\Arr;
use FMT\Vista;

	$config	= \FMT\Configuracion::instancia();
	$vars_vista['SUBTITULO']	= 'Modificar Pedido de Marbetes';
	$vars_vista['CSS_FILES'][]    = ['CSS_FILE'   => $config['app']['endpoint_cdn']."/js/select2/css/select2.min.css"];
    $vars_vista['JS_FILES'][]     = ['JS_FILE'    => $config['app']['endpoint_cdn']."/js/select2/js/select2.full.min.js"];
    $vars_template['OPERACION'] = 'modificacion';
    $vars_template['EMPRESA']				= \FMT\Helper\Template::select_block($empresas,$pedido->id_empresa);
    $vars_template['IMPRENTA']				= \FMT\Helper\Template::select_block($imprentas,$pedido->id_imprenta);
    $vars_template['TIPO_MARBETE']			= \FMT\Helper\Template::select_block($tipos_marbetes,$pedido->id_tipo_marbete);
    $vars_template['FECHA_SOLICITUD']		= !empty($temp = $pedido->fecha_solicitud) ? $temp->format('d/m/Y') : $pedido->fecha_solicitud;
    $vars_template['CANTIDAD']				= $pedido->cantidad;	
     $vars_template['CUIT']					= \FMT\Helper\Arr::get($empresas,$pedido->id_empresa) ? $empresas[$pedido->id_empresa]['cuit'] :'';
    $vars_template['CODIGO_CNRT']			= \FMT\Helper\Arr::get($empresas,$pedido->id_empresa) ? $empresas[$pedido->id_empresa]['codigo_cnrt'] :'';
    $vars_template['CANCELAR'] = \App\Helper\Vista::get_url('index.php/Pedidos_marbete/index'); 
	$template = (new \FMT\Template(VISTAS_PATH.'/templates/pedidos_marbete/alta.html', $vars_template,['CLEAN'=>false]));
	$vars_vista['JS_FOOTER'][]['JS_SCRIPT']     = \App\Helper\Vista::get_url('/pedidos_marbete/pedidos_marbete.js');
	$vars_vista['CONTENT'] = "$template";
	$base_url = \App\Helper\Vista::get_url('index.php');
	$vars_vista['JS'][]['JS_CODE']	= <<<JS
	var \$base_url = "{$base_url}";
JS;
	$vista->add_to_var('vars',$vars_vista);

	return true;
?>
