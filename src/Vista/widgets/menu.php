<?php
use \App\Modelo\AppRoles;
	$menu		= new \FMT\Menu();
	$config = FMT\Configuracion::instancia();
	if ($config['app']['dev']) {
		$menu->activar_dev();
	}
	//-----------------------------------------------------------//
	if(
		AppRoles::puede('Usuarios','index')
	|| AppRoles::puede('Imprentas', 'index')
	|| AppRoles::puede('Tipo_marbetes', 'index')
	|| AppRoles::puede('Empresas', 'index')
	|| AppRoles::puede('Camaras','index')
	|| AppRoles::puede('Pedidos_marbete', 'index')
	|| AppRoles::puede('Auditorias', 'index')
	) {
		$opcion2	= $menu->agregar_opcion('Gestion');
	}
	//-----------------------------------------------------------//
	//-----------------------------------------------------------//
	if(AppRoles::puede('Usuarios','index')) {
		//$opcion2->agregar_titulo('Administración del sistema', \FMT\Opcion::COLUMNA1);
		$opcion2->agregar_link('Usuarios', \App\Helper\Vista::get_url('index.php/usuarios/index'), \FMT\Opcion::COLUMNA1);
	}
	if(AppRoles::puede('Imprentas','index')) {
		$opcion2->agregar_titulo('Administración del Imprenta', \FMT\Opcion::COLUMNA1);
		$opcion2->agregar_link('Imprenta', \App\Helper\Vista::get_url('index.php/imprentas/index'), \FMT\Opcion::COLUMNA1);
	}
	if(AppRoles::puede('Camaras','index')) {
		$opcion2->agregar_titulo('Administración de Cámara', \FMT\Opcion::COLUMNA1);
		$opcion2->agregar_link('Cámara', \App\Helper\Vista::get_url('index.php/camaras/index'), \FMT\Opcion::COLUMNA1);
	}
	if(AppRoles::puede('Empresas','index')) {
		$opcion2->agregar_titulo('Administración de Empresa', \FMT\Opcion::COLUMNA1);
		$opcion2->agregar_link('Empresa', \App\Helper\Vista::get_url('index.php/empresas/index'), \FMT\Opcion::COLUMNA1);
	}
	if (AppRoles::puede('Tipo_marbetes', 'index')) {
	$opcion2->agregar_titulo('Administración de Tipo de Marbete', \FMT\Opcion::COLUMNA1);
	$opcion2->agregar_link('Tipo de Marbete', \App\Helper\Vista::get_url('index.php/Tipo_marbetes/index'), \FMT\Opcion::COLUMNA1);
	}
	if (AppRoles::puede('Pedidos_marbete', 'index')) {
	$opcion2->agregar_titulo('Administración de Pedidos de Marbetes', \FMT\Opcion::COLUMNA1);
	$opcion2->agregar_link('Pedido de Marbetes', \App\Helper\Vista::get_url('index.php/Pedidos_marbete/index'), \FMT\Opcion::COLUMNA1);
	}
	if (AppRoles::puede('Auditorias', 'index')) {
		$opcion2->agregar_titulo('Administración de Reporte', \FMT\Opcion::COLUMNA1);
		$opcion2->agregar_link('Reporte', \App\Helper\Vista::get_url('index.php/auditorias/index'), \FMT\Opcion::COLUMNA1);
	}
	//-----------------------------------------------------------//
	$menu->agregar_manual('elmanualestaaca.html');
	$menu->agregar_salir($config['app']['endpoint_panel'].'/logout.php');

	$vars['CABECERA'] = "{$menu}";
	$vista->add_to_var('vars', $vars);
	return true;