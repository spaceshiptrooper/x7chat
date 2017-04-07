hCREATE TABLE `{$prefix}rooms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NULL,
  `topic` varchar(1024) NULL,
  `greeting` varchar(1024) NULL,
  `password` varchar(255) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;