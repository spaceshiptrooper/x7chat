CREATE TABLE `{$prefix}groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NULL,
  `access_admin_panel` tinyint(1) NULL,
  `create_room` tinyint(1) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;