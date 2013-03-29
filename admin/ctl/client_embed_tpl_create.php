<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Client\EmbedTpl;

$client_id = get('client_id');
$client = Client::fetch($client_id);

if(post('create')){
	try {
		EmbedTpl::create(array(
			'client_id'	=>	$client_id
			,'name'		=>	post('name')
			,'content'	=>	post('content')
		));
		alert('Embed template added successfully',true,true);
		redirect(Url::client_embed_tpl_list($client_id));
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = EmbedTpl::createParams();
$params = array_merge($params,post());
$params = array_merge($params,Client::adminHeaderParams($client_id,$client['company']));

//form settings
$params['create'] = true;
$params['form_action'] = 'create';
$params['button_label'] = 'Create';
$params['url_form'] = Url::client_embed_tpl_create($client_id);

Tpl::_get()->output('client_embed_tpl_form',$params);
