<?php
	$x7->load('user');
	$x7->load('mail');
	$db = $x7->db();
	
	$email = isset($_POST['email']) ? $_POST['email'] : null;
	
	try
	{
		$user = new x7_user($email, 'email');
		$user_data = $user->data();
	}
	catch(x7_exception $ex)
	{
		$user = null;
	}
	
	if(!$user || !$user_data['password'])
	{
		$x7->set_message($x7->lang('email_not_registered'));
		$x7->go('resetpassword', $_POST);
	}

	$token = password_hash(mt_rand() . microtime(TRUE) . print_r($_SERVER, 1), PASSWORD_BCRYPT, array('cost' => cost));
	
	$sql = "
		UPDATE {$x7->dbprefix}users SET
			reset_password = :token
		WHERE
			id = :user_id
	";
	$st = $db->prepare($sql);
	$st->execute(array(
		':token' => $token,
		':user_id' => $user->id(),
	));
	
	$user_data = $user->data();
	$mail = new x7_mail();
	$mail->send($user_data['email'], 'reset_password', array(
		'reset_url' => $x7->url('updatepassword?token=' . $token),
	));
	
	$x7->set_message($x7->lang('reset_token_sent'), 'notice');
	$x7->go('login');