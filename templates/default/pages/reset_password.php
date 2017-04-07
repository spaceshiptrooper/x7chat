<?php $display('layout/header'); ?>
	<p><?php $lang('reset_pass_instr'); ?></p>
	<form action="<?php $url('doresetpassword'); ?>" method="post">
		<label for="email"><?php $lang('email_label'); ?></label>
		<input type="text" name="email" id="email" value="<?php $esc($var('email')); ?>" />
		
		<input type="submit" id="reset_button" name="reset_button" value="<?php $lang('reset_button'); ?>" />
	</form>
<?php $display('layout/footer'); ?>