<?php
session_start();
require_once __DIR__ . "/constantes.php";

define('APP_VERSION','1.0');

require_once BASE_PATH . '/vendor/autoload.php';
$config = FMT\Configuracion::instancia();
$config->cargar(BASE_PATH . '/config');
FMT\Mailer::init($config['email']['app_mailer'], $config['app']['ssl_verifypeer']);

FMT\FMT::init([
        'roles' => '\\App\\Modelo\\AppRoles',
        'id_modulo' => $config['app']['modulo']
]);

if(!isset($_SESSION['iu'])) {
        header("Location: {$config['app']['endpoint_panel']}");
        exit;
} else {
        \FMT\Logger::init($_SESSION['iu'], $config['app']['modulo'], $config['logs']['end_point_event'], $config['logs']['end_point_debug'], $config['logs']['debug'], ['CURLOPT_SSL_VERIFYPEER' => $config['app']['ssl_verifypeer']]);
        \FMT\Usuarios::init($config['app']['modulo'], $config['app']['endpoint_panel'].'/api.php', ['CURLOPT_SSL_VERIFYPEER' => $config['app']['ssl_verifypeer']]);
        \App\Modelo\CNRTApi::init($config['app']['endpoint_cnrt'],['CURLOPT_PROXY' => $config['app']['proxy']]);

        if (!isset($_SESSION['contador_login'])) {
                $datos  = [];
                $datos['session_data']  = $_SESSION['datos_usuario_logueado'];
                FMT\Logger::event('login', $datos);
                $_SESSION['contador_login']     = true;
        }
}