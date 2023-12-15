
/*db_log*/
USE `marbetes_log`;

ALTER TABLE `imprenta` 
ADD COLUMN `fecha_ultima_revision` DATETIME NOT NULL AFTER `direccion`,
ADD COLUMN `inscripcion_en_afip` TINYINT(4) NULL DEFAULT 0 AFTER `fecha_ultima_revision`,
ADD COLUMN `modelo_de_marbete` TINYINT(4) NULL DEFAULT 0 AFTER `inscripcion_en_afip`,
ADD COLUMN `observacion` TEXT NULL DEFAULT NULL AFTER `modelo_de_marbete`;


USE `marbetes`;

/*db*/
ALTER TABLE `imprenta` 
ADD COLUMN `fecha_ultima_revision` DATETIME NOT NULL AFTER `direccion`,
ADD COLUMN `inscripcion_en_afip` TINYINT(4) NULL DEFAULT 0 AFTER `fecha_ultima_revision`,
ADD COLUMN `modelo_de_marbete` TINYINT(4) NULL DEFAULT 0 AFTER `inscripcion_en_afip`,
ADD COLUMN `observacion` TEXT NULL DEFAULT NULL AFTER `modelo_de_marbete`;

DROP TRIGGER IF EXISTS `marbetes`.`imprenta_tg_alta`;

DELIMITER $$
USE `marbetes`$$
CREATE DEFINER=`marbetes`@`%` TRIGGER `imprenta_tg_alta` AFTER INSERT ON `imprenta` FOR EACH ROW
BEGIN
INSERT INTO marbetes_log.imprenta(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_imprenta`,`cuit`,`razon_social`,`direccion`,`fecha_ultima_revision`,`inscripcion_en_afip`,`modelo_de_marbete`,`observacion`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.cuit,NEW.razon_social,NEW.direccion,NEW.fecha_ultima_revision, NEW.inscripcion_en_afip,NEW.modelo_de_marbete,NEW.observacion,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `marbetes`.`imprenta_tg_modificacion`;

DELIMITER $$
USE `marbetes`$$
CREATE DEFINER=`marbetes`@`%` TRIGGER `imprenta_tg_modificacion` AFTER UPDATE ON `imprenta` FOR EACH ROW
BEGIN
INSERT INTO marbetes_log.imprenta(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_imprenta`,`cuit`,`razon_social`,`direccion`,`fecha_ultima_revision`,`inscripcion_en_afip`,`modelo_de_marbete`,`observacion`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.cuit,NEW.razon_social,NEW.direccion,NEW.fecha_ultima_revision, NEW.inscripcion_en_afip,NEW.modelo_de_marbete,NEW.observacion,NEW.borrado);
END$$
DELIMITER ;
