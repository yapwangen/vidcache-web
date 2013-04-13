<?php
use \LSS\Router;

Router::_get()->register('client',array(
	 'manage'			=>	'/ctl/client_manage.php'
	,'edit'				=>	'/ctl/client_edit.php'
	,'profile'			=>	'/ctl/client_profile.php'
	,'login'			=>	'/ctl/client_login.php'
	,'logout'			=>	'/ctl/client_logout.php'
	,'register'			=>	'/ctl/client_register.php'
	,'home'				=>	'/ctl/client_home.php'
	,Router::DEF		=>	'/ctl/client_home.php'
));