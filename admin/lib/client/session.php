<?php
namespace Vidcache\Client;

abstract class Session extends \LSS\Session {
	public static function init(){
		self::$config_name		= 'client';
		self::$session_name		= 'client_token';
		self::$session_table	= 'client_session';
		self::$user_primary_key	= 'contact_id';
		self::$urls_nologin		= array(Url::login(),Url::signup());
	}
}

//overrides the parent vars
Session::init();
