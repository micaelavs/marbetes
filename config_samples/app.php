<?php
return [
        'app' => [
                'dev'					=> true, // Estado del desarrollo
                'modulo'				=> '', // Numero del modulo
                'title'					=> '', // Nombre del Modulo,
                'titulo_pantalla'		=> '',		//ej. Sigla de la aplicacion       
                'endpoint_panel'		=> '', //ej. 'https://qa-panel.dev.transporte.gob.ar'
                'endpoint_cdn'			=> '', //ej. 'https://gyulan-cdn.dev.transporte.gob.ar',
                'id_usuario_sistema'	=> '99999', //Por convencion se define un numero alto para identificar operaciones automaticas del sistema.  
                'ssl_verifypeer'		=> false,   //Flag para la verificacion de certificados SSL
                'endpoint_cnrt'                 => '' //'https://api.cnrt.gob.ar/seop/public/v1',

        ]
];