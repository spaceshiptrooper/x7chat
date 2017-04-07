<?php
	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		die(json_encode(array('redirect' => $x7->url('login'))));
	}
	
	$room_id = isset($_POST['room']) ? $_POST['room'] : array();
	
	$user_id = $_SESSION['user_id'];
	
	foreach($_SESSION['rooms'] as $key => $id)
	{
		if($id == $room_id)
		{
			unset($_SESSION['rooms'][$key]);
		}
	}
	
	$sql = "
		DELETE
			room_user.*
		FROM {$x7->dbprefix}room_users room_user
		WHERE
			room_id = :room_id
			AND user_id = :user_id
	";
	$st = $db->prepare($sql);
	$st->execute(array(':user_id' => $user_id, ':room_id' => $room_id));
	
	$sql = "
		INSERT INTO {$x7->dbprefix}messages (timestamp, message_type, dest_type, dest_id, source_type, source_id) VALUES (:timestamp, :message_type, :dest_type, :dest_id, :source_type, :source_id)
	";
	$st = $db->prepare($sql);
	$st->execute(array(
		':timestamp' => date('Y-m-d H:i:s'), 
		':message_type' => 'room_resync', 
		':dest_type' => 'room', 
		':dest_id' => $room_id, 
		':source_type' => 'system', 
		':source_id' => 0,
	));