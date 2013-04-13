<?php
namespace Vidcache\Client;

use \Exception;
use \LSS\Config;
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Client\Session;

abstract class Session extends \LSS\Session {
	public static function init(){
		self::$config_name		= 'client';
		self::$session_name		= 'client_token';
		self::$session_table	= 'client_session';
		self::$user_primary_key	= 'contact_id';
		self::$urls_nologin		= array(Url::login());
	}
	
	public static function requireLogin(){
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
							,'client_loggedin'	=>	true
							,'client_free'		=>	true
							,'client_premium'	=>	false
							,'client_lastlogin'	=>	date(Config::get('account','date.general_format'),Session::get('last_login'))
						));
					}
				} else {
					if(server('REQUEST_URI') != Url::login()) redirect(Url::login());
				}
			} catch(Exception $e){
				alert($e->getMessage(),false,true);
				Session::tokenDestroy(Session::getTokenFromSession());
				Session::destroySession();
				if(server('REQUEST_URI') != Url::login()) redirect(Url::login());
			}
		}
	}

}

//overrides the parent vars
Session::init();
