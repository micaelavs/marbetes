USE `{{{db_app}}}`;

CREATE TABLE `pedido_marbete` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) unsigned NOT NULL,
  `id_imprenta` int(11) unsigned NOT NULL,
  `id_tipo_marbete` int(11) unsigned NOT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `cantidad` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

ALTER TABLE `{{{db_app}}}`.`pedido_marbete` 
ADD COLUMN `estado` TINYINT(1) NOT NULL DEFAULT 1 AFTER `cantidad`;


ALTER TABLE `{{{db_app}}}`.`pedido_marbete` 
ADD COLUMN `borrado` TINYINT(1) NULL DEFAULT 0 AFTER `estado`;

USE `{{{db_log}}}`;

CREATE TABLE `pedido_marbete` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) UNSIGNED NOT NULL,
  `fecha_operacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `tipo_operacion` VARCHAR(1) NOT NULL,
  `id_pedido_marbete` INT(11) UNSIGNED NOT NULL,
  `id_empresa` INT(11) UNSIGNED NOT NULL,
  `id_imprenta` INT(11) UNSIGNED NOT NULL,
  `id_tipo_marbete` INT(11) UNSIGNED NOT NULL,
  `fecha_solicitud` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `cantidad` INT(11) NOT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`));


ALTER TABLE `{{{db_log}}}`.`pedido_marbete` 
ADD COLUMN `borrado` TINYINT(1) NULL DEFAULT 0 AFTER `estado`;


DROP TRIGGER IF EXISTS `{{{db_app}}}`.`pedido_marbete_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `pedido_marbete_tg_alta` AFTER INSERT ON `pedido_marbete` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.pedido_marbete(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_pedido_marbete`,`id_empresa`,`id_imprenta`,`id_tipo_marbete`,`fecha_solicitud`,`cantidad`,`estado`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_empresa,NEW.id_imprenta,NEW.id_tipo_marbete,NEW.fecha_solicitud,NEW.cantidad,NEW.estado, NEW.borrado);
END$$
DELIMITER ;


DROP TRIGGER IF EXISTS `{{{db_app}}}`.`pedido_marbete_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `pedido_marbete_tg_modificacion` AFTER UPDATE ON `pedido_marbete` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.pedido_marbete(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_pedido_marbete`,`id_empresa`,`id_imprenta`,`id_tipo_marbete`,`fecha_solicitud`,`cantidad`,`estado`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.id_empresa,NEW.id_imprenta,NEW.id_tipo_marbete,NEW.fecha_solicitud,NEW.cantidad, NEW.estado, NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_log}}}`.`pedido_marbete_tg_insert`;

DELIMITER $$
USE `{{{db_log}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `pedido_marbete_tg_insert` AFTER INSERT ON `pedido_marbete` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'pedido_marbete');
END$$
DELIMITER ;
