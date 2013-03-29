<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Session as ClientSession;

if(post('login')){
	try {
		//get the client
		$client = Client::fetchByEmail(post('email'),false);
		if($client === false) throw new \Exception('Client doesnt exist');
		//check active flags
		if(!mda_get($client,'contact_is_active'))
			throw new \Exception('Contact is disabled');
		if(mda_get($client,'__is_client') && (!mda_get($client,'is_active')))
			throw new \Exception('Client is disabled');
		//check password(s)
		Client::auth(post('password'),$client);
		if(mda_get($client,'__auth') === 0)
			throw new \Exception('Password is invalid');
		//generate token and setup session
		$token = ClientSession::tokenCreate(mda_get($client,'contact_id'),server('REMOTE_ADDR'),server('HTTP_USER_AGENT'));
		ClientSession::startSession($token);
		ClientSession::storeSession($client);
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
Tpl::_get()->parse('login','page');
page_load_css_client();
output(Tpl::_get()->output());
