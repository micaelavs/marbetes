USE `{{{db_app}}}`;

CREATE TABLE `autorizacion` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pedido` INT(11) UNSIGNED NOT NULL,
  `fecha_autorizacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `cantidad_autorizada` INT(11) NOT NULL,
  `borrado` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`));

USE `{{{db_log}}}`;

CREATE TABLE `autorizacion` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) UNSIGNED NOT NULL,
  `fecha_operacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `tipo_operacion` VARCHAR(1) NOT NULL,
  `id_autorizacion` INT(11) UNSIGNED NOT NULL,
  `id_pedido` INT(11) UNSIGNED NOT NULL,
  `fecha_autorizacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `cantidad_autorizada` INT(11) NOT NULL,
  `borrado` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`));


ALTER TABLE `{{{db_log}}}`.`_registros_abm` 
CHANGE COLUMN `tabla_nombre` `tabla_nombre` ENUM('camara', 'empresa', 'imprenta', 'tipo_marbete', 'usuarios', 'pedido_marbete', 'autorizacion') NULL DEFAULT NULL ;


USE `{{{db_app}}}`;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`autorizacion_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `autorizacion_tg_alta` AFTER INSERT ON `autorizacion` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.autorizacion(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_autorizacion`,`id_pedido`,`fecha_autorizacion`,`cantidad_autorizada`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_pedido,NEW.fecha_autorizacion,NEW.cantidad_autorizada,NEW.borrado);
END$$
DELIMITER ;


DROP TRIGGER IF EXISTS `{{{db_app}}}`.`autorizacion_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `autorizacion_tg_modificacion` AFTER UPDATE ON `autorizacion` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.autorizacion(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_autorizacion`,`id_pedido`,`fecha_autorizacion`,`cantidad_autorizada`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.id_pedido,NEW.fecha_autorizacion,NEW.cantidad_autorizada,NEW.borrado);
END$$
DELIMITER ;

USE `{{{db_log}}}`;

DROP TRIGGER IF EXISTS `{{{db_log}}}`.`autorizacion_tg_insert`;

DELIMITER $$
USE `{{{db_log}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `autorizacion_tg_insert` AFTER INSERT ON `autorizacion` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'autorizacion');
END$$
DELIMITER ;
