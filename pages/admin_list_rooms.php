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
	
	$per_page = 10;
	$page = 1;
	if(isset($_GET['page']) && (int)$_GET['page'] >= 1)
	{
		$page = (int)$_GET['page'];
	}
	
	$sql = "
		SELECT
			COUNT(*) as num
		FROM {$x7->dbprefix}rooms
	";
	$st = $db->prepare($sql);
	$st->execute();
	$count = $st->fetch();
	$st->closeCursor();
	$pages = ceil($count['num'] / $per_page);
	
	$sql = "
		SELECT
			*
		FROM {$x7->dbprefix}rooms
	";
	$st = $db->prepare($sql);
	$st->execute();
	$rooms = $st->fetchAll();
	
	$pages = 5;
	
	$x7->display('pages/admin/rooms', array(
		'rooms' => $rooms,
		'paginator' => array(
			'per_page' => $per_page,
			'pages' => $pages,
			'page' => $page,
			'action' => 'admin_rooms',
		),
		'menu' => generate_admin_menu('list_rooms'),
	));