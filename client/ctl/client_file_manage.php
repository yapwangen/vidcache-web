<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Client\Embed;
use \Vidcache\Admin\Client\EmbedTpl;
use \Vidcache\Admin\Client\File as ClientFile;
use \Vidcache\Admin\Client\Folder;
use \Vidcache\Admin\File;

$client_id = get('client_id');
$client_file_id = get('client_file_id');
$client = Client::fetch($client_id);

if(post('edit')){
	try {
		/*if(post('data_copies_req') < 1)
			throw new \Exception('There must be at least 1 data copy');*/
		$client_file = ClientFile::fetch(post('client_file_id'));
		ClientFile::update(post('client_file_id'),array(
			 'name'				=>	post('name')
		));
		/*File::update($client_file['client_file_id'],array(
			'data_copies_req'		=>	post('data_copies_req')
			,'cache_copies_req'		=>	post('cache_copies_req')
		));*/
		alert('File successfully updated',true,true);
		redirect(Url::client_file_manage($client_id,post('client_file_id')));
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = ClientFile::fetchFull(get('client_file_id'));
$params['size'] = format_bytes($params['size']);
$params = array_merge($params,post(),Client::adminHeaderParams($client_id,$client['company']));
$params['url_client_file_manage'] = $params['url_form'] = Url::client_file_manage($client_id,$client_file_id);

//urls
$params['urls'][] = array('name'=>'Download','url'=>File::urlDownload($params['chksum'],$client_id));
if(strpos($params['mime_type'],'video/') !== false || strpos($params['mime_type'],'audio/') !== false)
	$params['urls'][] = array('name'=>'Stream','url'=>File::urlStream($params['chksum'],$client_id));
if(strpos($params['mime_type'],'text/') !== false || strpos($params['mime_type'],'image/') !== false)
	$params['urls'][] = array('name'=>'Static','url'=>File::urlStatic($params['chksum']));

//embed objects
$embed = array();
foreach(Embed::fetchAllHandlesByFile($params['client_file_id']) as $row){
	$embed_vars = Embed::fetchVars($row['handle']);
	$embed_tpl = EmbedTpl::fetchByHandle($embed_vars['tpl']);
	$embed[] = array(
		'url_manage'	=>	Url::client_embed_manage($client_id,$row['handle'])
		,'handle'		=>	$row['handle']
		,'tpl_name'		=>	$embed_tpl['name']
		,'url'			=>	Embed::url($row['handle'])
	);
}
$params['embed'] = $embed; unset($embed);

//path
$params['path'] = Folder::path($client_id,$params['client_folder_id']);
$params['path'][] = array('url'=>Url::client_file_manage($client_id,$client_file_id),'name'=>$params['name']);

//form settings
$params['edit'] = true;
$params['form_action'] = 'edit';
$params['button_label'] = 'Update';

Tpl::_get()->output('client_file_manage',$params);
