USE `{{{db_app}}}`;

CREATE TABLE `rango_imprenta` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_imprenta` int(11) unsigned NOT NULL,
  `rango` text NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`,`id_imprenta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

USE `{{{db_log}}}`;

CREATE TABLE `rango_imprenta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned NOT NULL,
  `fecha_operacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo_operacion` varchar(1) NOT NULL,
  `id_rango_imprenta` int(11) unsigned NOT NULL,
  `id_imprenta` int(11) unsigned NOT NULL,
  `rango` text NOT NULL,
  `borrado` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `{{{db_log}}}`.`_registros_abm` 
CHANGE COLUMN `tabla_nombre` `tabla_nombre` ENUM('camara', 'empresa', 'imprenta', 'tipo_marbete', 'usuarios', 'pedido_marbete', 'autorizacion', 'asignacion_marbete', 'rango_imprenta') NULL DEFAULT NULL ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`rango_imprenta_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `rango_imprenta_tg_alta` AFTER INSERT ON `rango_imprenta` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.rango_imprenta(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_rango_imprenta`,`id_imprenta`,`rango`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_imprenta,NEW.rango,NEW.borrado);
END$$
DELIMITER ;


DROP TRIGGER IF EXISTS `{{{db_app}}}`.`rango_imprenta_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `rango_imprenta_tg_modificacion` AFTER UPDATE ON `rango_imprenta` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.rango_imprenta(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_rango_imprenta`,`id_imprenta`,`rango`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.id_imprenta,NEW.rango,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_log}}}`.`rango_imprenta_tg_insert`;

DELIMITER $$
USE `{{{db_log}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `rango_imprenta_tg_insert` AFTER INSERT ON `rango_imprenta` FOR EACH ROW BEGIN
		INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'rango_imprenta');
END$$
DELIMITER ;
