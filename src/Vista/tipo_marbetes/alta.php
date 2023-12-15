<?php
namespace App\Vista;

$vars_vista['SUBTITULO']		=  'Alta Tipo Marbete';
$vars_template['OPERACION']		=  'alta';
$vars_template['TIPO']			=  $tipo_marbetes->tipo;
$vars_template['DESCRIPCION']	=  $tipo_marbetes->descripcion;
$vars_template['CANCELAR'] 		= \App\Helper\Vista::get_url('index.php/tipo_marbetes/index');
$template = new \FMT\Template(VISTAS_PATH. '/templates/tipo_marbetes/alta.html', $vars_template, ['CLEAN'=>false]);
$vars_vista['CONTENT'] = "$template";
$vista->add_to_var('vars',$vars_vista);

return true;
