<?php
	$x7->load('user');
	$x7->load('admin');
	
	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		$x7->fatal_error($x7->lang('login_required'));
	}
	
	$user = new x7_user();
	$perms = $user->permissions();
	if(empty($perms['access_admin_panel']))
	{
		$x7->fatal_error($x7->lang('access_denied'));
	}
	
	$sql = "SELECT `id`, `name` FROM {$x7->dbprefix}rooms";
	$st = $db->prepare($sql);
	$st->execute();
	$rooms = $st->fetchAll();
	
	$sql = "SELECT * FROM {$x7->dbprefix}config LIMIT 1";
	$st = $db->prepare($sql);
	$st->execute();
	$config = $st->fetch();
	$st->closeCursor();
	
	$vars = $x7->get_vars();
	if(!empty($vars['config']))
	{
		$config = array_merge($config, $vars['config']);
	}
	
	$x7->display('pages/admin/settings', array(
		'config' => $config,
		'rooms' => $rooms,
		'menu' => generate_admin_menu('smilies'),
	));