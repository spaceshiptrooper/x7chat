ALTER TABLE `{$prefix}users`
	ADD `enable_sounds` BOOL NULL DEFAULT '1',
	ADD `use_default_timestamp_settings` BOOL NULL DEFAULT '1',
	ADD `enable_timestamps` BOOL NULL DEFAULT '1',
	ADD `ts_24_hour` BOOL NULL DEFAULT '0',
	ADD `ts_show_seconds` BOOL NULL DEFAULT '0',
	ADD `ts_show_ampm` BOOL NULL DEFAULT '0',
	ADD `ts_show_date` BOOL NULL DEFAULT '0',
	ADD `enable_styles` BOOL NULL DEFAULT '1',
	ADD `message_font_size` TINYINT UNSIGNED NULL ,
	ADD `message_font_color` CHAR( 6 ) NULL ,
	ADD `message_font_face` BIGINT UNSIGNED NULL;