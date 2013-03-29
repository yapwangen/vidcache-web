<?php
use \LSS\Router;

Router::_get()->register('client',array(
	//ticket management
	 'ticket_create'	=>	'/ctl/client_ticket_create.php'
	,'ticket_manage'	=>	'/ctl/client_ticket_manage.php'
	,'ticket_list'		=>	'/ctl/client_ticket_list.php'
));