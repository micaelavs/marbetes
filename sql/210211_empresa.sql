USE `{{{db_app}}}`;

ALTER TABLE `{{{db_app}}}`.`empresa` 
CHANGE COLUMN `cuit` `cuit` DECIMAL(11,0) NULL DEFAULT NULL ;

USE `{{{db_log}}}`;

ALTER TABLE `{{{db_log}}}`.`empresa` 
CHANGE COLUMN `cuit` `cuit` DECIMAL(11,0) NULL DEFAULT NULL ;

