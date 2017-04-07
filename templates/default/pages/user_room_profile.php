<?php if(!$user): ?>
	<div id="title_def"><?php $lang('user_not_found'); ?></div>
	<?php $lang('user_not_found'); ?>
	<?php $display('layout/messages'); ?>
<?php else: ?>
	<div id="title_def"><?php $esc($user['username']); ?></div>
	<?php $display('layout/messages'); ?>
	<?php if($user['real_name']): ?>
		<p><b><?php $lang('real_name_label'); ?></b></p>
		<p><?php $esc($user['real_name']); ?></p>
		<hr />
	<?php endif; ?>
	<?php if($user['gender']): ?>
		<p><b><?php $lang('gender_label'); ?></b></p>
		<p><?php $lang($user['gender']); ?></p>
		<hr />
	<?php endif; ?>
	<?php if($user['about']): ?>
		<p><b><?php $lang('bio_label'); ?></b></p>
		<p><?php $esc($user['about']); ?></p>
		<hr />
	<?php endif; ?>
	<?php if($show_ip && $user['ip']): ?>
		<p><b><?php $lang('ip_label'); ?></b></p>
		<p><?php $esc($user['ip']); ?><?php if($allow_ban): ?> - <a href="#" id="ip_ban" onclick="return false;"><?php $lang('ban_by_ip'); ?></a><?php endif; ?></p>
		<hr />
	<?php endif; ?>
	
	<p><a href="#" id="start_private_chat" onclick="return false;">Start private chat</a></p>
	<?php if($allow_ban): ?><p><a href="#" id="user_ban" onclick="return false;"><?php $lang('ban_user'); ?></a></p><?php endif; ?>
	
	<script type="text/javascript">
		$("#start_private_chat").bind('click', function() {
			var room = new App.Room({
				id: '<?php echo $user['id']; ?>',
				type: 'user',
				name: <?php echo json_encode($x7->esc($user['username'])); ?>
			});
			
			App.add_room(room);
			
			App.set_active_room(room);
			close_content_area();
		});
		
		$("#ip_ban").bind('click', function() {
			open_content_area('<?php $url('ban') ?>&by=ip&user_id=<?php echo $user['id']; ?>');
		});
		
		$("#user_ban").bind('click', function() {
			open_content_area('<?php $url('ban') ?>&by=account&user_id=<?php echo $user['id']; ?>');
		});
	</script>
<?php endif; ?>