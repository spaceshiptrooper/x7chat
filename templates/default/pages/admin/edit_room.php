<div id="title_def"><?php $lang('admin_rooms_title'); ?></div>
<?php $display('layout/adminmenu'); ?>
<div id="admin_content">
	<?php $display('layout/messages'); ?>
	<?php $display('layout/paginator', array('data' => isset($paginator) ? $paginator : array())); ?>
	<form class="standard_form" data-action="do_admin_edit_room">
		<input type="hidden" name="room_id" id="room_id" value="<?php $esc($val('room.id')); ?>" />

		<label for="name"><?php $lang('room_name'); ?></label>
		<input type="text" name="name" id="name" value="<?php $esc($val('room.name')); ?>" />
		<p>&nbsp;</p>
		
		<label for="topic""><?php $lang('room_topic'); ?></label>
		<input type="text" name="topic" id="topic" value="<?php $esc($val('room.topic')); ?>" />
		<p>&nbsp;</p>
		
		<label for="greeting""><?php $lang('room_greeting'); ?></label>
		<input type="text" name="greeting" id="greeting" value="<?php $esc($val('room.greeting')); ?>" />
		<p><?php $lang('room_greeting_instr'); ?></p>
		
		<label for="enable_password"><?php $lang('enable_room_password'); ?></label>
		<input type="checkbox" name="enable_password" id="enable_password" <?php if($val('room.password')) echo "checked"; ?> />
		<p>&nbsp;</p>
		
		<label for="password"><?php $lang('room_password'); ?></label>
		<input type="password" name="password" id="password" value="" />
		<p><?php $lang('room_password_instr'); ?></p>
		
		<input type="submit" value="<?php $lang('save_room_button'); ?>" />
	</form>
</div>