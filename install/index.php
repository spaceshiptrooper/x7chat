<?php

	// X X7 Chat 3 (always 3)
	// M major chat version
	// m minor chat version
	// T release type (1 = alpha, 3 = beta, 5 = release candidate, 7 = final)
	// b build number (resets to 0 each time any other digit increases)
	//                XMMmmTbb
	define('VERSION', 30300101);
	//              3.03.00a01

	ini_set('display_errors', 'on');
	error_reporting(E_ALL);

	date_default_timezone_set('UTC');

	if(PHP_VERSION < '5.4.9') {

		// Only need the alternative password file if the PHP version is 5.4.9 and below
		// password_hash is available in 5.5 so this file is not really needed.
		require('./includes/libraries/password_compat/lib/password.php');

	}

	require('util.php');
	
	session_start();
	
	// Initialize a new install workflow
	if(empty($_SESSION['x7chat_install']) || isset($_GET['restart']))
	{
		$_SESSION['x7chat_install'] = array(
			'step' => 0,
			'error' => '',
			'type' => 'install',
			'prev_version' => null,
		);
	}
	
	// Load the database connection if possible
	$db = false;
	if(file_exists('../config.php'))
	{
		$config = require('../config.php');
		if(is_array($config) && !empty($config['dbname']))
		{
			try
			{
				$db = db_connection($config);
			}
			catch(Exception $ex)
			{
				$_SESSION['x7chat_install']['error'] = "Database connection failed: {$ex->getMessage()}";
			}
		}
	}
	
	// Check to see whether this is an upgrade
	if(empty($_SESSION['x7chat_install']['error']) && $_SESSION['x7chat_install']['step'] === 0 && $db)
	{
		try
		{
			$sql = "SELECT `version` FROM {$config['prefix']}config LIMIT 1;";
			$st = $db->prepare($sql);
			$st->execute();
			$version = $st->fetchAll();
			
			if($version)
			{
				$version = $version[0]['version'];
				
				if($version == VERSION)
				{
					$_SESSION['x7chat_install']['error'] = "X7 Chat is already installed and up to date.";
				}
				elseif($version > VERSION)
				{
					$_SESSION['x7chat_install']['error'] = "A newer version of X7 Chat is already installed.";
				}
				
				$_SESSION['x7chat_install']['type'] = 'upgrade';
				$_SESSION['x7chat_install']['prev_version'] = $version;
			}
		}
		catch(Exception $ex)
		{
		}
	}
	
	// Perform initial system checks
	if(empty($_SESSION['x7chat_install']['error']) && $_SESSION['x7chat_install']['step'] === 0)
	{
		$checks = array();
		
		$checks[] = array(
			'title' => 'PHP Version',
			'server' => phpversion(),
			'required' => '5.3.0',
			'result' => version_compare(phpversion(), '5.3.0', '>=') ? 'OK' : 'FAIL',
			'fix' => 'Upgrade PHP',
		);
		
		$checks[] = array(
			'title' => 'Magic Quotes GPC',
			'server' => (bool)get_magic_quotes_gpc(),
			'required' => false,
			'result' => !get_magic_quotes_gpc() ? 'OK' : 'WARN',
			'fix' => 'Set magic_quotes_gpc to off',
		);
		
		$checks[] = array(
			'title' => 'Magic Quotes Runtime',
			'server' => (bool)get_magic_quotes_runtime(),
			'required' => false,
			'result' => !get_magic_quotes_runtime() ? 'OK' : 'FAIL',
			'fix' => 'Set magic_quotes_runtime to off',
		);
		
		$checks[] = array(
			'title' => 'Magic Quotes Sybase',
			'server' => (bool)ini_get('magic_quotes_sybase'),
			'required' => false,
			'result' => !ini_get('magic_quotes_sybase') ? 'OK' : 'FAIL',
			'fix' => 'Set magic_quotes_sybase to off',
		);
		
		$checks[] = array(
			'title' => 'File Uploads',
			'server' => (bool)ini_get('file_uploads'),
			'required' => true,
			'result' => ini_get('file_uploads') ? 'OK' : 'WARN',
			'fix' => 'Set file_uploads to on',
		);
		
		$checks[] = array(
			'title' => 'config.php is writable',
			'server' => (bool)is_writable('../config.php'),
			'required' => true,
			'result' => is_writable('../config.php') ? 'OK' : 'WARN',
			'fix' => 'Make config.php writable or create it manually',
		);
		
		$checks_pass = true;
		foreach($checks as $check)
		{
			if($check['result'] == 'FAIL')
			{
				$checks_pass = false;
				break;
			}
		}
	}
	
	// Transition to the intial step
	if(isset($_GET['start']) && $_SESSION['x7chat_install']['step'] === 0 && $checks_pass)
	{
		if($_SESSION['x7chat_install']['type'] == 'upgrade')
		{
			$_SESSION['x7chat_install']['step'] = 4;
		}
		else
		{
			if($db)
			{
				$_SESSION['x7chat_install']['step'] = 2;
			}
			else
			{
				$_SESSION['x7chat_install']['step'] = 1;
			}
		}
	}
	
	// Attempt a save of the database connection details
	$error_message = false;
	if($_SESSION['x7chat_install']['type'] == 'install' && 
		(!empty($_POST) && $_SESSION['x7chat_install']['step'] === 1 
		|| ($_SESSION['x7chat_install']['step'] === 2 && !$db && !empty($_SESSION['x7chat_install']['config_contents']))))
	{
		try
		{
			if($_SESSION['x7chat_install']['step'] === 1)
			{
				$check_db = db_connection($_POST);
				
				$_SESSION['x7chat_install']['config_contents'] = $contents = '<?php return ' . var_export(array(
					'user' => $_POST['user'],
					'pass' => $_POST['pass'],
					'dbname' => $_POST['dbname'],
					'host' => $_POST['host'],
					'prefix' => $_POST['prefix'],
					'cost' => 10, // The cost for algorithmic cost. The more the cost, the more secure the password is, but the more resource it uses. The less the cost, the less secure the password will be and the less resource it uses.
				), 1) . ';';
			}
			else
			{
				$contents = $_SESSION['x7chat_install']['config_contents'];
			}
			
			if(is_writable('../config.php'))
			{
				$written = file_put_contents('../config.php', $contents);
				if($written)
				{
					$config = require('../config.php');
					$db = db_connection($config);
				}
			} 
			else
			{
				$written = false;
			}
			
			$_SESSION['x7chat_install']['step'] = 2;
		}
		catch(Exception $ex)
		{
			$error_message = "The connection to the database failed: {$ex->getMessage()}";
		}
	}
	
	// Check configuration file for valid values & setup database tables
	if($_SESSION['x7chat_install']['step'] === 2 && $_SESSION['x7chat_install']['type'] == 'install')
	{
		if($db)
		{
			unset($_SESSION['x7chat_install']['config_contents']);
			
			try
			{
				run_sql($db, 'new', $config['prefix']);
				
				$_SESSION['x7chat_install']['step'] = 3;
				$_POST = array();
			}
			catch(Exception $ex)
			{
				$_SESSION['x7chat_install']['error'] = "An error occurred while setting up the database: {$ex->getMessage()}";
			}
		}
		else
		{
			$contents = $_SESSION['x7chat_install']['config_contents'];
		}
	}
	
	if(!empty($_POST) && $_SESSION['x7chat_install']['step'] === 3 && $_SESSION['x7chat_install']['type'] == 'install')
	{
		$error_message = '';
	
		if(empty($_POST['admin_username']))
		{
			$error_message = "Please enter a username.";
		}
		
		if(empty($_POST['admin_password']))
		{
			$error_message = "Please enter a password.";
		}
		elseif($_POST['admin_password'] != $_POST['retype_admin_password'])
		{
			$error_message = "The passwords you entered do not match.";
		}
		
		if(!filter_var($_POST['admin_email'], FILTER_VALIDATE_EMAIL))
		{
			$error_message = "The email address you entered is not valid.";
		}
		
		if(!$error_message)
		{

			try
			{
				$sql = "INSERT INTO {$config['prefix']}users (`id`, `username`, `password`, `email`, `group_id`) VALUES (1, :username, :password, :email, 1);";
				$st = $db->prepare($sql);
				$st->execute(array(
					':username' => $_POST['admin_username'],
					':password' => password_hash($_POST['admin_password'], PASSWORD_BCRYPT, array('cost' => 10)),
					':email' => $_POST['admin_email'],
				));
				
				$sql = "UPDATE {$config['prefix']}config SET from_address = :from";
				$st = $db->prepare($sql);
				$st->execute(array(
					':from' => $_POST['admin_email'],
				));
				
				$_SESSION['x7chat_install']['step'] = 4;
			}
			catch(Exception $ex)
			{
				$_SESSION['x7chat_install']['error'] = "An error occurred while setting up your admin account: {$ex->getMessage()}";
			}
		}
	}
	
	if($_SESSION['x7chat_install']['step'] === 4)
	{
		$old_version = (int)$_SESSION['x7chat_install']['prev_version'];
		$apply_patches = array();
		$patches = scandir('./sql');
		foreach($patches as $patch)
		{
			if(preg_match('#^3[0-9]{7}$#', $patch) && (int)$patch > $old_version)
			{
				$apply_patches[] = $patch;
			}
		}
		
		sort($apply_patches);
		foreach($apply_patches as $patch)
		{
			try
			{
				run_sql($db, $patch, $config['prefix']);
			}
			catch(Exception $ex)
			{
				$_SESSION['x7chat_install']['error'] = "An error occurred while patching your database: {$ex->getMessage()}";
			}
		}
		
		if(empty($_SESSION['x7chat_install']['error']))
		{
			try
			{
				$sql = "UPDATE {$config['prefix']}config SET version = :version";
				$st = $db->prepare($sql);
				$st->execute(array(
					':version' => VERSION,
				));
				
				$_SESSION['x7chat_install']['step'] = 5;
			}
			catch(Exception $ex)
			{
				$_SESSION['x7chat_install']['error'] = "An error occurred while updating your database: {$ex->getMessage()}";
			}
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Install X7 Chat 3.3</title>
		<link rel="stylesheet" type="text/css" href="css/install.css" />
		<script type="text/javascript" src="scripts/jquery.js"></script>
		<script type="text/javascript" src="scripts/install.js"></script>
	</head>
	<body>
		<div id="page_wrapper">
			<div id="page_header">
				<div id="header_inner">
					<div id="page_logo">Install X7 Chat 3.3</div>
					<div id="header_menu">
						<ul>
							<li><a href="index.php?restart=1">Reset Installer</a></li>
						</ul>
					</div>
					<div style="clear: both;"></div>
				</div>
			</div>
			<div id="page_content">
				<div id="page_content_inner">
					<div class="inner_page">
						<div id="upper_content">
							<?php if($_SESSION['x7chat_install']['error']): ?>
								<p><?php echo $_SESSION['x7chat_install']['error']; ?></p>
							<?php elseif($_SESSION['x7chat_install']['step'] === 0): ?>
						
								<h1>Welcome to X7 Chat</h1>
								<p>X7 Chat will now check several configuration properties on your server to confirm that the server is compatible with X7 Chat.</p>
						
								<table cellspacing="0" cellpadding="0">
									<thead>
										<tr>
											<th>Property</th>
											<th>Current<br />Value</th>
											<th>Required<br />Value</th>
											<th>Result</th>
											<th>Solution</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($checks as $check): ?>
											<tr>
												<th><?php echo $check['title']; ?></th>
												<td><?php echo var_export($check['server'], 1); ?></td>
												<td><?php echo var_export($check['required'], 1); ?></td>
												<td class="<?php echo strtolower($check['result']); ?>"><?php echo $check['result']; ?></td>
												<td>
													<?php if($check['result'] != 'OK'): ?>
														<?php echo $check['fix']; ?>
													<?php else: ?>
														<i>None</i>
													<?php endif; ?>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							
								<?php if($checks_pass): ?>
									<p class='ok'>All checks passed successfully.</p>
									<?php if($_SESSION['x7chat_install']['type'] == 'install'): ?>
										<p>X7 Chat has not detected an existing installation and will proceed with a new installation.</p>
									<?php else: ?>
										<p>X7 Chat has detected a previous installation and will proceed with upgrading the existing installation.</p>
										<p>You should create a backup of your chatroom database before upgrading.  This installer will not create a backup for you.  This installer cannot repair, restore or revert your database if the upgrade fails.</p>
									<?php endif; ?>
									<a href="index.php?start=1">Click here to begin</a>
								<?php else: ?>
									<p class='fail'>Your current server configuration is not compatible with X7 Chat.  Please review the items in the table with a 'FAIL' result and correct them before installing X7 Chat.</p>
								<?php endif; ?>
								
							<?php elseif($_SESSION['x7chat_install']['step'] === 1): ?>
								
								<h1>Database Connection Details</h1>
								
								<?php if(!empty($error_message)): ?>
									<p class='fail'><?php echo $error_message; ?></a>
								<?php endif; ?>
								
								<p>Please enter the connection details for your database.</p>
								
								<form action="index.php" method="POST">
									<b><label for="host">Database Host</label></b>
									<input type="text" name="host" value="<?php echo isset($_POST['host']) ? sf($_POST['host']) : 'localhost'; ?>" />
									<hr />
									<b><label for="user">Database Username</label></b>
									<input type="text" name="user" value="<?php echo isset($_POST['user']) ? sf($_POST['user']) : ''; ?>" />
									<hr />
									<b><label for="pass">Database Password</label></b>
									<input type="password" name="pass" value="<?php echo isset($_POST['pass']) ? sf($_POST['pass']) : ''; ?>" />
									<hr />
									<b><label for="dbname">Database Name</label></b>
									<input type="text" name="dbname" value="<?php echo isset($_POST['dbname']) ? sf($_POST['dbname']) : ''; ?>" />
									<hr />
									<b><label for="prefix">Table Prefix</label></b>
									<input type="text" name="prefix" value="<?php echo isset($_POST['prefix']) ? sf($_POST['prefix']) : 'x7chat_'; ?>" />
									<hr />
									<input type="submit" value="Continue" />
								</form>
								
							<?php elseif($_SESSION['x7chat_install']['step'] === 2): ?>
							
								<h1>Configuration File Setup</h1>
								<p>The configuration file, config.php, could not be created automatically.  Please modify this file and add the following content to it:</p>
								<pre><?php echo sf($contents); ?></pre>
								<a href="index.php">Continue</a>
								
							<?php elseif($_SESSION['x7chat_install']['step'] === 3): ?>
								
								<h1>Administrator Account Details</h1>
								
								<?php if(!empty($error_message)): ?>
									<p class='fail'><?php echo $error_message; ?></a>
								<?php endif; ?>
								
								<p>Please enter details for your administrator account.</p>
								
								<form action="index.php" method="POST">
									<b><label for="admin_username">Admin Username</label></b>
									<input type="text" name="admin_username" value="<?php echo isset($_POST['admin_username']) ? sf($_POST['admin_username']) : 'admin'; ?>" />
									<hr />
									<b><label for="admin_username">Admin Password</label></b>
									<input type="password" name="admin_password" value="<?php echo isset($_POST['admin_password']) ? sf($_POST['admin_password']) : ''; ?>" />
									<hr />
									<b><label for="retype_admin_password">Retype Admin Password</label></b>
									<input type="password" name="retype_admin_password" value="<?php echo isset($_POST['retype_admin_password']) ? sf($_POST['retype_admin_password']) : ''; ?>" />
									<hr />
									<b><label for="admin_email">Admin E-Mail</label></b>
									<input type="text" name="admin_email" value="<?php echo isset($_POST['admin_email']) ? sf($_POST['admin_email']) : ''; ?>" />
									<hr />
									<input type="submit" value="Continue" />
								</form>
								
							<!--
							<?php elseif($_SESSION['x7chat_install']['step'] === 4): ?>
								
								<h1>Initial Chatroom Configuration</h1>
								
								<form action="index.php" method="POST">
									<b><label for="title">Chatroom Name</label></b>
									<input type="text" name="title" value="" />
									<hr />
									<input id="continue" type="submit" value="Continue" />
								</form>
							-->
							
							<?php elseif($_SESSION['x7chat_install']['step'] === 5): ?>

								<h1>Setup Complete</h1>
								
								<p>Setup is now complete.</p>
								<p><a href="../index.php">Visit your chatroom</a></p>
								
							<?php endif; ?>
							
						</div>
				</div>
			</div>
			<div id="page_footer">
				<div id="footer_inner">
					<a href="http://www.x7chat.com/" target="_blank">Powered By X7 Chat</a>
				</div>
			</div>
		</div>
	</body>
</html>