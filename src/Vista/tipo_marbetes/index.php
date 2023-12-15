<?php
use \FMT\Helper\Template;
/**
 * $vars_template |Variable de configuracion para el template de la funcionalidad que se esta desarrollando.
 * $vars_vista  |Variable de configuracion para el template general. Llega a la vista por medio de la variable "vista"
 * propagada por la clase Vista.
 **/
$vars_template = [];
$vars_vista['SUBTITULO'] = 'Tipos de Marbete';
$vars_template['CLASS'] = 'tipo_marbetes';
$vars_vista['JS'][]['JS_CODE']  = <<<JS
  \$data_table_init = '{$vars_template['CLASS']}';
JS;

$vars_template['TITULOS'] = [
    ['TITULO' =>'Tipo','DATA' => 'data-target="tipo" data-width="10%" data-orderable="true"'],
    ['TITULO' =>'Descripcion', 'DATA' => 'data-target="descripcion" data-width="10%"'],
    ['TITULO' =>'Acciones', 'DATA' => 'data-target="tipo_marbetes" data-orderable="false" data-width="4%"']
];


foreach ($tipo_marbetes as $key => $elem) {
    if (empty($elem->id)) {
        continue;
    }
    $modifica = '';
    $modifica = '<a href="' . \App\Helper\Vista::get_url("index.php/Tipo_marbetes/modificar/{$elem->id}") . '" data-toggle="tooltip" data-placement="top" data-id="" title="Modificar" data-toggle="modal"><i class="fa fa-pencil"></i></a>';
    $elimina = '<a href="' . \App\Helper\Vista::get_url("index.php/Tipo_marbetes/baja/{$elem->id}") . '" data-toggle="tooltip" data-placement="top" data-id="" title="Baja" data-toggle="modal"><i class="fa fa-trash"></i></a>';
    $vars_template['ROW'][] =
        [
            'COL' => [
                ['CONT' => $elem->tipo],
                ['CONT' => $elem->descripcion],
                ['CONT' => '<span class="acciones">' . $modifica . $elimina . '</span> ']
            ],
        ];
}

$endpoint_cdn = $vista->getSystemConfig()['app']['endpoint_cdn'];
$vars_vista['JS_FOOTER'][]['JS_SCRIPT']     = \App\Helper\Vista::get_url('/tipo_marbetes/tipo_marbetes.js');
$vars_vista['JS_FOOTER'][]['JS_SCRIPT']     =  \App\Helper\Vista::get_url('script.js');
$vars_vista['CSS_FILES'][]  = ['CSS_FILE'   => $endpoint_cdn . '/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][] = ['JS_FILE'      => $endpoint_cdn . "/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][] = ['JS_FILE'      => $endpoint_cdn . "/datatables/defaults.js"];

$vars_vista['JS'][]['JS_CODE']  = <<<JS
var \$endpoint_cdn = '{$endpoint_cdn}';
JS;
$vars_template['BOTON_NUEVO'][] = ['LINK' => \App\Helper\Vista::get_url("index.php/Tipo_marbetes/alta")];
$tabla_vars_template  = $vars_template;
$vars_template['TABLA'][] =  new \FMT\Template(TEMPLATE_PATH . '/tabla.html', $tabla_vars_template, ['CLEAN' => false]);
$tipo_listar = new \FMT\Template(TEMPLATE_PATH . '/tipo_marbetes/index.html', $vars_template, ['CLEAN' => false]);
$vars_vista['CONTENT'] = "{$tipo_listar}";
$vista->add_to_var('vars', $vars_vista);
return true;

?>