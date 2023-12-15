<?php

use \FMT\Helper\Template;
use \Dompdf\Dompdf;
use \Dompdf\Options;

setlocale(LC_ALL, 'spanish');
$fecha_actual = strftime('%d de %B del %Y');
$vars_template                    = [];

$vars_template    = [
   'IMG_PATH'              => \App\Helper\Vista::get_url("img").'/logo_ministerio_grande.png', 
   'IMG_PATH_2'            => \App\Helper\Vista::get_url("img").'/reconstruccion_argentina.png', 
   'TITLE_FILE'            => $file_nombre,
   'BASE_URL'              => BASE_PATH,
   'FECHA_SOLICITUD'       => ($pedido->fecha_solicitud instanceof \DateTime) ? $pedido->fecha_solicitud ->format('d/m/Y') : '',
   'FECHA_AUTORIZADO'      => ($pedido->fecha_autorizacion instanceof \DateTime) ? $pedido->fecha_autorizacion->format('d/m/Y') : '',
   'FECHA_ACTUAL'          => $fecha_actual,
   'EMPRESA_NOMBRE'        => \FMT\Helper\Arr::get($empresas, $pedido->id_empresa) ? $empresas[$pedido->id_empresa]['nombre'] : '',
   'EMPRESA_CUIT'          => \FMT\Helper\Arr::get($empresas, $pedido->id_empresa) ? $empresas[$pedido->id_empresa]['cuit'] : '',
   'CNRT'                  => \FMT\Helper\Arr::get($empresas, $pedido->id_empresa) ? $empresas[$pedido->id_empresa]['codigo_cnrt'] : '',
   'EMPRESA_DIRECCION'     => \FMT\Helper\Arr::get($empresas, $pedido->id_empresa) ? $empresas[$pedido->id_empresa]['direccion'] : '',
   'NOMBRE_APODERADO'      => \FMT\Helper\Arr::get($empresas, $pedido->id_empresa) ? $empresas[$pedido->id_empresa]['nombre_apoderado'] : '',
   'DNI_APODERADO'         =>\FMT\Helper\Arr::get($empresas, $pedido->id_empresa) ? $empresas[$pedido->id_empresa]['dni_apoderado'] : '',
   'IMPRENTA_NOMBRE'       => \FMT\Helper\Arr::get($imprentas, $pedido->id_imprenta) ? $imprentas[$pedido->id_imprenta]['nombre'] : '',
   'IMPRENTA_DIRECCION'    => \FMT\Helper\Arr::get($imprentas, $pedido->id_imprenta) ? $imprentas[$pedido->id_imprenta]['direccion'] : '',
   'CANTIDAD_SOLICITADA'   => ($pedido->cantidad) ? $pedido->cantidad : '',
   'CANTIDAD_AUTORIZADA'   => ($pedido->cantidad_autorizada) ? $pedido->cantidad_autorizada : '',
   'DESCRIPCION'           => \FMT\Helper\Arr::get($tipos_marbetes, $pedido->id_tipo_marbete) ? $tipos_marbetes[$pedido->id_tipo_marbete]['nombre'] : '',
   'TIPO_MARBETE'          => \FMT\Helper\Arr::get($tipos_marbetes, $pedido->id_tipo_marbete) ? $tipos_marbetes[$pedido->id_tipo_marbete]['tipo'] : '',
   'ID_MARBETE'            => \FMT\Helper\Arr::get($tipos_marbetes, $pedido->id_tipo_marbete) ? $tipos_marbetes[$pedido->id_tipo_marbete]['id'] : '',
   'LETRAS_INICIO'         => ($asignacion_marbete['letras_inicio']) ? $asignacion_marbete['letras_inicio'] : '',
   'LETRAS_FIN'            => ($asignacion_marbete['letras_fin']) ? $asignacion_marbete['letras_fin'] : '',
   'DIGITOS_INICIO'        => ($asignacion_marbete['digitos_inicio']) ? $asignacion_marbete['digitos_inicio'] : '' ,
   'DIGITOS_FIN'           => ($asignacion_marbete['digitos_fin']) ? $asignacion_marbete['digitos_fin'] : ''
   //'NRO_INICIAL_LDT'     => 'M01074216',
   //'NRO_FINAL_LDT'       => 'M01600007',  
];

$vars_vista['CONTENT'] = "";
$vista->add_to_var('vars',$vars_vista);

$template_html  = new \FMT\Template(TEMPLATE_PATH . '/pedidos_marbete/pedido_autorizado.html', $vars_template, ['CLEAN' => true]);

$options = new Options();
$options->set('isRemoteEnabled',true);      
$dompdf = new Dompdf( $options );

$dompdf    = new Dompdf($options);
$dompdf->loadHtml($template_html);
$dompdf->setPaper('A4');
$dompdf->render();
$dompdf->stream($file_nombre);
exit;

