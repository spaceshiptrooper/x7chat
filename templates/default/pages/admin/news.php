<div id="title_def"><?php $lang('admin_news_title'); ?></div>
<?php $display('layout/adminmenu'); ?>
<div id="admin_content">
	<table class="data_table" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="2"><?php $lang('version_information'); ?></th>
			
		</thead>
		<tbody>
			<tr>
				<td><b><?php $lang('installed_version'); ?></b></td>
				<td><?php echo $installed_version; ?></td>
			</tr>
			<tr>
				<td><b><?php $lang('latest_stable_version'); ?></b></td>
				<td><?php echo $cur_stable; ?></td>
			</tr>
			<tr>
				<td><b><?php $lang('latest_unstable_version'); ?></b></td>
				<td><?php echo $cur_unstable; ?></td>
			</tr>
		</tbody>
	</table>
	<h3><?php $lang('news'); ?></h3>
	<?php foreach($news as $entry): ?>
		<b><?php echo $entry->title; ?></b>
		<p><i><?php echo $entry->date; ?></i></p>
		<?php echo $entry->body; ?>
		<hr />
	<?php endforeach; ?>
</div>