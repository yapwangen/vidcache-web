<?php
use \LSS\Router;

Router::_get()->register('client',array(
	//contact management
	 'contact_edit'		=>	'/ctl/client_contact_edit.php'
	,'contact_create'	=>	'/ctl/client_contact_create.php'
));