<?php

	class x7_exception extends exception
	{
	}

	class x7_user
	{
		protected $x7;
		
		protected $by;
		protected $by_id;
		protected $loaded = false;
		protected $db_data;
	
		public function __construct($id = null, $by = null)
		{
			global $x7;
			$this->x7 = $x7;
		
			if($id === null)
			{
				$id = !empty($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
				$by = 'id';
			}
			elseif($by === null)
			{
				$by = 'id';
			}
			elseif(!in_array($by, array('id', 'username', 'email', 'reset_password')))
			{
				throw new x7_exception("Invalid value for `by` parameter");
			}
			elseif(empty($id))
			{
				throw new x7_exception("Invalid parameter value for `id` parameter");
			}
			
			$this->by_id = $id;
			$this->by = $by;
			$this->loaded = false;
		}
		
		public function get_settings()
		{
			$data = $this->data();
			
			$keys = array(
				'enable_sounds',
				'use_default_timestamp_settings',
				'enable_timestamps',
				'ts_24_hour',
				'ts_show_seconds',
				'ts_show_ampm',
				'ts_show_date',
				'enable_styles',
				'message_font_size',
				'message_font_color',
				'message_font_face',
			);
			
			$settings = array();
			foreach($keys as $key)
			{
				$settings[$key] = $data[$key];
				if(!$settings[$key])
				{
					$settings[$key] = false;
				}
			}
			
			return $settings;
		}
		
		public function data()
		{
			$this->load();
			return $this->db_data;
		}
		
		public function db()
		{
			return $this->x7->db();
		}
		
		public function load()
		{
			if(!$this->loaded)
			{
				$db = $this->db();
				$sql = "
					SELECT
						*
					FROM {$this->x7->dbprefix}users
					WHERE {$this->by} = :value
					LIMIT 1;
				";
				$st = $db->prepare($sql);
				$st->execute(array(
					':value' => $this->by_id
				));
				$this->db_data = $st->fetch();
				$st->closeCursor();
			}
			
			if(!$this->db_data)
			{
				throw new x7_exception("Invalid parameter value for `id` paramter");
			}
		}
		
		public function id()
		{
			if($this->by === 'id')
			{
				return (int)$this->by_id;
			}
			
			$this->load();
			
			return $this->db_data['id'];
		}
		
		public function permissions()
		{
			$id = $this->id();
			
			$sql = "
				SELECT
					`group`.access_admin_panel,
					`group`.create_room
				FROM {$this->x7->dbprefix}groups `group`
				INNER JOIN {$this->x7->dbprefix}users user ON
					user.id = :user_id
					AND user.group_id = group.id
				LIMIT 1;
			";
			$st = $this->db()->prepare($sql);
			$st->execute(array(
				':user_id' => $id
			));
			$row = $st->fetch();
			$st->closeCursor();
			
			if($row)
			{
				return $row;
			}
			
			return array();
		}
		
		public function banned()
		{
			if($this->id() === 1)
			{
				return false;
			}
			
			$this->load();
			
			return (bool)$this->db_data['banned'];
		}
		
		public function leave_rooms()
		{
			$user_id = $this->id();
		
			$rooms = isset($_SESSION['rooms']) ? $_SESSION['rooms'] : array();
			if(empty($rooms))
			{
				return true;
			}
			
			$rooms_str = implode(',', $rooms);
			$sql = "
				DELETE FROM {$this->x7->dbprefix}room_users
				WHERE
					room_id IN ({$rooms_str})
					AND user_id = :user_id
			";
			$st = $this->db()->prepare($sql);
			$st->execute(array(':user_id' => $user_id));
			
			foreach($rooms as $room)
			{
				$sql = "
					INSERT INTO {$this->x7->dbprefix}messages (timestamp, message, message_type, dest_type, dest_id, source_type, source_id) VALUES (:timestamp, :message, :message_type, :dest_type, :dest_id, :source_type, :source_id)
				";
				$st = $this->db()->prepare($sql);
				$st->execute(array(
					':timestamp' => date('Y-m-d H:i:s'), 
					':message_type' => 'room_resync', 
					':message' => 'leave_rooms',
					':dest_type' => 'room', 
					':dest_id' => $room, 
					':source_type' => 'system', 
					':source_id' => 0,
				));
			}
			
			return true;
		}
	}
	
	function x7_check_ip_bans()
	{
		global $x7;
		$db = $x7->db();
	
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$sql = "SELECT * FROM {$x7->dbprefix}bans";
		$st = $db->prepare($sql);
		$st->execute();
		$bans = $st->fetchAll();
		foreach($bans as $ban)
		{
			if(strpos($ban['ip'], '*') !== FALSE)
			{
				$ban['ip'] = preg_quote($ban['ip']);
				$ban['ip'] = str_replace('\*', '(.+?)', $ban['ip']);
				$ban['ip'] = str_replace('#', '\#', $ban['ip']);
				if(preg_match('#' . $ban['ip'] . '#i', $ip))
				{
					return true;
				}
			}
			elseif($ban['ip'] == $ip)
			{
				return true;
			}
		}
		
		return false;
	}

	// @deprecated
	function x7_get_user_id()
	{
		if(!empty($_SESSION['user_id']))
		{
			return (int)$_SESSION['user_id'];
		}
		
		return 0;
	}

	// @deprecated
	function x7_get_user_permissions($id = null)
	{
		global $x7;
		$db = $x7->db();
		
		if(!$id)
		{
			$id = x7_get_user_id();
		}
		
		if(!$id)
		{
			return array();
		}
		
		$sql = "
			SELECT
				`group`.access_admin_panel,
				`group`.create_room
			FROM {$x7->dbprefix}groups `group`
			INNER JOIN {$x7->dbprefix}users user ON
				user.id = :user_id
				AND user.group_id = group.id
			LIMIT 1;
		";
		$st = $db->prepare($sql);
		$st->execute(array(
			':user_id' => $id
		));
		$row = $st->fetch();
		$st->closeCursor();
		
		if($row)
		{
			return $row;
		}
		
		return array();
	}

	// @deprecated
	function x7_get_user($id = null, $by = 'id')
	{
		global $x7;
		$db = $x7->db();
		
		if(!$id)
		{
			$id = x7_get_user_id();
		}
		
		if(!$id)
		{
			return array();
		}
		
		$sql = "
			SELECT
				id,
				username,
				email,
				group_id,
				banned,
				timestamp,
				ip,
				real_name,
				gender,
				about
			FROM {$x7->dbprefix}users
			WHERE {$by} = :value
			LIMIT 1;
		";
		$st = $db->prepare($sql);
		$st->execute(array(':value' => $id));
		$row = $st->fetch();
		$st->closeCursor();
		return $row;
	}