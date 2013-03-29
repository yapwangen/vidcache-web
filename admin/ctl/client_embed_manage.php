<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Client\Embed;

$client_id = get('client_id');
$client = Client::fetch($client_id);

if(post('edit')){
	try {
		Embed::update(post('handle'),post('vars'));
		alert('Embed object updated successfully',true,true);
		redirect(Url::client_embed_manage($client_id,post('handle')));
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

if(post('delete')){
	try {
		if(!post('confirm_delete'))
			throw new Exception('Deletion not confirmed');
		Embed::delete(post('handle'));
		alert('Embed object deleted successfully',true,true);
		redirect(Url::client_file_list($client_id));
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

//get embed vars
$embed_vars = Embed::fetchVars(get('handle'));
if(!is_array($embed_vars) || !count($embed_vars))
	throw new Exception('Embed object doesnt exist');

//setup params
$params = array();
$params['handle'] = get('handle');
$params = array_merge($params,post());
$params = array_merge($params,Client::adminHeaderParams($client_id,$client['company']));

//embed var edit
$params['vars'] = array();
foreach($embed_vars as $name => $value)
	$params['vars'][] = array('name'=>$name,'value'=>$value);

//form settings
$params['edit'] = true;
$params['form_action'] = 'edit';
$params['button_label'] = 'Update';
$params['url_form'] = Url::client_embed_manage($client_id,get('handle'));

Tpl::_get()->output('client_embed_form',$params);
