<div id="title_def"><?php $lang('confirm_ban_user'); ?></div>
<?php if($by == 'ip'): ?>
	<p><?php $lang('ban_confirm_ip'); ?></p>
<?php else: ?>
	<p><?php $lang('ban_confirm_account'); ?></p>
<?php endif; ?>
<p><?php $lang('username_label'); ?>: <?php $esc($user['username']); ?></p>
<?php if($by == 'ip'): ?>
	<p><?php $lang('ip_label'); ?>: <?php $esc($user['ip']); ?></p>
<?php else: ?>
	<p><?php $lang('user_id_label'); ?>: <?php $esc($user['id']); ?></p>
<?php endif; ?>
<hr />
<p><a href="#" id="continue_ban" onclick="return false;"><?php $lang('continue_ban'); ?></a></p>
<p><a href="#" id="cancel_ban" onclick="return false;"><?php $lang('cancel_ban'); ?></a></p>

<script type="text/javascript">
	$("#continue_ban").bind('click', function() {
		open_content_area('<?php $url('doban') ?>&by=<?php echo $by; ?>&user_id=<?php echo $user['id']; ?>');
	});
	
	$("#cancel_ban").bind('click', function() {
		open_content_area('<?php $url('user_room_profile') ?>&user_id=<?php echo $user['id']; ?>');
	});
</script>