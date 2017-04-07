<?php $display('layout/header'); ?>
	<?php if(!empty($news)): ?>
		<div class='login_page_news'>
			<?php echo nl2br($news); ?>
		</div>
	<?php endif; ?>
	<p><?php $lang('login_instructions'); ?></p>
	<form action="<?php $url('dologin'); ?>" method="post">
		<label for="username"><?php $lang('username_label'); ?></label>
		<input type="text" name="username" id="username" value="<?php $var('username'); ?>" />
		
		<label for="password"><?php $lang('password_label'); ?></label>
		<input type="password" name="password" id="password" value="" />
		
		<input type="submit" id="login_button" name="login_button" value="<?php $lang('login_button'); ?>" />
		<a href="<?php $url('register'); ?>" id="register_button"><?php $lang('register_button'); ?></a> | 
		<a href="<?php $url('resetpassword'); ?>" id="resetpass_button"><?php $lang('resetpass_button'); ?></a>
	</form>
<?php $display('layout/footer'); ?>