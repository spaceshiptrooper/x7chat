CREATE TABLE `{$prefix}users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NULL,
  `password` varchar(255) NULL,
  `reset_password` varchar(255) NULL,
  `email` varchar(255) NULL,
  `group_id` bigint(20) unsigned NULL,
  `banned` tinyint(1) NULL,
  `timestamp` datetime NULL,
  `ip` varchar(45) NULL,
  `real_name` varchar(255) NULL,
  `gender` varchar(255) NULL,
  `about` text NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;