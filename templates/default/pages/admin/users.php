<div id="title_def"><?php $lang('admin_users_title'); ?></div>
<?php $display('layout/adminmenu'); ?>
<div id="admin_content">
	<?php $display('layout/messages'); ?>
	<?php if(empty($users)): ?>
		<i><?php $lang('no_users'); ?></i>
	<?php else: ?>
		<table class="data_table" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th><?php $lang('username'); ?></th>
					<th><?php $lang('actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($users as $user): ?>
					<tr>
						<td><?php $esc($user['username']); ?></td>
						<td>
							<a href="#" data-href="admin_edit_user&id=<?php echo $user['id']; ?>"><?php $lang('edit'); ?></a> | <a href="#" data-href="admin_delete_user&id=<?php echo $user['id']; ?>"><?php $lang('delete'); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<?php $display('layout/paginator', array('data' => $paginator)); ?>
</div>