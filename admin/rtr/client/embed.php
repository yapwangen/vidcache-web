<?php
use \LSS\Router;

Router::_get()->register('client',array(
	//embed management
	 'embed_list'		=>	'/ctl/client_embed_list.php'
	,'embed_create'		=>	'/ctl/client_embed_create.php'
	,'embed_manage'		=>	'/ctl/client_embed_manage.php'
));