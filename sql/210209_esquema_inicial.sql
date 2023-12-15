#SIEMPRE SE ACTUALIZARA LA VERSIÓN DE LAS DOS DBs AUNQUE NO SE HAGAN CAMBIOS.
#REMPLAZAR ANTES DE EJECUTAR
# {{{user_mysql}}}  = REEMPLAZAR POR NOMBRE USER QUE EJECUTA.
# {{{db_log}}}      = REEMPLAZAR POR NOMBRE DB LOG.
# {{{db_app}}}      = REEMPLAZAR POR NOMBRE DB APP.


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
  `borrado` tinyint(1) NOT NULL,
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
  `cuit` int(10) DEFAULT NULL,
  `razon_social` varchar(250) DEFAULT NULL,
  `codigo_cnrt` int(20) DEFAULT NULL,
  `id_camara` int(15) DEFAULT NULL,
  `borrado` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cuit_UNIQUE` (`cuit`)
  
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
  `cuit` int(11) DEFAULT NULL,
  `razon_social` varchar(255) DEFAULT NULL,
  `borrado` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cuit_UNIQUE` (`cuit`)
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
  `borrado` tinyint(1) NOT NULL,
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

CREATE TABLE IF NOT EXISTS `db_version` (
  `version` mediumint(5) unsigned NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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