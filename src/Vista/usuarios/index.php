<?php
	namespace App\Vista;
	$vars_vista['SUBTITULO'] = 'Gestion de Usuarios del Sistema.';
	$vars['TITULOS'] = [
			['TITULO' => 'Nombre y apellido'],
			['TITULO' => 'Usuario'],
			['TITULO' => 'Rol'],
			['TITULO' => 'Acciones']
		];
	foreach ($usuarios as $usuario) {
			$vars['ROW'][] = ['COL' => [
				['CONT' => $usuario->nombre.' '.$usuario->apellido],
				['CONT' => $usuario->user],
				['CONT' => $usuario->rol_nombre],
				['CONT' => '<span class="acciones">
				<a href="'.\App\Helper\Vista::get_url("index.php/usuarios/modificar/{$usuario->idUsuario}").'" data-toggle="tooltip" data-placement="top" data-id="'.$usuario->idUsuario.'" title="Ver/Modificar" class="dis" data-toggle="modal"><i class="fa fa-eye"></i><i class="fa fa-pencil"></i></a>
				<a href="'.\App\Helper\Vista::get_url("index.php/usuarios/baja/{$usuario->idUsuario}").'" class="borrar" data-user="'.$usuario->nombre.'" data-toggle="tooltip" data-placement="top" title="Eliminar" target="_self"><i class="fa fa-trash"></i></a>

				</span>']
				]
			];
		}
	$vars_vista['CSS_FILES'][]	= ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'].'/datatables/1.10.12/datatables.min.css'];
	$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/1.10.12/datatables.min.js"];
	$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/defaults.js"];

	$html = (new \FMT\Template(VISTAS_PATH.'/templates/tabla.html',$vars));
	$vars['CLASS_COL'] = 'col-md-12';
	$vars['BOTON_ACCION'][] = ['HTTP'=> \App\Helper\Vista::get_url('index.php'),'ACCION' => 'alta','CONTROL' => 'usuarios','CLASS'=>'btn-primary','NOMBRE' => 'NUEVO'];

	$html2 = (new \FMT\Template(VISTAS_PATH.'/widgets/botonera.html',$vars));

	$vars_vista['CONTENT'] = "{$html}{$html2}";
	$vista->add_to_var('vars',$vars_vista);
	return true;