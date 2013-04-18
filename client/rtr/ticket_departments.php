<?php
use \LSS\Router;

Router::_get()->register('ticket_department',array(
	 'list'		=>	'/ctl/ticket_department_list.php'
	,'edit'		=>	'/ctl/ticket_department_edit.php'
	,'create'		=>	'/ctl/ticket_department_create.php'
	,Router::DEF		=>	'/ctl/ticket_department_list.php'
));
