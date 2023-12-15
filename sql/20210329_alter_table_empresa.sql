USE `{{{db_app}}}`;

ALTER TABLE `{{{db_app}}}`.`empresa`
ADD COLUMN `direccion` varchar(256) DEFAULT NULL AFTER `id_camara`;

ALTER TABLE `{{{db_app}}}`.`empresa`
ADD COLUMN `nombre_apoderado` varchar(256) DEFAULT NULL AFTER `direccion`;

ALTER TABLE `{{{db_app}}}`.`empresa`
ADD COLUMN `dni_apoderado` int(10) DEFAULT NULL AFTER `nombre_apoderado`;

USE `{{{db_log}}}`;

ALTER TABLE `{{{db_log}}}`.`empresa`
ADD COLUMN `direccion` varchar(256) DEFAULT NULL AFTER `id_camara`;

ALTER TABLE `{{{db_log}}}`.`empresa`
ADD COLUMN `nombre_apoderado` varchar(256) DEFAULT NULL AFTER `direccion`;

ALTER TABLE `{{{db_log}}}`.`empresa`
ADD COLUMN `dni_apoderado` int(10) DEFAULT NULL AFTER `nombre_apoderado`;


DROP TRIGGER IF EXISTS `{{{db_app}}}`.`empresa_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `empresa_tg_alta` AFTER INSERT ON  `empresa` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.empresa(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_empresa`,`cuit`,`razon_social`,`codigo_cnrt`,`id_camara`,`direccion`,`nombre_apoderado`,`dni_apoderado`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.cuit,NEW.razon_social,NEW.codigo_cnrt,NEW.id_camara,NEW.direccion,NEW.nombre_apoderado,NEW.dni_apoderado,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`empresa_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `empresa_tg_modificacion` AFTER UPDATE ON `empresa` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.empresa(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_empresa`,`cuit`,`razon_social`,`codigo_cnrt`,`id_camara`,`direccion`,`nombre_apoderado`,`dni_apoderado`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.cuit,NEW.razon_social,NEW.codigo_cnrt,NEW.id_camara,NEW.direccion,NEW.nombre_apoderado,NEW.dni_apoderado,NEW.borrado);
END$$
DELIMITER ;
