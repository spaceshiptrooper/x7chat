CREATE TABLE `{$prefix}messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NULL,
  `dest_type` varchar(255) NULL,
  `dest_id` bigint(20) unsigned NULL,
  `source_type` varchar(255) NULL,
  `source_id` bigint(20) unsigned NULL,
  `message_type` varchar(255) NULL,
  `message` text NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;