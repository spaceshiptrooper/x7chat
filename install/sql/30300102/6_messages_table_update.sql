ALTER TABLE `{$prefix}messages`
	ADD `font_size` TINYINT UNSIGNED NULL ,
	ADD `font_color` CHAR( 6 ) NULL ,
	ADD `font_face` VARCHAR( 255 ) NULL;