CREATE TABLE `{$prefix}room_users` (
  `user_id` bigint(20) unsigned NOT NULL,
  `room_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;