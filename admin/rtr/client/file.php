<?php
use \LSS\Router;

Router::_get()->register('client',array(
	//file management
	 'file_list'		=>	'/ctl/client_file_list.php'
	,'file_manage'		=>	'/ctl/client_file_manage.php'
	,'folder_manage'	=>	'/ctl/client_folder_manage.php'
));