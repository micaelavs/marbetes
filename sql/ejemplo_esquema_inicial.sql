-- EJEMPLO DE LA ESTRUCTURA DE UN SCRIPT DE VERSIONES SQL

-- CADA VEZ QUE UN DEV AGREGUE UNA TABLA DEBERA SUMINISTRAR ESTTRUCTURA DE DOS TABLAS app y log. AACOMPAÑADAS DE 2 TRIGGER PARA APP "alta y modificacion" y UN TRIGGER EN LOG PARA ACTUALIZACION DEL INDICE

-- UNA VEZ INSTALADAS AMBAS DBS PARA INSERTAR DATOS MANUALMENTE DEBE EJECUTARSE ANTES   "SET @id_usuario :=99999;". ESTO INICIALIZA LA VARIABLE id_usuario  QUE USARAN LOS TRIGGER.
-- POR CONVENCION 99999 ES INTERVENCION DE PERSONAL DE SISTEMAS.
-- DESDE EL CODIGO EL SETEO DE id_usuario ES TRANSPARENTE PORQUE ESTA INTEGRADO EN EL MODELO.PHP LOCAL DEL CUAL HEREDARAN TODOS LOS MODELOS.
-- 
 

#SIEMPRE SE ACTUALIZARA LA VERSIÓN DE LAS DOS DBs AUNQUE NO SE HAGAN CAMBIOS.
#REMPLAZAR ANTES DE EJECUTAR
# {{{user_mysql}}}  = REEMPLAZAR POR NOMBRE USER QUE EJECUTA.
# {{{db_log}}}      = REEMPLAZAR POR NOMBRE DB LOG.
# {{{db_app}}}      = REEMPLAZAR POR NOMBRE DB APP.


CREATE DATABASE  IF NOT EXISTS `{{{db_log}}}` DEFAULT CHARACTER SET utf8 ;
USE `{{{db_log}}}`;

--
-- Tabla que indexa todas las operacion que entran en el log `_registros_abm`
-- NOTA: cada vez que se agregue una tabla al esquema debe actualizarse el campo tabla_nombre
-- TABLA OBLIGATORIA
--
CREATE TABLE IF NOT EXISTS  `_registros_abm` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned DEFAULT NULL,
  `fecha_operacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo_operacion` char(1) DEFAULT NULL,
  `id_tabla` bigint(20) unsigned NOT NULL,
  `tabla_nombre` enum('areas','estados','unidades') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fecha_operacion` (`fecha_operacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Tabla de registro de version
-- TABLA OBLIGATORIA
--
CREATE TABLE IF NOT EXISTS  `db_version` (
  `version` mediumint(5) unsigned NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- TABLA EJEMPLO
CREATE TABLE IF NOT EXISTS `estados` (  
#CAMPOS DE SEGUIMIENTO
  `id` bigint(20) unsigned  NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned  NOT NULL,
  `fecha_operacion` timestamp NOT NULL,
  `tipo_operacion` varchar(1) NOT NULL,
#CAMPOS DE LA TABLA TRACKEADA CON SU CAMPO "id" RENOMBRADO PARA EVITAR DUPLICIDAD
  `id_estado` int(11) unsigned NOT NULL,
  `estado` varchar(10) NOT NULL,
  `borrado` tinyint(1), 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Trigger para indexacion
-- TRIGGER EJEMPLO
--
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`estados_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `estados_tg_insert` AFTER INSERT ON `estados` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'estados');
END $$
DELIMITER ;


--
-- NOTA: Aunque el manejo y configuracion de usuarios es externo a la aplicacion, deben registrarse todos lo cambios generados
-- TABLA OBLIGATORIA
--

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



-- TRIGGER OBLIGATORIO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`usuarios_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `usuarios_tg_insert` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'usuarios');
END $$
DELIMITER ;



CREATE DATABASE  IF NOT EXISTS `{{{db_app}}}` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `{{{db_app}}}`;

--
-- Tabla de registro de version
-- TABLA OBLIGATORIA
--
CREATE TABLE IF NOT EXISTS  `db_version` (
  `version` mediumint(5) unsigned NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- TABLA EJEMPLO
CREATE TABLE IF NOT EXISTS `estados` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `estado` varchar(10) NOT NULL,
  `borrado` tinyint(1), 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Triggers para la tabla estados
-- TRIGGER EJEMPLO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`estados_tg_alta`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `estados_tg_alta` AFTER INSERT ON `estados` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.estados(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_estado`,`estado`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.estado,NEW.borrado);
END $$
DELIMITER ;
-- TRIGGER EJEMPLO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`estados_tg_modificacion`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `estados_tg_modificacion` AFTER UPDATE ON `estados` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.estados(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_estado`,`estado`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.estado,NEW.borrado);
END $$
DELIMITER ;


-- INSERT Obligatorio en las versiones, no asi en los script de desarrollo.
INSERT INTO {{{db_app}}}.db_version VALUES('1.0', now());
INSERT INTO {{{db_log}}}.db_version VALUES('1.0', now());
