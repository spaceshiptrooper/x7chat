ALTER TABLE `{$prefix}config`
	ADD `min_font_size` TINYINT( 2 ) UNSIGNED NULL DEFAULT 10 ,
	ADD `max_font_size` TINYINT( 2 ) UNSIGNED NULL DEFAULT 16 ,
	ADD `login_page_news` TEXT NULL;