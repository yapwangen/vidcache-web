<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;

if(post('register')){
	try {
		if(Client::register(post()) !== false){
			alert("Registered successfully",true,true);
			redirect(Url::login());
		} else
			throw new \Exception('Registration failed!');
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

Tpl::_get()->setConstant('site_title','Register for an Account'.SITE_TITLE);
Tpl::_get()->parse('login','signup');
page_load_css_client();
output(Tpl::_get()->output());
