<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Client\Contact;

if(req('edit')){
	try {
		Contact::update(req('contact_id'),req());
		alert('Client contact updated successfully',true,true);
		redirect(Url::client_manage(req('client_id')));
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

if(req('delete')){
	try {
		if(!req('confirm_delete')) throw new \Exception('Deactivation must be confirmed');
		Contact::deactivate(req('contact_id'));
		alert('Client contact deactivated successfully',true,true);
		redirect(Url::client_manage(req('client_id')));
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = Contact::fetch(req('contact_id'));
$params = array_merge($params,req());
$params['account_key'] = 'client_id';
$params['edit'] = true;
$params['form_action'] = 'edit';
$params['button_label'] = 'Update';
$params['url_client_manage'] = Url::client_manage(get('client_id'));
$params['url_client_contact_edit'] = $params['url_form'] = Url::client_contact_edit(req('client_id'),req('contact_id'));
$params = array_merge($params,Client::adminHeaderParams(get('client_id'),''));

Tpl::_get()->output('client_contact_form',$params);
