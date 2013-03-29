<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Client\Contact;

if(post('create')){
	try {
		Contact::create(post('client_id'),post());
		alert('Client contact created successfully',true,true);
		redirect(Url::client_manage(post('client_id')));
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = Contact::createParams();
$params = array_merge($params,post());
$params['client_id'] = get('client_id');
$params = array_merge($params,Client::adminHeaderParams($params['client_id'],''));
$params['create'] = true;
$params['form_action'] = 'create';
$params['button_label'] = 'Create';
$params['url_client_manage'] = Url::client_manage(get('client_id'));
$params['url_client_contact_create'] = $params['url_form'] = Url::client_contact_create(get('client_id'));

Tpl::_get()->output('client_contact_form',$params);
