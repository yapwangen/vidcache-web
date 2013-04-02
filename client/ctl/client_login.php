<?php
use \LSS\Config;
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Client\Session;

if(post('login')){
	try {
		//get the client member
		$client = Client::fetchByEmail(post('email'));
		if(!$client) throw new Exception('Client doesnt exist');
		//check password
		if(!bcrypt_check(post('password'),$client['password']))
			throw new Exception('Password is invalid');
		//generate token and setup session
		$token = Session::tokenCreate($client['client_id'],server('REMOTE_ADDR'),server('HTTP_USER_AGENT'));
		Session::startSession($token);
		//update last login
		Client::updateLastLogin($client);
		//redirect request
		if(session('login_referrer') && strpos(session('login_referrer'),Url::login()) === false)
			redirect(session('login_referrer'));
		else redirect(Url::home());
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

session('login_referrer',server('HTTP_REFERER'));

$params = array();
$params['url_login'] = Url::login();
$params['page_title'] = Config::get('site_name').' - Client Login';
Tpl::_get()->output('client_login',$params);
