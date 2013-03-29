<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\File;

if(post('edit')){
	try {
		File::update(post('file_id'),post());
		alert('File updated successfully',true,true);
		redirect(Url::file());
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

if(post('delete')){
	try {
		File::deactivate(post('file_id'));
		alert('File deleted successfully',true,true);
		redirect(Url::file());
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = File::fetch(get('file_id'));
$params = array_merge($params,$_POST);
$params['is_cache'] = $params['is_cache'] ? 'checked="checked"' : '';
$params['url_file_manage'] = htmlentities(Url::file_manage(get('file_id')));

Tpl::_get()->output('file_edit',$params);
