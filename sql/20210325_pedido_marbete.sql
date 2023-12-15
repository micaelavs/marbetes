USE `{{{db_app}}}`;

ALTER TABLE `{{{db_app}}}`.`pedido_marbete` 
ADD COLUMN `observaciones` TEXT NULL DEFAULT NULL AFTER `estado`;

USE `{{{db_log}}}`;

ALTER TABLE `{{{db_log}}}`.`pedido_marbete` 
ADD COLUMN `observaciones` TEXT NULL DEFAULT NULL AFTER `estado`;


DROP TRIGGER IF EXISTS `{{{db_app}}}`.`pedido_marbete_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `pedido_marbete_tg_alta` AFTER INSERT ON `pedido_marbete` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.pedido_marbete(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_pedido_marbete`,`id_empresa`,`id_imprenta`,`id_tipo_marbete`,`fecha_solicitud`,`cantidad`,`estado`,`observaciones`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_empresa,NEW.id_imprenta,NEW.id_tipo_marbete,NEW.fecha_solicitud,NEW.cantidad,NEW.estado,NEW.observaciones, NEW.borrado);
END$$
DELIMITER ;


DROP TRIGGER IF EXISTS `{{{db_app}}}`.`pedido_marbete_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `pedido_marbete_tg_modificacion` AFTER UPDATE ON `pedido_marbete` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.pedido_marbete(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_pedido_marbete`,`id_empresa`,`id_imprenta`,`id_tipo_marbete`,`fecha_solicitud`,`cantidad`,`estado`,`observaciones`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.id_empresa,NEW.id_imprenta,NEW.id_tipo_marbete,NEW.fecha_solicitud,NEW.cantidad, NEW.estado, NEW.observaciones, NEW.borrado);
END$$
DELIMITER ;

