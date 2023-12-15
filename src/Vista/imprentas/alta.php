<?php
namespace App\Vista;

$vars_vista['SUBTITULO']		=  'Alta Imprentas';
$vars_template['OPERACION']		=  'alta';
$vars_template['CUIT']			=  !empty($imprenta->cuit) ? $imprenta->cuit:'';
$vars_template['RAZON_SOCIAL']	=  !empty($imprenta->razon_social) ? $imprenta->razon_social: '';
$vars_template['DIRECCION']    =  !empty($imprenta->direccion) ? $imprenta->direccion : '';
$vars_template['INSCRIPCION_AFIP'] 	=  ($imprenta->inscripcion_en_afip) ? $imprenta->inscripcion_en_afip : '';
$vars_template['CHECKED_1']			= ($imprenta->inscripcion_en_afip == \App\Modelo\Imprenta::INSCRIPCION_AFIP) ? 'checked' : '';
$vars_template['MODELO_MARBETE'] 	= ($imprenta->modelo_de_marbete) ? $imprenta->modelo_de_marbete : '';
$vars_template['CHECKED_2']			= ($imprenta->modelo_de_marbete == \App\Modelo\Imprenta::MODELO_DE_MARBETE) ? 'checked' : '';
$vars_template['FECHA_ULTIMA_REVISION']		= !empty($temp = $imprenta->fecha_ultima_revision) ? $temp->format('d/m/Y') : '';
$vars_template['OBSERVACION']    =  !empty($imprenta->observacion) ? $imprenta->observacion : '';

$tipo_check	= json_encode([
		'inscripcion_afip' => \App\Modelo\Imprenta::INSCRIPCION_AFIP,
		'modelo_marbete' => \App\Modelo\Imprenta::MODELO_DE_MARBETE,
	], JSON_UNESCAPED_UNICODE);

$vars_template['CANCELAR'] 		= \App\Helper\Vista::get_url('index.php/imprentas/index');
$vars_vista['JS_FOOTER'][]['JS_SCRIPT']     = \App\Helper\Vista::get_url('/imprentas/imprentas.js');
$url_base	 = \App\Helper\Vista::get_url('index.php');

$vars_vista['JS'][]['JS_CODE']    = <<<JS
var \$url_base        = "{$url_base}";
var \$tipo_check = {$tipo_check};
JS;

$template = new \FMT\Template(VISTAS_PATH.'/templates/imprentas/alta.html', $vars_template, ['CLEAN'=>false]);
$vars_vista['CONTENT'] = "$template";
$vista->add_to_var('vars',$vars_vista);

return true;
?>