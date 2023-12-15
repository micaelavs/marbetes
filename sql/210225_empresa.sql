USE {{{db_app}}};

ALTER TABLE `{{{db_app}}}`.`empresa` 
CHANGE COLUMN `borrado` `borrado` INT(2) NULL DEFAULT 0 ;

ALTER TABLE `{{{db_app}}}`.`empresa` 
DROP INDEX `cuit_UNIQUE` ;
;

USE {{{db_log}}};

ALTER TABLE `{{{db_log}}}`.`empresa` 
DROP INDEX `cuit_UNIQUE` ;
;




