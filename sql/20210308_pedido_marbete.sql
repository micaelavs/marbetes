USE `{{{db_log}}}`;

ALTER TABLE `{{{db_log}}}`.`_registros_abm` 
CHANGE COLUMN `tabla_nombre` `tabla_nombre` ENUM('camara', 'empresa', 'imprenta', 'tipo_marbete', 'usuarios', 'pedido_marbete') NULL DEFAULT NULL ;

