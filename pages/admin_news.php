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
	
	$installed_version = x7chat::VERSION;
	$cur_stable = '';
	$cur_unstable = '';
	$news = array();
	
	$timeout = array('http' => array('timeout' => 3));
	$context = stream_context_create($timeout);
	$updates = @file_get_contents('http://www.x7chat.com/updates/v3.php', false, $context);
	if($updates)
	{
		$updates = json_decode($updates);
		$cur_stable = $updates->stable_release;
		$cur_unstable = $updates->unstable_release;
		$news = $updates->news;
	}
	
	$x7->display('pages/admin/news', array(
		'installed_version' => $installed_version,
		'cur_stable' => $cur_stable,
		'cur_unstable' => $cur_unstable,
		'news' => $news,
		'menu' => generate_admin_menu('news'),
	));