<?php
	$x7->load('user');
	$x7->load('admin');
	
	$db = $x7->db();
	$page_name = 'delete_user';
	
	force_admin_access($page_name);
	
	$id = isset($_GET['id']) ? $_GET['id'] : 0;
	if(!$id)
	{
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
	}
	
	try
	{
		$user = new x7_user($id);
		$user_data = $user->data();
	}
	catch(x7_exception $ex)
	{
		$x7->set_message($x7->lang('invalid_user'));
		$x7->go('admin_list_users');
	}
	
	$cur_user = new x7_user();
	if($cur_user->id() == $user->id())
	{
		$x7->set_message($x7->lang('cannot_delete_self'));
		$x7->go('admin_list_users');
	}
	
	$confirmed = isset($_POST['confirm']) ? $_POST['confirm'] : 0;
	
	if($confirmed)
	{
		$sql = "DELETE FROM {$x7->dbprefix}users WHERE id = :id";
		$st = $db->prepare($sql);
		$st->execute(array(
			':id' => $id,
		));
		
		$sql = "
			INSERT INTO {$x7->dbprefix}messages (timestamp, message_type, dest_type, dest_id, source_type, source_id) VALUES (:timestamp, :message_type, :dest_type, :dest_id, :source_type, :source_id)
		";
		$st = $db->prepare($sql);
		$st->execute(array(
			':timestamp' => date('Y-m-d H:i:s'), 
			':message_type' => 'logout', 
			':dest_type' => 'user', 
			':dest_id' => $id, 
			':source_type' => 'system', 
			':source_id' => 0,
		));
		
		$x7->set_message($x7->lang('user_deleted'), 'notice');
		$x7->go('admin_list_users');
	}
	else
	{
		$x7->display('pages/admin/confirm_delete_user', array(
			'user' => $user->data(),
			'menu' => generate_admin_menu($page_name),
		));
	}