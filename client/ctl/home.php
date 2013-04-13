<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Client\Session;

//bounce to user home when logged in
if(Session::isLoggedIn())
	redirect(Url::client_home());

$params = array();
Tpl::_get()->output('home',$params);
