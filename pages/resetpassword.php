<?php
	$db = $x7->db();
	
	$vars = $x7->get_vars();
	
	$x7->display('pages/reset_password', array(
		'email' => (isset($vars['email']) ? $vars['email'] : '')
	));