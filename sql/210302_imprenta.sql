USE `{{{db_app}}}`;

ALTER TABLE `{{{db_app}}}`.`imprenta` 
DROP INDEX `cuit_UNIQUE` ;
;

ALTER TABLE `{{{db_app}}}`.`imprenta` 
CHANGE COLUMN `cuit` `cuit` DECIMAL(11,0) NULL DEFAULT NULL ;

USE `{{{db_log}}}`;

ALTER TABLE `{{{db_log}}}`.`imprenta` 
CHANGE COLUMN `cuit` `cuit` DECIMAL(11,0) NULL DEFAULT NULL ,
DROP INDEX `cuit_UNIQUE` ;
;

