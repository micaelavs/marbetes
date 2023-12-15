<?php

namespace App\Vista;

$vars_vista['SUBTITULO'] = 'Baja de Tipo Marbete.';
$vars['CONTROL'] = 'Tipo de Marbete';
$vars['ARTICULO'] = 'el';
$vars['TEXTO_AVISO'] = 'Dará de baja ';
$vars['NOMBRE'] = $tipo_marbetes->tipo;
$vars['CANCELAR'] = \App\Helper\Vista::get_url('index.php/tipo_marbetes/index');

$template = (new \FMT\Template(VISTAS_PATH . '/widgets/confirmacion.html', $vars));
$vars_vista['CONTENT'] = "$template";
$vista->add_to_var('vars', $vars_vista);

return true;
