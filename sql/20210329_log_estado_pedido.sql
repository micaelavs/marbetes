USE `{{{db_app}}}`;

CREATE TABLE `log_estado_pedido` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_pedido_marbete` int(11) unsigned NOT NULL,
  `estado` int(11) unsigned NOT NULL,
  `fecha_operacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8