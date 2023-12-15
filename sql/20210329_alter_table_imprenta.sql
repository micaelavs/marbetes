USE `{{{db_app}}}`;

ALTER TABLE `{{{db_app}}}`.`imprenta`
ADD COLUMN `direccion` varchar(250) DEFAULT NULL AFTER `razon_social`;

USE `{{{db_log}}}`;

ALTER TABLE `{{{db_log}}}`.`imprenta`
ADD COLUMN `direccion` varchar(250) DEFAULT NULL AFTER `razon_social`;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`imprenta_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `imprenta_tg_alta` AFTER INSERT ON `imprenta` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.imprenta(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_imprenta`,`cuit`,`razon_social`,`direccion`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.cuit,NEW.razon_social,NEW.direccion,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`imprenta_tg_modificacion`;

DELIMITER $$

CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `imprenta_tg_modificacion` AFTER UPDATE ON `imprenta` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.imprenta(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_imprenta`,`cuit`,`razon_social`,`direccion`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.cuit,NEW.razon_social,NEW.direccion,NEW.borrado);
END$$
DELIMITER ;