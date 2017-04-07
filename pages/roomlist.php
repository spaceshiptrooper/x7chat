<?php
	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		$x7->fatal_error($x7->lang('login_required'));
	}
	
	$sql = "SELECT * FROM {$x7->dbprefix}rooms";
	$st = $db->prepare($sql);
	$st->execute();
	$rooms = $st->fetchAll();
	
	$x7->display('pages/roomlist', array('rooms' => $rooms));