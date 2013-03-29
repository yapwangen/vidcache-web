<?php

Router::_get()->register(
	'client'
	,array(
		 'login'		=>	'/ctl/client/client_login.php'
		,'logout'		=>	'/ctl/client/client_logout.php'
		,'signup'		=>	'/ctl/client/client_signup.php'
		,Router::DEF	=>	'/ctl/client/client_profile.php'
	)
);
