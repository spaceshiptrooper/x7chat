<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title><?php $esc($x7->config('title')); ?></title>
		<link rel="stylesheet" type="text/css" href="themes/<?php echo $x7->config('theme'); ?>/style.css" />
	</head>
	<body>
		<div id="page_wrapper"<?php if($x7->config('chat_size') == 'boxed') { ?> class="boxed"<?php } elseif($x7->config('chat_size') == 'full') { ?> class="wide"<?php } ?>>
			<div id="page_header">
				<div id="header_inner">
					<div id="page_logo">
						<?php $esc($x7->config('title')); ?>
					</div>
					<div id="header_menu">
						<ul>
							<?php if(!empty($_SESSION['user_id'])): ?>
								<li id="chatrooms_menu"><a href="#" onclick="return false;"><?php $lang('chatrooms_menu'); ?></a></li>
								<li id="settings_menu"><a href="#" onclick="return false;"><?php $lang('settings_menu'); ?></a></li>
								<?php
									if(isset($access_acp)):

										if($access_acp):
								?>
									<li id="admin_menu"><a href="#" onclick="return false;"><?php $lang("admin_control_panel_button"); ?></a></li>
								<?php

										endif;

									endif;
								?>
								<li id="logout_menu"><a href="#" onclick="return false;"><?php $lang('logout_menu'); ?></a></li>
							<?php else: ?>
								<li id="register_menu"><a href="<?php $url('register'); ?>"><?php $lang('register_button'); ?></a></li>
							<?php endif; ?>
						</ul>
					</div>
					<div style="clear: both;"></div>
				</div>
			</div>
			<div id="page_content">
				<div id="page_content_inner">
					<div class="inner_page">
						<?php $display('layout/messages'); ?>