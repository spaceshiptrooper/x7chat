<?php $display('layout/header'); ?>
	<script type="text/javascript" src="scripts/ko.js"></script>
	<script type="text/javascript" src="scripts/jquery.js"></script>
	<script type="text/javascript">
		var App = new function()
		{
			var app = this;
			
			this.settings = <?php echo json_encode($settings); ?>;
			
			this.filters = <?php echo json_encode($filters); ?>;
		
			this.Room = function(room)
			{
				this.id = room.id;
				this.type = room.type;
				this.name = room.name;
				this.messages = ko.observableArray();
				this.users = ko.observableArray();
				this.alert = ko.observable(0);
			}
			
			this.UserRoom = function(user)
			{
				this.user_id = user.user_id;
				this.room_id = user.room_id;
				this.user_name = user.user_name;
				this.refreshed = 1;
			}
			
			this.Message = function(message)
			{
				var dt = new Date();
				dt.setTime((parseInt(message.timestamp))*1000);
				
				var ampm = '';
				ampm = ' am';
				var hours = dt.getHours();
				if(hours >= 12)
				{
					ampm = ' pm';
				}
				
				if(!app.settings.ts_24_hour || app.settings.use_default_timestamp_settings)
				{
					if(hours > 12)
					{
						hours -= 12;
					}
					if(hours == 0)
					{
						hours = 12;
					}
				}
				
				if(!app.settings.ts_show_ampm && !app.settings.use_default_timestamp_settings)
				{
					ampm = '';
				}
				
				var minutes = ''+dt.getMinutes();
				if(minutes.length < 2)
				{
					minutes = '0' + minutes;
				}
				
				var seconds = ''+dt.getSeconds();
				if(seconds.length < 2)
				{
					seconds = '0' + seconds;
				}
				
				if(app.settings.enable_timestamps || app.settings.use_default_timestamp_settings)
				{
					this.timestamp = hours + ":" + minutes;
					if(app.settings.ts_show_seconds)
					{
						this.timestamp += ":" + seconds;
					}
					this.timestamp += ampm;
				}
				else
				{
					this.timestamp = '';
				}
				
				this.source_type = message.source_type;
				this.source_id = message.source_id;
				this.source_name = message.source_name;
				this.dest_type = message.dest_type;
				this.dest_id = message.dest_id;
				this.raw_message = message.message;
				
				this.size = '';
				if(!app.settings.enable_styles)
				{
					this.size = app.settings.message_font_size;
				}
				else if(message.font_size)
				{
					this.size = message.font_size;
				}
				
				this.face = '';
				if(!app.settings.enable_styles)
				{
					this.face = app.settings.message_font_face;
				}
				else if(message.font_face)
				{
					this.face = message.font_face;
				}
				
				this.color = '';
				if(!app.settings.enable_styles)
				{
					this.color = app.settings.message_font_color;
				}
				else if(message.font_color)
				{
					this.color = message.font_color;
				}
				
				var filtered_message = message.message;
				for(var key in app.filters)
				{
					var filter = app.filters[key];
					var find = filter.word.replace(/([.*+?^=!:${}()|[\]\/\\])/g, "\\$1");
					
					var repl = filter.replacement;
					if(!repl.length)
					{
						for(var len = 0; len < filter.word.length; len++)
						{
							repl += '*';
						}
					}
					
					if(filter.whole_word_only == "1")
					{
						find = "\\b" + find + "\\b";
					}
					
					var reg = new RegExp(find, "i");
					filtered_message = filtered_message.replace(reg, repl);
				}
				
				this.message = filtered_message;
			}
			
			this.add_room = function(room)
			{
				app.rooms.push(room);
				app.room_count(app.rooms().length);
			}
			
			this.get_room = function(id, type)
			{
				var rooms = app.rooms();
				for(var key in rooms)
				{
					if(rooms[key].id == id && rooms[key].type == type)
					{
						return rooms[key];
					}
				}
				
				return 0;
			}
			
			this.set_active_room = function(room)
			{
				room.alert(0);
				app.active_room(room);
				
				$("#active_rooms_area").slideUp('fast');
				app.active_rooms_area_open = false;
				
				$("#message_scroll_wrapper").scrollTop($("#messages").height());
				setTimeout(function() {
					$("#message_scroll_wrapper").scrollTop($("#messages").height());
				}, 100);
			}
			
			this.sync_room_users = function(data)
			{
				var rooms = app.rooms();
				for(var key in rooms)
				{
					for(var rkey in rooms[key].users())
					{
						var user = rooms[key].users()[rkey];
						user.refreshed = 0;
					}
				}
			
				var modified_rooms = [];
				for(var key in data)
				{
					var user_room = new App.UserRoom(data[key]);
					var room = app.get_room(user_room.room_id, 'room');
					if(room)
					{
						var user = 0;
						for(var rkey in room.users())
						{
							if(room.users()[rkey].user_id == user_room.user_id)
							{
								user = room.users()[rkey];
							}
						}
						
						if($.inArray(room.id, modified_rooms) == -1)
						{
							modified_rooms.push(room.id);
						}
						
						if(user)
						{
							user.refreshed = 1;
						}
						else
						{
							app.add_user_room(user_room);
						}
					}
				}
				
				for(var key in modified_rooms)
				{
					var room = app.get_room(modified_rooms[key], 'room');
					if(room)
					{
						for(var rkey in room.users())
						{
							var user = room.users()[rkey];
							if(!user.refreshed)
							{
								room.users.remove(user);
								
								var dt = new Date();
								var utc = Math.round(dt.getTime()/1000);
								
								var message = new App.Message({
									timestamp: utc,
									source_type: 'system',
									source_name: '',
									source_id: '0',
									dest_type: rooms[key].type, 
									dest_id: rooms[key].id, 
									message: <?php echo json_encode($x7->lang('leave_message')); ?>.replace(':user', user.user_name)
								});
								
								app.add_message(message);
							}
						}
					}
				}
			};
			
			this.add_user_room = function(user_room, supress_join_message)
			{
				var room = app.get_room(user_room.room_id, 'room');
				if(room)
				{
					room.users.push(user_room);
					
					if(!supress_join_message)
					{
						var dt = new Date();
						var utc = Math.round(dt.getTime()/1000);
						
						var message = new App.Message({
							timestamp: utc,
							source_type: 'system',
							source_name: '',
							source_id: '0',
							dest_type: room.type, 
							dest_id: room.id, 
							message: <?php echo json_encode($x7->lang('join_message')); ?>.replace(':user', user_room.user_name)
						});
						
						app.add_message(message);
					}
				}
			}
			
			this.add_message = function(message, supress_sounds)
			{
				var do_scroll = false;
				var messages_height = $("#messages").height();
				var messages_scroll = $("#message_scroll_wrapper").scrollTop();
				var message_pane_height = $("#message_scroll_wrapper").height();
				var at_bottom = (messages_height-messages_scroll <= message_pane_height);
				
				var room = 0;
				if(message.dest_type == 'user')
				{
					var check_id = message.dest_id;
					if(check_id == '<?php $esc($user['id']); ?>')
					{
						check_id = message.source_id;
					}
					
					if(message.source_type == 'system')
					{
						room = app.active_room();
					}
					else
					{
						room = app.get_room(check_id, message.dest_type);
					}
				}
				else
				{
					room = app.get_room(message.dest_id, message.dest_type);
				}
				
				if(!room.id)
				{
					if(message.dest_type == 'user' && message.source_type != 'system')
					{
						room = new App.Room({
							id: message.source_id,
							type: 'user',
							name: message.source_name
						});
						
						App.add_room(room);
					}
				}
				
				if(room)
				{
					room.messages.push(message);
					
					if(app.active_room() && app.active_room().id == room.id)
					{
						do_scroll = true;
					}
					else
					{
						room.alert(room.alert()+1);
					}
				}
				
				if(do_scroll && at_bottom)
				{
					$("#message_scroll_wrapper").scrollTop($("#messages").height());
				}
				
				// play sounds
				if(app.settings.enable_sounds && !supress_sounds)
				{
					try
					{
						$('#message_sound')[0].play();
					}
					catch(ex)
					{
					}
				}
			}
			
			this.send_message = function()
			{
				var dt = new Date();
				var utc = Math.round(dt.getTime()/1000);
				var message_input = jQuery.trim($('#message_input').val());

				if(message_input == '') {
				} else {

					var message = new App.Message({
						timestamp: utc,
						source_type: 'user',
						source_name: <?php echo json_encode($x7->esc($user['username'])); ?>,
						source_id: '<?php $esc($user['id']); ?>',
						dest_type: app.active_room().type, 
						dest_id: app.active_room().id, 
						message: $('#message_input').val(),
						font_size: app.settings.message_font_size,
						font_color: app.settings.message_font_color,
						font_face: app.settings.message_font_face
					});
					
					app.add_message(message, 1);
				
					$.ajax({
						url: '<?php $url('send'); ?>',
						cache: false,
						type: 'POST',
						dataType: 'json',
						data: {
							dest_type: message.dest_type,
							room: message.dest_id,
							message: message.message
						},
						success: function(data)
						{
							
						}
					});

				}
				
				$('#message_input').val('');
				$('#message_input').focus();
			}
			
			this.message_key = function(p1, ev)
			{
				var alt_func = (ev.shiftKey || ev.ctrlKey || ev.altKey);
				var enter_key = (13 == ev.keyCode);
				if(enter_key && !alt_func)
				{
					app.send_message();
					return false;
				}
				
				return true;
			}
			
			this.show_rooms = function()
			{
				var top = $("#room_tab_bar").position().top+$("#room_tabs_button").height()+5;
				var left = $("#room_tabs_button").position().left-11;
				
				$("#active_rooms_area").css("top", top);
				$("#active_rooms_area").css("left", left);
				$("#active_rooms_area").slideDown('fast', function() {
					app.active_rooms_area_open = true;
				});
			}
			
			this.show_user_profile = function(data)
			{
				open_content_area('<?php $url('user_room_profile'); ?>' + '&user_id=' + data.user_id + '&room_id=' + data.room_id);
			}
			
			this.leave_room = function(data)
			{
				if(data.type == 'room')
				{
					$.ajax({
						url: '<?php $url('leaveroom'); ?>',
						cache: false,
						type: 'POST',
						dataType: 'json',
						data: {
							room: data.id
						}
					});
				}
				
				var need_switch = false;
				if(app.active_room() && app.active_room().id == data.id)
				{
					need_switch = true;
				}
				
				var rooms = app.rooms();
				for(var key in rooms)
				{
					if(rooms[key].id == data.id)
					{
						app.rooms.remove(rooms[key]);
					}
				}
				
				app.room_count(app.rooms().length);
				
				if(need_switch)
				{
					app.active_room(0);
					
					if(app.rooms().length > 0)
					{
						app.active_room(app.rooms()[0]);
					}
				}
				
				if(app.rooms().length == 0)
				{
					open_content_area('<?php $url('roomlist'); ?>');
				}
			}
		
			this.active_rooms_area_open = false;
			this.room_count = ko.observable(0);
			this.active_room = ko.observable();
			this.rooms = ko.observableArray();
		}
		
		setInterval(function() {
			$.ajax({
				url: '<?php $url('sync'); ?>',
				cache: false,
				type: 'POST',
				dataType: 'json',
				data: {
				},
				success: function(data)
				{
					if(data['redirect'])
					{
						window.location = data['redirect'];
						return;
					}
					
					if(data['showcontent'])
					{
						open_content_area(data['showcontent']);
					}
						
					if(data['events'])
					{
						for(var key in data['events'])
						{
							if(data['events'][key].message_type == 'message')
							{
								var message = new App.Message(data['events'][key]);
								App.add_message(message);
							}
						}
					}
					
					if(data['users'])
					{
						App.sync_room_users(data['users']);
					}
					
					if(data['filters'])
					{
						App.filters = data['filters'];
					}
				}
			});
		}, 2000);
		
		$(function() {
			ko.applyBindings(App, $('#chat_area')[0]);
			
			$("#send_button").attr('value', "WHAT?");
			$("#send_button").bind('click', function() {
				alert("SEND");
			});
		
			$("#chatrooms_menu").bind('click', function() {
				open_content_area('<?php $url('roomlist'); ?>');
			});
			
			$("#admin_menu").bind('click', function() {
				open_content_area('<?php $url('admin_news'); ?>');
			});
			
			$("#settings_menu").bind('click', function() {
				open_content_area('<?php $url('settings'); ?>');
			});
			
			$("#logout_menu").bind('click', function() {
				window.location = '<?php $url('logout'); ?>';
			});
			
			$("#close").bind('click', function() {
				close_content_area();
			});
			
			if(!App.active_room())
			{
				open_content_area('<?php $url('roomlist'); ?>');
			}
			
			$('body').bind('click', function(ev) {
				if(App.active_rooms_area_open && $(ev.target).attr("id") !== 'active_rooms_area')
				{
					$("#active_rooms_area").slideUp('fast');
					App.active_rooms_area_open = false;
				}
			});
			
			$(window).bind('unload', function() {
				$.ajax({
					url: '<?php $url('leaverooms'); ?>',
					cache: false,
					type: 'POST',
					dataType: 'json',
					async: false
				});
			});
			
			<?php if(!empty($auto_join)): ?>
				open_content_area('<?php $url('joinroom?room_id=' . $auto_join); ?>');
			<?php endif; ?>
		});
		
		function open_content_area(url, postdata)
		{
			$("#content_area").slideDown();
			$("#chat_area").slideUp();
			$('#close').show();
			
			var handle_page = function(data)
			{
				if(data)
				{
					$("#content_page").html(data);
				}
			
				var title = $('#content_page #title_def').text();
				$('#title').text(title);
				
				$("#content_page *[data-href]").each(function() {
					$(this).bind('click', function() {
						var url = $(this).attr('data-href');
						var page = url;
						var query = '';
						if(url.indexOf('?') >= 0)
						{
							page = url.substring(0, url.indexOf('?'));
							query = url.substring(url.indexOf('?')+1);
						}
						
						open_content_area('?page=' + page + '&' + query);
						return false;
					});
				});
				
				$("#content_page form[data-action]").each(function() {
					$(this).bind('submit', function() {
						open_content_area('?page=' + $(this).attr('data-action'), $(this).serialize());
						return false;
					});
				});
				
				if(!App.active_room())
				{
					$('#close').hide();
				}
				
				$('#content_page').scrollTop(0);
			}
			
			if(postdata)
			{
				$.post(url, postdata, function(data)
				{
					handle_page(data);
				});
			}
			else
			{
				$('#content_page').load(url, function()
				{
					handle_page();
				});
			}
		}
		
		function close_content_area()
		{
			$("#content_area").slideUp();
			$("#chat_area").slideDown();
			setTimeout(function() {
				$("#message_scroll_wrapper").scrollTop($("#messages").height());
			}, 500);
		}
	</script>
	<div id="content_area">
		<div id="content_title_bar">
			<div id="title"></div>
			<div id="close"><a href="#" onclick="return false;"><?php $lang('close_content_button'); ?></a></div>
			<div style="clear: both;"></div>
		</div>
		<div id="content_page"></div>
	</div>
	<div id="chat_area">
		<div id="active_rooms_area">
			<p><?php $lang('all_rooms_text'); ?></p>
			<br />
			<ul data-bind="foreach: rooms">
				<li><a href="#" data-bind="text: name, click: $root.set_active_room" onclick="return false;"></a> <span data-bind="if: type == 'user'" class="private_chat_text">(private)</span> - <a href="#" data-bind="click: $root.leave_room" onclick="return false;"><?php $lang('leave_room'); ?></a></li>
			</ul>
			<br />
			<p><?php $lang('all_rooms_join_other_rooms'); ?></p>
		</div>
		<div id="room_tab_bar">
			<div id="current_room_button">
				<!-- ko if: active_room() -->
					<!-- ko if: active_room().type == 'user' -->
						<b><?php $lang('private_chat'); ?>: </b>
					<!-- /ko -->
					<!-- ko if: active_room().type == 'room' -->
						<b><?php $lang('current_room'); ?>: </b>
					<!-- /ko -->
					<span data-bind="text: active_room().name"></span>
				<!-- /ko -->
			</div>
			<div id="room_tabs_button">
				<a href="#" data-bind="click: show_rooms" onclick="return false;"><?php $lang('all_rooms'); ?></a> (<span data-bind="text: room_count()"></span>)
			</div>
			<div id="alert_rooms">
				<ul data-bind="foreach: rooms">
					<!-- ko if: alert -->
						<li data-bind="click: $root.set_active_room"><a href="#" data-bind="text: name" onclick="return false;"></a> <span data-bind="if: type == 'user'" class="private_chat_text"> - private</span> (<span data-bind="text: alert"></span>)</li>
					<!-- /ko -->
				</ul>
			</div>
			<div style="clear: both;"></div>
		</div>
		<div style="clear: both;"></div>
		<!-- ko if: active_room() -->
			<div id="message_scroll_wrapper">
				<div id="messages" data-bind="foreach: active_room().messages()">
					<div class="message_container"><span class="timestamp" data-bind="text: timestamp"></span>
						<!-- ko if: source_type != 'system' -->
							<span class="sender" data-bind="text: source_name + ':'"></span> 
							<span class="message" data-bind="text: message, style: { color: color ? '#' + color : '', fontSize: size > 0 ? size + 'px' : '', fontFamily: face ? face : ''}"></span>
						<!-- /ko -->
						<!-- ko if: source_type == 'system' -->
							<span class="sender system_sender"><?php $lang('system_sender'); ?>: </span>
							<span class="message system_message" data-bind="text: message"></span>
						<!-- /ko -->
					</div>
				</div>
			</div>
			<div id="onlinelist" data-bind="foreach: active_room().users()">
				<div class="onlineuser" data-bind="click: $root.show_user_profile"><a href='#' data-bind="text: user_name"></a></div>
			</div>
			<div style="clear: both;"></div>
			<div id="input_form">
				<textarea class="chat-message" id="message_input" data-bind="event: {keypress: message_key}"></textarea>
				<input class="chat-button" type="button" id="send_button" value="<?php $lang('send_button'); ?>" data-bind="click: send_message" />
				<div style="clear: both;"></div>
			</div>
		<!-- /ko -->
	</div>
	<audio id="message_sound">
	   <source src="sounds/message.ogg" type='audio/ogg; codecs="vorbis"'>
	   <source src="sounds/message.mp3" type='audio/mpeg; codecs="mp3"'>
	</audio>
	<audio id="enter_sound">
	   <source src="sounds/enter.ogg" type='audio/ogg; codecs="vorbis"'>
	   <source src="sounds/enter.mp3" type='audio/mpeg; codecs="mp3"'>
	</audio>
<?php $display('layout/footer'); ?>