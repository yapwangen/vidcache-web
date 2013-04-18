<?php
use \LSS\Config;
use \LSS\Url;
use \Vidcache\Client\Session;

Session::tokenDestroy(Session::getTokenFromSession());
Session::destroySession();
//unset any cookie if there was one
setcookie(
	 Config::get('client','cookie_prefix').'_session'
	,null
	,time()-31536000
	,Config::get('client','cookie_path')
	,Config::get('client','cookie_domain')
);
//redirect
redirect(Url::login());
