CREATE TABLE `{$prefix}config` (
  `version` int(10) NULL,
  `title` varchar(255) NULL,
  `theme` varchar(32) NULL,
  `auto_join` bigint(20) unsigned NULL,
  `chat_size` varchar(11) NULL,
  `use_smtp` tinyint(1) NULL,
  `smtp_host` varchar(255) NULL,
  `smtp_user` varchar(255) NULL,
  `smtp_port` int(11) NULL,
  `smtp_pass` varchar(255) NULL,
  `smtp_mode` varchar(5) NULL,
  `from_address` varchar(255) NULL,
  `allow_guests` tinyint(1) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;