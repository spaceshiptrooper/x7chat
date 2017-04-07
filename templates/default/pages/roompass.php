<div id="title_def"><?php $lang('roompass_title'); ?></div>
<?php $display('layout/messages'); ?>
<?php $lang('roompass_instructions'); ?>
<form class="standard_form" id="roompass_form">
	<label for="room_password"><?php $lang('roompass_label'); ?></label>
	<input type="password" name="room_password" id="room_password" />
	
	<input type="submit" value="<?php $lang('continue_button'); ?>" />
</form>
<script type="text/javascript">
	$("#roompass_form").bind('submit', function() {
		var pass = $('#room_password').val();
		open_content_area('<?php $url('joinroom?room_id=' . $room_id); ?>&password=' + pass);
		return false;
	});
</script>