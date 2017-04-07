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
	
	$room = array();
	$user_id = isset($_GET['id']) ? $_GET['id'] : 0;
	
	$edit_user_data = array();
	if($user_id)
	{
		try
		{
			$edit_user = new x7_user($user_id);
			$edit_user_data = $edit_user->data();
		}
		catch(x7_exception $ex)
		{
			$x7->set_message($x7->lang('user_not_found'));
			$x7->go('admin_users');
		}
	}
	
	$vars = $x7->get_vars();
	if(!empty($vars['user']))
	{
		$edit_user_data = array_merge($edit_user_data, $vars['user']);
	}
	
	$x7->display('pages/admin/edit_user', array(
		'user' => $edit_user_data,
		'menu' => generate_admin_menu($user_id ? 'edit_user' : 'create_user'),
	));