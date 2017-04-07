<div id="title_def"><?php $lang('roomlist_title'); ?></div>
<?php $lang('roomlist_instructions'); ?>
<table class="data_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th><?php $lang('room_name'); ?></th>
			<th><?php $lang('room_topic'); ?></th>
			<th><?php $lang('room_actions'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($rooms as $room): ?>
			<tr>
				<td><?php $esc($room['name']); ?></td>
				<td><?php $esc($room['topic']); ?></td>
				<td><a href='#' class='join_room_button' data-room-id='<?php echo $room['id']; ?>' onclick="return false;"><?php $lang('join_room'); ?></a></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<script type="text/javascript">
	$('.join_room_button').bind('click', function() {
		var room_id = $(this).attr('data-room-id');
		var room = App.get_room(room_id, 'room');
		if(room)
		{
			App.set_active_room(room);
			close_content_area();
		}
		else
		{
			open_content_area('<?php $url('joinroom'); ?>&room_id=' + room_id);
		}
	});
</script>