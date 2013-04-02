<?php

use \LSS\Config;
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Staff;
use \Vidcache\Admin\Staff\Session;

if(session_id() != ''){
	//check for session
	try {
		if(Session::checkLogin()){
			//register session
			$token = Session::fetchByToken(Session::getTokenFromSession());
			$session = array_merge(Staff::fetch($token['staff_id']),$token);
			Session::storeSession($session);
			unset($session,$token);
			//set tpl globals (if Tpl is available)
			if(is_callable(array('\LSS\Tpl','_get'))){
				Tpl::_get()->set(array(
					 'staff_name'		=>	Session::get('name')
					,'staff_lastlogin'	=>	date(Config::get('date','general_format'),Session::get('last_login'))
				));
			}
		} else {
			if(server('REQUEST_URI') != Url::login()) redirect(Url::login());
		}
	} catch(Exception $e){
		Session::tokenDestroy(Session::getTokenFromSession());
		Session::destroySession();
		if(server('REQUEST_URI') != Url::login()) redirect(Url::login());
	}
}
