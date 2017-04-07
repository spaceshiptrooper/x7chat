<?php $display('layout/header'); ?>
	<p><?php $lang('register_instructions'); ?></p>
	<form action="<?php $url('doregister'); ?>" method="post">
		<label for="username"><?php $lang('username_label'); ?></label>
		<input type="text" name="username" id="username" value="<?php $var('username'); ?>" />
		
		<label for="email"><?php $lang('email_label'); ?></label>
		<input type="text" name="email" id="email" value="<?php $var('email'); ?>" />
		
		<label for="password"><?php $lang('password_label'); ?></label>
		<input type="password" name="password" id="password" value="" />
		
		<label for="repassword"><?php $lang('retype_password_label'); ?></label>
		<input type="password" name="repassword" id="repassword" value="" />
		
		<input type="submit" id="register_button" name="register_button" value="<?php $lang('register_button'); ?>" />
		<a href="<?php $url('login'); ?>" id="login_button"><?php $lang('login_link'); ?></a>
	</form>
<?php $display('layout/footer'); ?>