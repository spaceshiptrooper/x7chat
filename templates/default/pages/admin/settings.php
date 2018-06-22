<div id="title_def"><?php $lang('admin_settings_title'); ?></div>
<?php $display('layout/adminmenu'); ?>
<div id="admin_content">
	<?php $display('layout/messages'); ?>
	<form class="standard_form" data-action="do_admin_settings">
		<label for="chatroom_title"><?php $lang('chatroom_title'); ?></label>
		<input type="text" name="title" id="chatroom_title" value="<?php $esc($config['title']); ?>" />
		<p><?php $lang('chatroom_title_instr'); ?></p>
		
		<label for="auto_join"><?php $lang('auto_join'); ?></label>
		<select name="auto_join" id="auto_join">
			<option value="0"><?php $lang('disable_auto_join'); ?></option>
			<?php foreach($rooms as $room): ?>
				<option <?php if($config['auto_join'] == $room['id']) echo "selected"; ?> value="<?php echo $room['id']; ?>"><?php $esc($room['name']); ?></option>
			<?php endforeach; ?>
		</select>
		<p><?php $lang('auto_join_instr'); ?></p>
		
		<label for="allow_guests"><?php $lang('allow_guests'); ?></label>
		<input type="checkbox" name="allow_guests" id="allow_guests" <?php if($config['allow_guests']) echo "checked"; ?> />
		<p><?php $lang('allow_guests_instr'); ?></p>

		<label for="chat_size"><?php $lang('chat_size'); ?></label>
		<select name="chat_size" id="chat_size">
			<option <?php if($config['chat_size'] == 'boxed') echo "selected"; ?> value="0">Boxed</option>
			<option <?php if($config['chat_size'] == 'full') echo "selected"; ?> value="1">Full</option>
		</select>
		<p><?php $lang('chat_size_instr'); ?></p>

		<label for="login_page_news"><?php $lang('login_page_news'); ?></label>
		<textarea name="login_page_news" id="login_page_news" rows="5" cols="25"><?php $esc($config['login_page_news']); ?></textarea>
		<p><?php $lang('login_page_news_instr'); ?></p>
		
		<label for="min_font_size"><?php $lang('min_font_size'); ?></label>
		<input type="text" name="min_font_size" id="min_font_size" value="<?php $esc($config['min_font_size']); ?>" />
		<p><?php $lang('min_font_size_instr'); ?></p>
		
		<label for="max_font_size"><?php $lang('max_font_size'); ?></label>
		<input type="text" name="max_font_size" id="max_font_size" value="<?php $esc($config['max_font_size']); ?>" />
		<p><?php $lang('max_font_size_instr'); ?></p>
		
		<label for="from_address"><?php $lang('from_address'); ?></label>
		<input type="text" name="from_address" id="from_address" value="<?php $esc($config['from_address']); ?>" />
		<p><?php $lang('from_address_instr'); ?></p>
		
		<label for="use_smtp"><?php $lang('use_smtp'); ?></label>
		<input type="checkbox" name="use_smtp" id="use_smtp" <?php if($config['use_smtp']) echo "checked"; ?> />
		<p><?php $lang('use_smtp_instr'); ?></p>
		
		<label for="smtp_host"><?php $lang('smtp_host'); ?></label>
		<input type="text" name="smtp_host" id="smtp_host" value="<?php $esc($config['smtp_host']); ?>" />
		<p><?php $lang('smtp_host_instr'); ?></p>
		
		<label for="smtp_user"><?php $lang('smtp_user'); ?></label>
		<input type="text" name="smtp_user" id="smtp_user" value="<?php $esc($config['smtp_user']); ?>" />
		<p><?php $lang('smtp_user_instr'); ?></p>
		
		<label for="smtp_pass"><?php $lang('smtp_pass'); ?></label>
		<input type="password" name="smtp_pass" id="smtp_pass" value="<?php $esc($config['smtp_pass']); ?>" />
		<p><?php $lang('smtp_pass_instr'); ?></p>
		
		<label for="smtp_port"><?php $lang('smtp_port'); ?></label>
		<input type="text" name="smtp_port" id="smtp_port" value="<?php $esc($config['smtp_port']); ?>" />
		<p><?php $lang('smtp_port_instr'); ?></p>
		
		<label for="smtp_mode"><?php $lang('smtp_mode'); ?></label>
		<input type="text" name="smtp_mode" id="smtp_mode" value="<?php $esc($config['smtp_mode']); ?>" />
		<p><?php $lang('smtp_mode_instr'); ?></p>
		
		<input type="submit" id="submit" value="<?php $lang('save_settings_button'); ?>" />
	</form>
</div>
<script type="text/javascript">
jQuery(document).ready(function() {

	$('#submit').on('click', function() {

		var chat_size = $('#chat_size').val();
		if(chat_size == 0) {
			$('#page_wrapper').addClass('boxed');
			$('#page_wrapper').removeClass('wide');
		} else {
			$('#page_wrapper').addClass('wide');
			$('#page_wrapper').removeClass('boxed');
		}

	});

});
</script>
