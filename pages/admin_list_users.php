<?php
	$x7->load('user');
	$x7->load('admin');
	
	$db = $x7->db();
	$page_name = 'list_users';
	
	force_admin_access($page_name);
	
	$per_page = 15;
	$page = 1;
	if(isset($_GET['pg']) && (int)$_GET['pg'] >= 1)
	{
		$page = (int)$_GET['pg'];
	}
	
	$sql = "
		SELECT
			COUNT(*) as num
		FROM {$x7->dbprefix}users
	";
	$st = $db->prepare($sql);
	$st->execute();
	$count = $st->fetch();
	$st->closeCursor();
	$pages = ceil($count['num'] / $per_page);
	
	$start = $per_page*($page-1);
	$end = $start+$per_page;
	
	$sql = "
		SELECT
			*
		FROM {$x7->dbprefix}users
		ORDER BY
			username ASC
		LIMIT {$start}, {$end}
	";
	$st = $db->prepare($sql);
	$st->execute();
	$users = $st->fetchAll();
	
	$x7->display('pages/admin/users', array(
		'users' => $users,
		'paginator' => array(
			'per_page' => $per_page,
			'pages' => $pages,
			'page' => $page,
			'action' => 'admin_list_users?pg=',
		),
		'menu' => generate_admin_menu($page_name),
	));