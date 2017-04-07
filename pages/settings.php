<?php
	$x7->load('user');

	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		$x7->fatal_error($x7->lang('login_required'));
	}
	
	$user_id = $_SESSION['user_id'];
	
	$sql = "
		SELECT
			*
		FROM {$x7->dbprefix}message_fonts
	";
	$st = $db->prepare($sql);
	$st->execute();
	$fonts = $st->fetchAll();
	
	$user = new x7_user();
	
	$vals = $x7->get_vars();
	if($vals)
	{
		$defaults = $vals;
	}
	else
	{
		$defaults = $user->data();
	}
	
	$genders = array(
		'male' => $x7->lang('male'),
		'female' => $x7->lang('female'),
	);
	
	$x7->display('pages/settings', array(
		'genders' => $genders, 
		'user' => $defaults,
		'settings' => $user->get_settings(),
		'fonts' => $fonts,
	));