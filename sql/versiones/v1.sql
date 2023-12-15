#SIEMPRE SE ACTUALIZARA LA VERSIÓN DE LAS DOS DBs AUNQUE NO SE HAGAN CAMBIOS.
#REMPLAZAR ANTES DE EJECUTAR
# {{{user_mysql}}}  = REEMPLAZAR POR NOMBRE USER QUE EJECUTA.
# {{{db_log}}}      = REEMPLAZAR POR NOMBRE DB LOG.
# {{{db_app}}}      = REEMPLAZAR POR NOMBRE DB APP.
CREATE DATABASE  IF NOT EXISTS `{{{db_app}}}` DEFAULT CHARACTER SET utf8 ;
USE `{{{db_app}}}`;

CREATE TABLE IF NOT EXISTS  `camara` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) DEFAULT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;                                   

CREATE TABLE IF NOT EXISTS  `empresa` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cuit` DECIMAL(11,0) NULL DEFAULT NULL,
  `razon_social` varchar(250) DEFAULT NULL,
  `codigo_cnrt` int(20) DEFAULT NULL,
  `id_camara` int(15) DEFAULT NULL,
  `borrado` int(2) NULL DEFAULT 0,
  PRIMARY KEY (`id`)
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `imprenta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cuit` DECIMAL(11,0) DEFAULT NULL,
  `razon_social` varchar(255) DEFAULT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `tipo_marbete` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` varchar(40) DEFAULT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `borrado` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE DATABASE  IF NOT EXISTS `{{{db_log}}}` DEFAULT CHARACTER SET utf8 ;
USE `{{{db_log}}}`;

--
-- Table structure for table `_registros_abm`
--
CREATE TABLE IF NOT EXISTS  `_registros_abm` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned DEFAULT NULL,
  `fecha_operacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo_operacion` char(1) DEFAULT NULL,
  `id_tabla` bigint(20) unsigned NOT NULL,
  `tabla_nombre` enum('camara','empresa','imprenta','tipo_marbete','usuarios') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fecha_operacion` (`fecha_operacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS  `camara` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned NOT NULL,
  `fecha_operacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `tipo_operacion` varchar(1) NOT NULL,
  `id_camara` int(10) unsigned NOT NULL,
  `nombre` varchar(45) DEFAULT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `borrado` INT(2) NULL DEFAULT 0 ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`camara_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `camara_tg_insert` AFTER INSERT ON `camara` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'camara');
END $$
DELIMITER ;


CREATE TABLE IF NOT EXISTS  `empresa` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned NOT NULL,
  `fecha_operacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `tipo_operacion` varchar(1) NOT NULL,
  `id_empresa` int(11) unsigned NOT NULL,
  `cuit` DECIMAL(11,0) NULL DEFAULT NULL,
  `razon_social` varchar(250) DEFAULT NULL,
  `codigo_cnrt` int(20) DEFAULT NULL,
  `id_camara` int(15) DEFAULT NULL,
  `borrado` int(2) NOT NULL,
  PRIMARY KEY (`id`)
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`empresa_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `empresa_tg_insert` AFTER INSERT ON `empresa` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'empresa');
END $$
DELIMITER ;


CREATE TABLE IF NOT EXISTS  `imprenta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned NOT NULL,
  `fecha_operacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `tipo_operacion` varchar(1) NOT NULL,
  `id_imprenta` int(11) unsigned NOT NULL,
  `cuit` DECIMAL(11,0) NULL DEFAULT NULL,
  `razon_social` varchar(255) DEFAULT NULL,
  `borrado` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`imprenta_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `imprenta_tg_insert` AFTER INSERT ON `imprenta` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'imprenta');
END $$
DELIMITER ;

CREATE TABLE IF NOT EXISTS  `tipo_marbete` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned NOT NULL,
  `fecha_operacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `tipo_operacion` varchar(1) NOT NULL, 
  `id_tipo_marbete` int(10) unsigned NOT NULL,
  `tipo` varchar(40) DEFAULT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `borrado` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`tipo_marbete_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `tipo_marbete_tg_insert` AFTER INSERT ON `tipo_marbete` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'tipo_marbete');
END $$
DELIMITER ;

CREATE TABLE IF NOT EXISTS  `usuarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned DEFAULT NULL,
  `fecha_operacion` timestamp NULL DEFAULT NULL,
  `tipo_operacion` varchar(1) DEFAULT NULL,
  `id_usuario_panel` int(10) unsigned NOT NULL,
  `id_rol` int(10) unsigned NOT NULL,
  `username` varchar(30) NOT NULL,
  `metadata` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`usuarios_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `usuarios_tg_insert` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'usuarios');
END $$
DELIMITER ;

-- Agrego triggers de db_app

USE `{{{db_app}}}`;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`camara_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `camara_tg_alta` AFTER INSERT ON `camara` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.camara(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_camara`,`nombre`,`descripcion`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.nombre,NEW.descripcion,NEW.borrado);
END$$
DELIMITER ;


DROP TRIGGER IF EXISTS `{{{db_app}}}`.`camara_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `camara_tg_modificacion` AFTER UPDATE ON `camara` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.camara(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_camara`,`nombre`,`descripcion`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.nombre,NEW.descripcion,NEW.borrado);
END$$
DELIMITER ;


DROP TRIGGER IF EXISTS `{{{db_app}}}`.`empresa_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `empresa_tg_alta` AFTER INSERT ON `empresa` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.empresa(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_empresa`,`cuit`,`razon_social`,`codigo_cnrt`,`id_camara`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.cuit,NEW.razon_social,NEW.codigo_cnrt,NEW.id_camara,NEW.borrado);
END$$
DELIMITER ;


DROP TRIGGER IF EXISTS `{{{db_app}}}`.`empresa_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `empresa_tg_modificacion` AFTER UPDATE ON `empresa` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.empresa(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_empresa`,`cuit`,`razon_social`,`codigo_cnrt`,`id_camara`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.cuit,NEW.razon_social,NEW.codigo_cnrt,NEW.id_camara,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`imprenta_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `imprenta_tg_alta` AFTER INSERT ON `imprenta` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.imprenta(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_imprenta`,`cuit`,`razon_social`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.cuit,NEW.razon_social,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`imprenta_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `imprenta_tg_modificacion` AFTER UPDATE ON `imprenta` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.imprenta(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_imprenta`,`cuit`,`razon_social`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.cuit,NEW.razon_social,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`tipo_marbete_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `tipo_marbete_tg_alta` AFTER INSERT ON `tipo_marbete` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.tipo_marbete(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_tipo_marbete`,`tipo`,`descripcion`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.tipo,NEW.descripcion,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`tipo_marbete_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `tipo_marbete_tg_modificacion` AFTER UPDATE ON `tipo_marbete` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.tipo_marbete(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_tipo_marbete`,`tipo`,`descripcion`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.tipo,NEW.descripcion,NEW.borrado);
END$$
DELIMITER ;

USE `{{{db_app}}}`;

CREATE TABLE `pedido_marbete` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) unsigned NOT NULL,
  `id_imprenta` int(11) unsigned NOT NULL,
  `id_tipo_marbete` int(11) unsigned NOT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `cantidad` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

USE `{{{db_log}}}`;

ALTER TABLE `{{{db_log}}}`.`_registros_abm` 
CHANGE COLUMN `tabla_nombre` `tabla_nombre` ENUM('camara', 'empresa', 'imprenta', 'tipo_marbete', 'usuarios', 'pedido_marbete') NULL DEFAULT NULL ;

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

USE `{{{db_app}}}`;

CREATE TABLE `log_estado_pedido` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_pedido_marbete` int(11) unsigned NOT NULL,
  `estado` int(11) unsigned NOT NULL,
  `fecha_operacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

DROP TRIGGER IF EXISTS `{{{db_log}}}`.`asignacion_marbete_tg_insert`;

DELIMITER $$
USE `{{{db_log}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `asignacion_marbete_tg_insert` AFTER INSERT ON `asignacion_marbete` FOR EACH ROW BEGIN
		INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'asignacion_marbete');
END$$
DELIMITER ;

ALTER TABLE `{{{db_log}}}`.`_registros_abm` 
CHANGE COLUMN `tabla_nombre` `tabla_nombre` ENUM('camara', 'empresa', 'imprenta', 'tipo_marbete', 'usuarios', 'pedido_marbete', 'autorizacion', 'asignacion_marbete') NULL DEFAULT NULL ;

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

#####VERSIÓN##############################################
CREATE TABLE IF NOT EXISTS {{{db_app}}}.`db_version` (
  `version` mediumint(5) unsigned NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS {{{db_log}}}.`db_version` (
  `version` mediumint(5) unsigned NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO {{{db_app}}}.db_version VALUES('1.0', now());
INSERT INTO {{{db_log}}}.db_version VALUES('1.0', now());

#####IMPRENTAS##############################################
SET @id_usuario :=99999;

INSERT INTO `{{{db_app}}}`.`rango_imprenta` (id_imprenta,rango,borrado)
	VALUES (1,'["A","F"]',0);
INSERT INTO `{{{db_app}}}`.`rango_imprenta` (id_imprenta,rango,borrado)
	VALUES (2,'["G","L"]',0);
INSERT INTO `{{{db_app}}}`.`rango_imprenta` (id_imprenta,rango,borrado)
	VALUES (3,'["M","Z"]',0);

INSERT INTO `{{{db_app}}}`.`imprenta` (cuit,razon_social,direccion,borrado)
	VALUES (30500217274,'Ramon Choza','Sta María del Buen Aire 828, 1277 Buenos Aires',0);
INSERT INTO `{{{db_app}}}`.`imprenta` (cuit,razon_social,direccion,borrado)
	VALUES (30710845316,'Graf','Mariano Moreno 4794, Munro (1605), Buenos Aires, ',0);
INSERT INTO `{{{db_app}}}`.`imprenta` (cuit,razon_social,direccion,borrado)
	VALUES (20242378432,'Adrian Maestro','Gral Martin Guemes 640, 4400 Salta',0);








