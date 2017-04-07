CREATE TABLE `{$prefix}bans` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`ip` varchar(45) NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;