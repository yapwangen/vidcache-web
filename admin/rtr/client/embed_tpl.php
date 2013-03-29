<?php
use \LSS\Router;

Router::_get()->register('client',array(
	//embed tpl management
	 'embed_tpl_list'	=>	'/ctl/client_embed_tpl_list.php'
	,'embed_tpl_create'	=>	'/ctl/client_embed_tpl_create.php'
	,'embed_tpl_edit'	=>	'/ctl/client_embed_tpl_edit.php'
));