<?php
	$x7->load('user');
	
	$db = $x7->db();

	if(empty($_SESSION['user_id']))
	{
		$x7->go('login');
	}

	$user_id = $_SESSION['user_id'];
	
	$sql = "
		SELECT
			*
		FROM {$x7->dbprefix}word_filters
		ORDER BY
			LENGTH(word) DESC
	";
	$st = $db->prepare($sql);
	$st->execute();
	$filters = $st->fetchAll();
	
	$user = new x7_user();
	
	$user_ob = new x7_user();
	$perms = $user_ob->permissions();
	$access_acp = !empty($perms['access_admin_panel']);
	
	$auto_join = 0;
	if(empty($_SESSION['rooms']))
	{
		$auto_join = $x7->config('auto_join');
	}
	
	$x7->display('pages/chat', array(
		'user' => $user->data(), 
		'settings' => $user->get_settings(),
		'access_acp' => $access_acp, 
		'auto_join' => $auto_join,
		'filters' => $filters,
	));