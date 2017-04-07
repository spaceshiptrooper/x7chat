<?php
	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		die(json_encode(array('redirect' => $x7->url('login'))));
	}
	
	$local_sync_time = 11;
	$global_sync_time = 31;
	$user_expiration_time = 61;

	$last_event_id = isset($_SESSION['last_event_id']) ? $_SESSION['last_event_id'] : 0;
	$orig_last_event_id = $last_event_id;
	
	$last_global_sync_time = isset($_SESSION['last_global_sync_time']) ? $_SESSION['last_global_sync_time'] : 0;
	$last_local_sync_time = isset($_SESSION['last_local_sync_time']) ? $_SESSION['last_local_sync_time'] : 0;
	$server_rooms = isset($_SESSION['rooms']) ? $_SESSION['rooms'] : array();
	
	$process_rooms = $server_rooms;
	$process_rooms[] = 0;
	$rooms = implode(',', $process_rooms);
	
	$user_id = $_SESSION['user_id'];
	
	if(empty($last_event_id))
	{
		$sql = "
			SELECT
				MAX(id) as id
			FROM {$x7->dbprefix}messages
		";
		$st = $db->prepare($sql);
		$st->execute();
		$row = $st->fetch();
		$st->closeCursor();
		if($row)
		{
			$last_event_id = $row['id'];
			$_SESSION['last_event_id'] = $row['id'];
		}
	}
	
	// refresh the user's last update time
	if($last_local_sync_time < time() - $local_sync_time)
	{
		$_SESSION['last_local_sync_time'] = time();
		
		$sql = "
			UPDATE {$x7->dbprefix}users
			SET
				timestamp = :timestamp,
				ip = :ip
			WHERE
				id = :user_id
		";
		$st = $db->prepare($sql);
		$st->execute(array(
			':user_id' => $user_id,
			':timestamp' => date('Y-m-d H:i:s'),
			':ip' => $_SERVER['REMOTE_ADDR'],
		));
	}
	
	// cleared expired users from the online table
	if($last_global_sync_time < time() - $global_sync_time)
	{
		$_SESSION['last_global_sync_time'] = time();
	
		$sql = "
			DELETE room_user.* FROM {$x7->dbprefix}room_users room_user
			INNER JOIN {$x7->dbprefix}users user ON
				user.id = room_user.user_id
				AND user.timestamp < :expires
		";
		$st = $db->prepare($sql);
		$st->execute(array(
			'expires' => date("Y-m-d H:i:s", time() - $user_expiration_time),
		));
		$removed = $st->rowCount();
		if($removed > 0)
		{
			$sql = "
				INSERT INTO {$x7->dbprefix}messages (timestamp, message_type, dest_type, dest_id, source_type, source_id) VALUES (:timestamp, :message_type, :dest_type, :dest_id, :source_type, :source_id)
			";
			$st = $db->prepare($sql);
			$st->execute(array(
				':timestamp' => date('Y-m-d H:i:s'), 
				':message_type' => 'room_resync', 
				':dest_type' => 'room', 
				':dest_id' => 0, 
				':source_type' => 'system', 
				':source_id' => 0,
			));
		}
	}
	
	// pull new messages
	$sql = "
		SELECT
			message.*,
			user.username AS source_name
		FROM {$x7->dbprefix}messages message
		LEFT JOIN {$x7->dbprefix}users user ON
			message.source_type = 'user'
			AND user.id = message.source_id
		WHERE
			message.id > :last_event_id
			AND
			(
				(
					message.dest_type = 'room'
					AND
					message.dest_id IN ({$rooms})
				)
				OR
				(
					message.dest_type = 'user'
					AND
					message.dest_id IN (0,:user_id)
				)
			)
			AND NOT
			(
				message.source_type = 'user'
				AND
				message.source_id = :user_id
			)
	";
	$st = $db->prepare($sql);
	$st->execute(array(':user_id' => $user_id, ':last_event_id' => $last_event_id));
	$events = $st->fetchAll();
	
	$output = array();
	
	$do_resync = false;
	$filter_resync = false;
	foreach($events as $key => $event)
	{
		$events[$key]['timestamp'] = strtotime($event['timestamp']);
	
	
		if($event['id'] > $_SESSION['last_event_id'])
		{
			$_SESSION['last_event_id'] = $event['id'];
		}
		
		if($event['message_type'] == 'room_resync')
		{
			$_SESSION['last_global_sync_time'] = time();
			$do_resync = true;
			unset($events[$key]);
		}
		
		if($event['message_type'] == 'filter_resync')
		{
			$filter_resync = true;
			unset($events[$key]);
		}
		
		if($event['message_type'] == 'ban_resync')
		{
			$x7->load('user');
			$user_ob = new x7_user();
			if($user_ob->banned() || x7_check_ip_bans())
			{
				$user_ob->leave_rooms();
				
				$_SESSION['user_id'] = 0;
				session_destroy();
				session_start();
				
				$x7->set_message($x7->lang('login_failed_banned'));
				die(json_encode(array('redirect' => $x7->url('login'))));
			}
		}
		
		if($event['message_type'] == 'logout')
		{
			die(json_encode(array('redirect' => $x7->url('leaverooms'))));
		}
	}
	
	$output['events'] = $events;
	
	if($filter_resync)
	{
		$sql = "
			SELECT
				*
			FROM {$x7->dbprefix}word_filters
			ORDER BY
				LENGTH(word) DESC
		";
		$st = $db->prepare($sql);
		$st->execute();
		$filters = $st->fetchAll();
		$output['filters'] = $filters;
	}
	
	if($do_resync)
	{
		$sql = "
			SELECT
				room_user.*,
				user.username AS user_name
			FROM {$x7->dbprefix}room_users room_user
			INNER JOIN {$x7->dbprefix}users user ON
				user.id = room_user.user_id
			WHERE
				room_id IN ({$rooms})
		";
		$st = $db->prepare($sql);
		$st->execute();
		$users = $st->fetchAll();
		$output['users'] = $users;
	}
	
	echo json_encode($output);