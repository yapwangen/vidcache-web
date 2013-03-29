<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;

if(post('edit')){
	try {
		Client::update(post('client_id'),post());
		alert('Client updated successfully',true,true);
		redirect(Url::client_edit(post('client_id')));
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

if(post('delete')){
	try {
		Client::deactivate(post('client_id'));
		alert('Client deleted successfully',true,true);
		redirect(Url::client());
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = Client::fetch(get('client_id'));
$params = array_merge($params,post());
$params['edit'] = true;
$params['form_action'] = 'edit';
$params['button_label'] = 'Update';
$params['url_client_edit'] = $params['url_form'] = Url::client_edit(get('client_id'));
$params['url_client_manage'] = Url::client_manage(get('client_id'));
$params = array_merge($params,Client::adminHeaderParams($params['client_id'],$params['company']));

Tpl::_get()->output('client_form',$params);
