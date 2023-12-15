USE `{{{db_app}}}`;

CREATE TABLE `asignacion_marbete` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) unsigned NOT NULL,
  `letras_inicio` varchar(45) NOT NULL,
  `letras_fin` varchar(45) NOT NULL,
  `digitos_inicio` INT(6) UNSIGNED ZEROFILL NOT NULL,
  `digitos_fin` INT(6) UNSIGNED ZEROFILL NOT NULL,
  `borrado` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

USE `{{{db_log}}}`;

CREATE TABLE `asignacion_marbete` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned NOT NULL,
  `fecha_operacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo_operacion` varchar(1) NOT NULL,
  `id_asignacion_marbete` int(11) unsigned NOT NULL,
  `id_pedido` int(11) unsigned NOT NULL,
  `letras_inicio` varchar(45) NOT NULL,
  `letras_fin` varchar(45) NOT NULL,
  `digitos_inicio` INT(6) ZEROFILL NOT NULL,
  `digitos_fin` INT(6) ZEROFILL NOT NULL,
  `borrado` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`asignacion_marbete_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `asignacion_marbete_tg_alta` AFTER INSERT ON `asignacion_marbete` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.asignacion_marbete(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_asignacion_marbete`,`id_pedido`,`letras_inicio`,`letras_fin`,`digitos_inicio`, `digitos_fin`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_pedido,NEW.letras_inicio,NEW.letras_fin,NEW.digitos_inicio,NEW.digitos_fin,NEW.borrado);
END$$
DELIMITER ;


DROP TRIGGER IF EXISTS `{{{db_app}}}`.`asignacion_marbete_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `asignacion_marbete_tg_modificacion` AFTER UPDATE ON `asignacion_marbete` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.asignacion_marbete(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_asignacion_marbete`,`id_pedido`,`letras_inicio`,`letras_fin`,`digitos_inicio`,`digitos_fin`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.id_pedido,NEW.letras_inicio,NEW.letras_fin,NEW.digitos_inicio, NEW.digitos_fin,NEW.borrado);
END$$
DELIMITER ;

ALTER TABLE `{{{db_log}}}`.`_registros_abm` 
CHANGE COLUMN `tabla_nombre` `tabla_nombre` ENUM('camara', 'empresa', 'imprenta', 'tipo_marbete', 'usuarios', 'pedido_marbete', 'autorizacion', 'asignacion_marbete') NULL DEFAULT NULL ;



