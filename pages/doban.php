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
	
	if($by == 'ip')
	{
		$sql = "
			REPLACE INTO {$x7->dbprefix}bans (ip) VALUES (:ip)
		";
		$params = array(':ip' => $banning_user['ip']);
	}
	else
	{
		$sql = "
			UPDATE {$x7->dbprefix}users
			SET
				banned = 1
			WHERE
				id = :user_id
		";
		$params = array(':user_id' => $user_id);
	}
	
	$st = $db->prepare($sql);
	$st->execute($params);
	
	$sql = "
		INSERT INTO {$x7->dbprefix}messages (timestamp, message_type, dest_type, dest_id, source_type, source_id, message) VALUES (:timestamp, :message_type, :dest_type, :dest_id, :source_type, :source_id, :message)
	";
	$st = $db->prepare($sql);
	$st->execute(array(
		':timestamp' => date('Y-m-d H:i:s'), 
		':message_type' => 'ban_resync', 
		':message' => '',
		':dest_type' => 'user', 
		':dest_id' => ($by == 'account' ? $user_id : 0), 
		':source_type' => 'system', 
		':source_id' => 0,
	));
	
	$x7->set_message($x7->lang('user_banned'), 'notice');
	$x7->go('user_room_profile?user=' . $user_id);