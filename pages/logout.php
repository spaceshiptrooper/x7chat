<?php
	$x7->load('user');
	
	try
	{
		$user_ob = new x7_user();
		$user_ob->leave_rooms();
	}
	catch(x7_exception $ex)
	{
		die(json_encode(array('redirect' => $x7->url('login'))));
	}
	
	$_SESSION['user_id'] = 0;
	$_SESSION['rooms'] = array();
	
	$x7->go('login');