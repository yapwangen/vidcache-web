<?php
use \LSS\Router;

Router::_get()->register('client',array(
	 'create'			=>	'/ctl/client_create.php'
	,'manage'			=>	'/ctl/client_manage.php'
	,'edit'				=>	'/ctl/client_edit.php'
	,Router::DEF		=>	'/ctl/client_list.php'
));