<?php
use \LSS\Router;

Router::_get()->register('staff',array(
	 'create'		=>	'/ctl/staff_create.php'
	,'edit'			=>	'/ctl/staff_edit.php'
	,'profile'		=>	'/ctl/staff_profile.php'
	,'login'		=>	'/ctl/staff_login.php'
	,'logout'		=>	'/ctl/staff_logout.php'
	,Router::DEF	=>	'/ctl/staff_list.php'
));
