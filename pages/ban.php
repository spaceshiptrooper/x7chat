<?php
	$x7->load('user');
	
	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		$x7->fatal_error($x7->lang('login_required'));
	}
	
	$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
	$by = isset($_GET['by']) ? $_GET['by'] : 0;
	
	if(!$user_id)
	{
		throw new exception("Missing parameter value for user_id");
	}
	
	if(!in_array($by, array('ip', 'account')))
	{
		throw new exception("Invalid parameter value for by");
	}
	
	$perms = x7_get_user_permissions();
	if(!$perms || !$perms['access_admin_panel'])
	{
		$x7->set_message($x7->lang('login_failed_banned_access_denied'));
		$x7->go('user_room_profile?user=' . $user_id);
	}
	
	$banning_user = x7_get_user($user_id);
	if(!$banning_user)
	{
		$x7->fatal_error($x7->lang('login_failed_banned_invalid_user'));
	}
	
	if(!$banning_user['ip'])
	{
		$x7->fatal_error($x7->lang('login_failed_banned_unknown_ip'));
	}
	
	$x7->display('pages/banconfirm', array(
		'user' => $banning_user,
		'by' => $by,
	));