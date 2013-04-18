<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Staff\Session;

Session::requireManager();

if(post('create')){
	try {
		$params = post();
		unset($params['create'],$params['confirm_password']);
		Client::create($params);
		alert('Client account created successfully',true,true);
		redirect(Url::client());
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = Client::createParams();
$params = array_merge($params,post());
$params['create'] = true;
$params['form_action'] = 'create';
$params['button_label'] = 'Create';
$params['url_form'] = Url::client_create();

Tpl::_get()->setStub('client_actions',false)->output('client_form',$params);
