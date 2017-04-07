<?php
	$x7->load('user');
	
	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		$x7->fatal_error($x7->lang('login_required'));
	}
	
	$user = new x7_user();
	$perms = $user->permissions();
	if(empty($perms['access_admin_panel']))
	{
		$x7->fatal_error($x7->lang('access_denied'));
	}
	
	$filter = array();
	$error = false;
	
	if(empty($_POST['word']))
	{
		$x7->set_message($x7->lang('word_required'));
		$error = true;
	}
	else
	{
		$filter[':word'] = $_POST['word'];
	}
	
	$filter[':replacement'] = $_POST['replacement'];
	$filter[':whole_word_only'] = isset($_POST['whole_word_only']) ? (int)(bool)$_POST['whole_word_only'] : 0;
	
	if(!empty($_POST['id']))
	{
		$filter[':id'] = $_POST['id'];
	}
	
	if($error)
	{
		$x7->go('admin_edit_word_filter', array('filter' => $_POST));
	}
	else
	{
		if(!empty($filter[':id']))
		{
			$sql = "
				UPDATE {$x7->dbprefix}word_filters SET
					word = :word,
					replacement = :replacement,
					whole_word_only = :whole_word_only
				WHERE
					id = :id
				LIMIT 1
			";
		}
		else
		{
			$sql = "
				INSERT INTO {$x7->dbprefix}word_filters (word, replacement, whole_word_only) VALUES (:word, :replacement, :whole_word_only)";
		}
	
		$st = $db->prepare($sql);
		$st->execute($filter);

		$sql = "
			INSERT INTO {$x7->dbprefix}messages (timestamp, message_type, dest_type, dest_id, source_type, source_id) VALUES (:timestamp, :message_type, :dest_type, :dest_id, :source_type, :source_id)
		";
		$st = $db->prepare($sql);
		$st->execute(array(
			':timestamp' => date('Y-m-d H:i:s'), 
			':message_type' => 'filter_resync', 
			':dest_type' => 'user', 
			':dest_id' => 0, 
			':source_type' => 'system', 
			':source_id' => 0,
		));
		
		$x7->set_message($x7->lang('admin_filter_updated'), 'notice');
		$x7->go('admin_list_word_filters');
	}