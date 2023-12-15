
DELIMITER $$
USE `{{{db_log}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `asignacion_marbete_tg_insert` AFTER INSERT ON `asignacion_marbete` FOR EACH ROW BEGIN
		INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'asignacion_marbete');
END$$
DELIMITER ;

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

