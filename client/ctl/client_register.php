<?php
use \LSS\Tpl;
use \LSS\Url;
use \LSS\Validate;
use \Vidcache\Admin\Client;
use \Vidcache\Client\Session;

//bounce to homepage when logged in
if(Session::isLoggedIn())
	redirect(Url::home());

if(post('register')){
	try {
		Validate::prime(post());
		Validate::go('email')->not('blank')->is('email');
		Validate::go('password')->not('blank')->min(6)->max(128);
		Validate::paint();
		if(post('password') != post('confirm_password'))
			throw new Exception('Passwords do not match!');
		$client_id = Client::create(array_merge(Client::createParams(),array(
			 'email'		=>	post('email')
			,'password'		=>	post('password')
		)));
		alert('Registration completed successfully!',true,true);
		redirect(Url::login());
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = array();
$params['email'] = post('email');
Tpl::_get()->output('client_register',$params);
