<div id="title_def"><?php $lang('admin_delete_user'); ?></div>
<?php $display('layout/adminmenu'); ?>
<div id="admin_content">
	<p><?php $lang('admin_confirm_delete_user', array(
		':account' => $x7->esc($user['username']),
	)); ?></p>
	<p><?php $lang('not_reversable'); ?></p>
	<br />
	<form class="standard_form" data-action="admin_delete_user">
		<input type="hidden" name="id" id="id" value="<?php $esc($val('user.id')); ?>" />
		<input type="hidden" name="confirm" value="1" />
		
		<input type="submit" value="<?php $lang('yes_delete'); ?>" />
		&nbsp; &nbsp;
		<a data-href="admin_list_users" href="admin_list_users"><?php $lang('no_delete'); ?></a>
	</form>
</div>