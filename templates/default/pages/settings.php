<div id="title_def"><?php $lang('settings_title'); ?></div>
<?php $display('layout/messages'); ?>
<form class="standard_form" id="settings_form">
	<h2><?php $lang('profile_settings'); ?></h2>
	<label for="real_name"><?php $lang('real_name_label'); ?></label>
	<input type="text" name="real_name" id="real_name" value="<?php $esc($user['real_name']); ?>" />
	<p>&nbsp;</p>
	
	<label for="gender"><?php $lang('gender_label'); ?></label>
	<select name="gender" id="gender">
		<option value=""></option>
		<?php foreach($genders as $key => $gender): ?>
			<option value="<?php $esc($key); ?>" <?php if($user['gender'] == $key) echo 'selected'; ?>><?php $esc($gender); ?></option>
		<?php endforeach; ?>
	</select>
	<p>&nbsp;</p>
	
	<label for="bio"><?php $lang('bio_label'); ?></label>
	<textarea name="bio" id="bio"><?php $esc($user['about']); ?></textarea>
	<p>&nbsp;</p>
	
	<?php if($user['email'] != $user['username']): ?>
		<h2><?php $lang('account_settings'); ?></h2>
		<label for="current_password"><?php $lang('current_password'); ?></label>
		<input type="password" name="current_password" id="current_password" />
		<p><?php $lang('current_password_instr'); ?></p>
		
		<label for="new_password"><?php $lang('new_password'); ?></label>
		<input type="password" name="new_password" id="new_password" />
		<p><?php $lang('new_password_instr'); ?></p>
		
		<label for="retype_new_password"><?php $lang('retype_new_password'); ?></label>
		<input type="password" name="retype_new_password" id="retype_new_password" />
		<p><?php $lang('retype_new_password_instr'); ?></p>
		
		<label for="email"><?php $lang('email_label'); ?></label>
		<input type="text" name="email" id="email" value="<?php $esc($user['email']); ?>" />
		<p>&nbsp;</p>
	<?php endif; ?>
	
	<h2><?php $lang('chat_settings'); ?></h2>
	<label for="enable_sounds"><?php $lang('enable_sounds'); ?></label>
	<input type="checkbox" name="enable_sounds" id="enable_sounds" value="1" <?php if($user['enable_sounds']) echo 'checked'; ?>>
	<p><?php $lang('enable_sounds_instr'); ?></p>
	
	<label for="timestamp_format"><?php $lang('timestamp_format'); ?></label>
	<div class="multi_checkbox" id="timestamp_settings">
		<input type="checkbox" name="use_default_timestamp_settings" id="use_default_timestamp_settings" value="1" <?php if($user['use_default_timestamp_settings']) echo 'checked'; ?>> <label for="use_default_timestamp_settings"><?php $lang('use_default_timestamp_settings'); ?></label>
		<br />
		<input type="checkbox" name="enable_timestamps" id="enable_timestamps" value="1" <?php if($user['enable_timestamps']) echo 'checked'; ?>> <label for="enable_timestamps"><?php $lang('enable_timestamps'); ?></label>
		<br />
		<input type="checkbox" name="ts_24_hour" id="ts_24_hour" value="1" <?php if($user['ts_24_hour']) echo 'checked'; ?>> <label for="ts_24_hour"><?php $lang('ts_24_hour'); ?></label>
		<br />
		<input type="checkbox" name="ts_show_seconds" id="ts_show_seconds" value="1" <?php if($user['ts_show_seconds']) echo 'checked'; ?>> <label for="ts_show_seconds"><?php $lang('ts_show_seconds'); ?></label>
		<br />
		<input type="checkbox" name="ts_show_ampm" id="ts_show_ampm" value="1" <?php if($user['ts_show_ampm']) echo 'checked'; ?>> <label for="ts_show_ampm"><?php $lang('ts_show_ampm'); ?></label>
		<!--
		<br />
		<input type="checkbox" name="ts_show_date" id="ts_show_date" value="1" <?php if($user['ts_show_date']) echo 'checked'; ?>> <label for="ts_show_date"><?php $lang('ts_show_date'); ?></label>
		-->
	</div>
	<p>&nbsp;</p>
	
	<label for="enable_styles"><?php $lang('show_message_styles'); ?></label>
	<input type="checkbox" name="enable_styles" id="enable_styles" value="1" <?php if($user['enable_styles']) echo 'checked'; ?>>
	<p><?php $lang('show_message_styles_instr'); ?></p>
	
	<label for="message_font_size"><?php $lang('message_font_size'); ?></label>
	<input type="text" name="message_font_size" id="message_font_size" value="<?php $esc($user['message_font_size']); ?>" />
	<p><?php $lang('message_font_size_instr'); ?></p>
	
	<label for="message_font_color"><?php $lang('message_font_color'); ?></label>
	<input type="text" name="message_font_color" id="message_font_color" value="<?php $esc($user['message_font_color']); ?>" />
	<p><?php $lang('message_font_color_instr'); ?></p>
	
	<label for="message_font_face"><?php $lang('message_font_face'); ?></label>
	<select name="message_font_face" id="message_font_face">
		<option value=""><?php $lang('default'); ?></option>
		<?php foreach($fonts as $font): ?>
			<option value="<?php $esc($font['id']); ?>" <?php if($user['message_font_face'] == $font['id']) echo 'selected'; ?>><?php $esc($font['name']); ?></option>
		<?php endforeach; ?>
	</select>
	<p>&nbsp;</p>
	
	<input type="submit" value="<?php $lang('save_settings_button'); ?>" />
</form>
<script type="text/javascript" src="scripts/jscolor/jscolor.js"></script>
<script type="text/javascript">
	function update_timestamp_settings()
	{
		var use_default_timestamp_settings = $("#use_default_timestamp_settings").attr('checked');
		var ts_24_hour = $("#ts_24_hour").attr('checked');
		var enable_timestamps = $("#enable_timestamps").attr('checked');
		
		$("#timestamp_settings input").attr('disabled', true);
		
		$("#use_default_timestamp_settings").attr('disabled', false);
		if(!use_default_timestamp_settings)
		{
			$("#enable_timestamps").attr('disabled', false);
			
			if(enable_timestamps)
			{
				$("#ts_24_hour").attr('disabled', false);
				$("#ts_show_seconds").attr('disabled', false);
				$("#ts_show_date").attr('disabled', false);
				
				if(!ts_24_hour)
				{
					$("#ts_show_ampm").attr('disabled', false);
				}
			}
		}
	}
	
	$("#timestamp_settings input").bind('click', function() {
		setTimeout(update_timestamp_settings, 250);
	});

	$("#settings_form").bind('submit', function() {
		$.post('<?php $url('savesettings'); ?>', $(this).serialize(), function(data) {
			$('#content_page').html(data);
			$('#content_page').scrollTop(0);
		});
		return false;
	});
	
	var pickers = new jscolor.color($("#message_font_color")[0], {
		required: false
	});
	
	App.settings = <?php echo json_encode($settings); ?>;
	
	setTimeout(update_timestamp_settings, 250);
</script>