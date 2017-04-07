<script type="text/javascript">
	var users = <?php echo json_encode($users); ?>;
	var room = new App.Room(<?php echo json_encode($room); ?>);
	var messages = <?php echo json_encode($messages); ?>;
	room.type = 'room';
	
	App.add_room(room);
	
	for(var key in users)
	{
		App.add_user_room(users[key], 1);
	}
	
	for(var key in messages)
	{
		var message = new App.Message(messages[key]);
		App.add_message(message);
	}
	
	App.set_active_room(room);
	close_content_area();
</script>