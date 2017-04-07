<?php
	$x7->load('user');
	$x7->load('admin');
	
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
	
	$id = isset($_GET['id']) ? $_GET['id'] : 0;
	if(!$id)
	{
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
	}
	
	$filter = array();
	
	if($id)
	{
		$sql = "SELECT * FROM {$x7->dbprefix}word_filters WHERE id = :id";
		$st = $db->prepare($sql);
		$st->execute(array(
			':id' => $id,
		));
		$filter = $st->fetch();
		$st->closeCursor();
	}
	
	$x7->display('pages/admin/edit_word_filter', array(
		'menu' => generate_admin_menu($id ? 'edit_word_filter' : 'create_word_filter'),
		'filter' => $filter,
	));