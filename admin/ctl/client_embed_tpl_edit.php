<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Client\EmbedTpl;

$client_id = get('client_id');
$client = Client::fetch($client_id);

if(post('edit')){
	try {
		EmbedTpl::update(post('client_embed_tpl_id'),array(
			'name'		=>	post('name')
			,'content'	=>	post('content')
		));
		alert('Embed template updated successfully',true,true);
		redirect(Url::client_embed_tpl_list($client_id));
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

if(post('delete')){
	try {
		if(!post('confirm_delete'))
			throw new Exception('Deletion not confirmed');
		EmbedTpl::delete(post('client_embed_tpl_id'));
		alert('Embed template deleted successfully',true,true);
		redirect(Url::client_embed_tpl_list($client_id));
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = EmbedTpl::fetch(get('client_embed_tpl_id'));
if($params === false)
	throw new \Exception('Client embed template could not be found');

$params = array_merge($params,post());
$params = array_merge($params,Client::adminHeaderParams($client_id,$client['company']));

//form settings
$params['edit'] = true;
$params['form_action'] = 'edit';
$params['button_label'] = 'Update';
$params['url_form'] = Url::client_embed_tpl_edit($client_id,get('client_embed_tpl_id'));

Tpl::_get()->output('client_embed_tpl_form',$params);
