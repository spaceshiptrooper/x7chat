<?php $display('layout/header'); ?>
	<p><?php $lang('reset_instructions'); ?></p>
	<form action="<?php $url('doreset'); ?>" method="post">
		<label for="email"><?php $lang('email_label'); ?></label>
		<input type="text" name="email" id="email" value="<?php $var('email'); ?>" />
		
		<input type="submit" id="reset_button" name="reset_button" value="<?php $lang('reset_button'); ?>" />
	</form>
<?php $display('layout/footer'); ?>