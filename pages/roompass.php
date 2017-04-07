<?php
	$db = $x7->db();

	if(empty($_SESSION['user_id']))
	{
		$x7->go('login');
	}

	$room_id = isset($_GET['room_id']) ? $_GET['room_id'] : 0;
	
	$x7->display('pages/roompass', array('room_id' => $room_id));