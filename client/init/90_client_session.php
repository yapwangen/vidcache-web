<?php

use \LSS\Config;
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Client\Session;

if(session_id() != ''){
	//check for session
	try {
		if(Session::checkLogin()){
			//register session
			$token = Session::fetchByToken(Session::getTokenFromSession());
			$session = array_merge(Client::fetchByContact($token['contact_id']),$token);
			Session::storeSession($session);
			unset($session,$token);
			//set tpl globals (if Tpl is available)
			if(is_callable(array('\LSS\Tpl','_get'))){
				Tpl::_get()->set(array(
					 'client_name'		=>	Session::get('name')
					,'client_lastlogin'	=>	date(Config::get('date','general_format'),Session::get('last_login'))
				));
			}
		} else {
			if(server('REQUEST_URI') != Url::login()) redirect(Url::login());
		}
	} catch(Exception $e){
		echo "<pre>$e</pre>"; exit;
		Session::tokenDestroy(Session::getTokenFromSession());
		Session::destroySession();
		if(server('REQUEST_URI') != Url::login()) redirect(Url::login());
	}
}
