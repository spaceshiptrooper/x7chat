<?php
	$db = $x7->db();
	
	$fail = false;
	
	$username = isset($_POST['username']) ? $_POST['username'] : null;
	if(!$username)
	{
		$x7->set_message($x7->lang('missing_register_username'));
		$fail = true;
	}
	
	$password = isset($_POST['password']) ? $_POST['password'] : null;
	if(!$password)
	{
		$x7->set_message($x7->lang('missing_register_password'));
		$fail = true;
	}
	
	$repassword = isset($_POST['repassword']) ? $_POST['repassword'] : null;
	if($password !== $repassword)
	{
		$x7->set_message($x7->lang('passwords_donot_match'));
		$fail = true;
	}
	
	$email = isset($_POST['email']) ? $_POST['email'] : null;
	if(!filter_var($email, FILTER_VALIDATE_EMAIL))
	{
		$x7->set_message($x7->lang('invalid_email'));
		$fail = true;
	}
	
	$sql = "SELECT * FROM {$x7->dbprefix}users WHERE username = :username OR email = :email";
	$st = $db->prepare($sql);
	$st->execute(array(':username' => $username, ':email' => $email));
	while($check = $st->fetch())
	{
		$fail = true;
			
		if($username && $check['username'] === $username)
		{
			$x7->set_message($x7->lang('username_in_use'));
		}
		elseif(filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$x7->set_message($x7->lang('email_in_use'));
		}
	}
	
	if($fail)
	{
		$x7->go('register', array(
			'username' => $username,
			'email' => $email,
		));
	}

	$hashed_password = password_hash($password, PASSWORD_BCRYPT, array('cost' => cost));
	
	$sql = "INSERT INTO {$x7->dbprefix}users (username, password, email) VALUES (:username, :password, :email)";
	$st = $db->prepare($sql);
	$st->execute(array(':username' => $username, ':email' => $email, ':password' => $hashed_password));
	$user_id = $db->lastInsertId();
	
	$_SESSION['user_id'] = $user_id;
	
	$x7->go('chat');