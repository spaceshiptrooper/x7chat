<?php

	function db_connection($config)
	{
		$dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8';
		$db_options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => FALSE,
		);
		$db = new PDO($dsn, $config['user'], $config['pass'], $db_options);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
		$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		
		$sql = "SHOW TABLES;";
		$st = $db->prepare($sql);
		$st->execute();
		$tables = $st->fetchAll();
		
		return $db;
	}
	
	function run_sql($db, $srcdir, $prefix)
	{
		$dir = scandir('./sql/' . $srcdir);
		$patches = array();
		foreach($dir as $file)
		{
			if(preg_match('#^([0-9]+)(.+?)\.sql$#', $file, $match))
			{
				$order = $match[1];
				
				$sql = file_get_contents('./sql/' . $srcdir .'/' . $file);
				$sql = str_replace('{$prefix}', $prefix, $sql);
				
				$patches[$order][$file] = $sql;
			}
		}
		
		ksort($patches);
		
		foreach($patches as $patch_level)
		{
			foreach($patch_level as $file => $patch)
			{
				try
				{
					$st = $db->prepare($patch);
					$st->execute();
				}
				catch(Exception $ex)
				{
					throw new Exception("({$srcdir}/{$file}) " . $ex->getMessage());
				}
			}
		}
	}
	
	function sf($value)
	{
		return htmlentities($value, ENT_QUOTES, 'UTF-8');
	}