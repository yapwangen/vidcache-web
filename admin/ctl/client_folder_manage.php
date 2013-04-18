<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Client\Folder;

$client_id = get('client_id');
$client_folder_id = get('client_folder_id');
$client = Client::fetch($client_id);

if(post('edit')){
	try {
		Folder::update(post('client_folder_id'),array(
			 'name'				=>	post('name')
		));
		alert('Folder successfully update',true,true);
		redirect(Url::client_folder_manage($client_id,post('client_folder_id')));
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = Folder::fetch(get('client_folder_id'));
$params = array_merge($params,post(),Client::adminHeaderParams($client_id,$client['company']));
$params['url_client_folder_manage'] = Url::client_folder_manage($client_id,$client_folder_id);

//path
$params['path'] = Folder::path($client_id,$client_folder_id);

//form settings
$params['edit'] = true;
$params['form_action'] = 'edit';
$params['button_label'] = 'Update';
$params['url_form'] = Url::client_folder_manage($client_id,$client_folder_id);

Tpl::_get()->output('client_folder_form',$params);
