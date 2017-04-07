<div id="title_def"><?php $lang('admin_rooms_title'); ?></div>
<?php $display('layout/adminmenu'); ?>
<div id="admin_content">
	<?php $display('layout/messages'); ?>
	<form class="standard_form" data-action="do_admin_edit_user">
		<input type="hidden" name="user_id" id="user_id" value="<?php $esc($val('user.id')); ?>" />

		<label for="username"><?php $lang('username_label'); ?></label>
		<input type="text" name="username" id="username" value="<?php $var('user.username'); ?>" />
		<p>&nbsp;</p>
		
		<label for="email"><?php $lang('email_label'); ?></label>
		<input type="text" name="email" id="email" value="<?php $var('user.email'); ?>" />
		<p>&nbsp;</p>
		
		<label for="password"><?php $lang('password_label'); ?></label>
		<input type="text" name="password" id="password" value="" />
		<p><?php $lang('admin_user_pass'); ?></p>
		
		<input type="submit" value="<?php $lang('save_user_button'); ?>" />
	</form>
</div>