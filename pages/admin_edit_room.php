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
	$room_id = isset($_GET['room_id']) ? $_GET['room_id'] : 0;
	
	if($room_id)
	{
		$sql = "
			SELECT
				*
			FROM {$x7->dbprefix}rooms
			WHERE
				id = :room_id
		";
		$st = $db->prepare($sql);
		$st->execute(array(':room_id' => $room_id));
		$room = $st->fetch();
		$st->closeCursor();
	}
	
	if(!$room && $room_id)
	{
		$x7->set_message($x7->lang('room_not_found'));
		$x7->go('admin_rooms');
	}
	
	$vars = $x7->get_vars();
	if(!empty($vars['room']))
	{
		$room = array_merge($room, $vars['room']);
	}
	
	$x7->display('pages/admin/edit_room', array(
		'room' => $room,
		'menu' => generate_admin_menu($room_id ? 'edit_room' : 'create_room'),
	));