*******************************************************************************
*                __  _______    ____ _           _     _____                  *
*                \ \/ /___  |  / ___| |__   __ _| |_  |___ /                  *
*                 \  /   / /  | |   | '_ \ / _` | __|   |_ \                  *
*                 /  \  / /   | |___| | | | (_| | |_   ___) |                 *
*                /_/\_\/_/     \____|_| |_|\__,_|\__| |____/                  *
*                                                                             *
*******************************************************************************
*                                                                             *
*                Version:           3.3.0a2                                   *
*                Release Date:      June 22, 2018                             *
*                Author:            Tim Chamness                              *
*                Copyright:         2003-2013 by Tim Chamness                 *
*                Website:           http://www.x7chat.com/                    *
*                                                                             *
*******************************************************************************


***  This file is formatted for a monospace font with wordwrap turned off.  ***


*******************************************************************************
*                               DISCLAIMER                                    *
*******************************************************************************

I (spaceshiptrooper) am not the author or original owner of this software. I
also DO NOT claim credit or ownership for what Tim has done and will claim no
credit for the additional modifications I have done.

I decided to update it and release my copy so that everyone can enjoy a more
up-to-date version.



*******************************************************************************
*                               CONTENTS                                      *
*******************************************************************************

1) Alpha Warning
2) License
3) System Requirements
4) Installation
5) Bug Reports
6) Technical Support



*******************************************************************************
*                             ALPHA WARNING                                   *
*******************************************************************************

This is an alpha release:

	1) Not all features have been implemented.
	2) Official technical support is not available.
	3) Upgrading from older versions of X7 Chat is not supported yet.
	4) Integration with external systems is not supported yet.
	5) The code design has not been finalized, so making extensive
	   customizations to the code, theme or language is not recommended.



*******************************************************************************
*                                LICENSE                                      *
*******************************************************************************

X7 Chat - a PHP chatroom
Copyright (C) 2013  Tim Chamness

This program is free software: you can redistribute it and/or modify it under 
the terms of the GNU General Public License as published by the Free Software 
Foundation, either version 3 of the License, or (at your option) any later 
version.

This program is distributed in the hope that it will be useful, but WITHOUT 
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

Please see LICENSE.txt for the entire license.



*******************************************************************************
*                          SYSTEM REQUIREMENTS                                *
*******************************************************************************

Server Requirements:
	PHP 5.3+
	MySQL 5.1+
	
Client Requirements:
	Supported Browsers:
		Firefox (latest version)
		Chrome (latest version)
		Safari 5+
		IE 9+
	JavaScript enabled
	Cookies enabled

	
	
*******************************************************************************
*                              INSTALLATION                                   *
*******************************************************************************

Installing a new copy of the software:
	1. Upload all of the X7 Chat files to the server.
	2. Create a MySQL database and user account.
	3. Make config.php writable by the web server (optional).
	4. Visit /index.php in your web browser and follow the instructions.
	
Upgrading from 3.3.X version of the software:
	1. Upload the new files over top of the old ones EXCEPT for config.php, 
	   overwriting all files.
	2. Visit /install/index.php in your web browser and follow the instructions.
	
Upgrading from a 2.X or 1.X version of the software:
	Upgrading from X7 Chat 1.X and 2.X is not supported in this release; however,
	future releases of X7 Chat 3 will support upgrading from	X7 Chat 1 and 2.
	
Integrating with external applications:
	Integration is not supported in this release; however, future releases of
	X7 Chat 3 will support integrating with many external applications.



*******************************************************************************
*                              CONFIGURATION FILE                             *
*******************************************************************************

The comments made below could not be dumped into the configuration file so it
will be stated here.

<?php return array (
  'user' => '',
  'pass' => '',
  'dbname' => '',
  'host' => '',
  'prefix' => 'x7chat_',

  // The cost for algorithmic cost. The more the cost, the more
  // secure the password is, but the more resource it uses. The less the cost, the
  // less secure the password will be and the less resource it uses.
  'cost' => 10,
);


*******************************************************************************
*                              BUG REPORTS                                    *
*******************************************************************************

Please report bugs on the forums: http://www.x7chat.com/forums/



*******************************************************************************
*                           TECHNICAL SUPPORT                                 *
*******************************************************************************

Technical support is not available at this time.  Please post on the forums if
you run into issues: http://www.x7chat.com/forums/


