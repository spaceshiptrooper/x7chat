<?php

	function force_admin_access($page)
	{
		global $x7;
		
		if(empty($_SESSION['user_id']))
		{
			$x7->fatal_error($x7->lang('login_required'));
			exit;
		}
		
		$user = new x7_user();
		$perms = $user->permissions();
		if(empty($perms['access_admin_panel']))
		{
			$x7->fatal_error($x7->lang('access_denied'));
			exit;
		}
		
		return $user;
	}

	function generate_admin_menu($current_page)
	{
		global $x7;
	
		$menu = array(
			'news' => array(
			),
			'settings' => array(
			),
			'word_filter' => array(
				'href' => 'admin_list_word_filters',
				'items' => array(
					'list_word_filters' => array(
					),
					'create_word_filter' => array(
						'href' => 'admin_edit_word_filter',
					),
					'edit_word_filter' => array(
						'hidden' => true,
					),
				),
			),
			'users' => array(
				'href' => 'admin_list_users',
				'items' => array(
					'list_users' => array(
					),
					'create_user' => array(
						'href' => 'admin_edit_user',
					),
					'edit_user' => array(
						'hidden' => true,
					),
					'delete_user' => array(
						'hidden' => true,
					),
				),
			),
			'rooms' => array(
				'href' => 'admin_list_rooms',
				'items' => array(
					'list_rooms' => array(
					),
					'create_room' => array(
						'href' => 'admin_edit_room',
					),
					'edit_room' => array(
						'hidden' => true,
					),
				),
			),
		);
		
		_process_admin_menu($menu, $current_page);
		
		return $menu;
	}
	
	function _process_admin_menu(&$menu, $current_page)
	{
		global $x7;
	
		$return = false;
		
		foreach($menu as $key => &$item)
		{
			if(empty($item['href']))
			{
				$item['href'] = 'admin_' . $key;
			}
			
			if(empty($item['label']))
			{
				$item['label'] = $x7->lang('admin_' . $key . '_button');
			}
			
			if(empty($item['hidden']))
			{
				$item['hidden'] = false;
			}
			
			$item['active'] = false;
			
			if(isset($item['items']))
			{
				if(_process_admin_menu($item['items'], $current_page))
				{
					$item['active'] = true;
					$return = true;
				}
			}
			else
			{
				$item['items'] = array();
			}
			
			if($key == $current_page)
			{
				$item['active'] = true;
				$return = true;
			}
		}
		
		return $return;
	}