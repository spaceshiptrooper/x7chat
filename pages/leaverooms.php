<?php
	$x7->load('user');

	$db = $x7->db();
	
	try
	{
		$user_ob = new x7_user();
		$user_ob->leave_rooms();
	}
	catch(x7_exception $ex)
	{
		die(json_encode(array('redirect' => $x7->url('login'))));
	}