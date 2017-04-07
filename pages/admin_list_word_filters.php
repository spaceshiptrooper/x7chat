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
	
	$sql = "SELECT * FROM {$x7->dbprefix}word_filters";
	$st = $db->prepare($sql);
	$st->execute();
	$filters = $st->fetchAll();
	
	$x7->display('pages/admin/word_filter', array(
		'filters' => $filters,
		'menu' => generate_admin_menu('list_word_filters'),
	));