<?php
use \LSS\Tpl;
use \LSS\Url;
use \LSS\Validate;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Client\Embed;
use \Vidcache\Admin\Client\EmbedTpl;
use \Vidcache\Admin\Client\File as ClientFile;
use \Vidcache\Admin\Client\Folder;
use \Vidcache\Admin\File;
use \Vidcache\Admin\Node;

$client_id = get('client_id');
$client_folder_id = get('client_folder_id') ? get('client_folder_id') : null;
$client = Client::fetch($client_id);

//---------------------------------------------------------
//Create Folder in this location
//---------------------------------------------------------
if(post('create_folder')){
	try {
		$data =	array(
			 'client_id'				=>	post('client_id')
			,'parent_client_folder_id'	=>	post('client_folder_id')
			,'name'						=>	post('name')
		);
		Folder::validate($data);
		$folder_id = Folder::create($data);
		if(!$folder_id)
			throw new Exception('Failed to create folder');
		alert('Folder created successfully',true,true);
		redirect(Url::client_file_list_by_folder($client_id,$client_folder_id));
	} catch(Exception $e){
		if($e->getCode() == 23000) alert('Folder with same name already exists',false);
		else alert($e->getMessage(),false);
	}
}

//---------------------------------------------------------
//Delete Files or Folders (Even recursively)
//---------------------------------------------------------
if(post('action') == 'delete'){
	try {
		if(!post('confirm_delete'))
			throw new Exception('Deletion not confirmed');
		if(post('folder') && !post('confirm_recursive_delete'))
			throw new Exception('Recursive deletion not confirmed and folder(s) selected');
		if(is_array(post('folder'))){
			foreach(post('folder') as $folder_id)
				Folder::delete($folder_id,true);
		}
		if(is_array(post('file'))){
			foreach(post('file') as $client_file_id)
				ClientFile::delete($client_file_id);
		}
		alert('Deletion successful',true,true);
		redirect(Url::client_file_list_by_folder($client_id,$client_folder_id));
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

//---------------------------------------------------------
//Upload a file from the web interface
//---------------------------------------------------------
if(post('upload_file')){
	try {
		if(!isset($_FILES['file']))
			throw new Exception('No file uploaded');
		if($_FILES['file']['error'] != UPLOAD_ERR_OK)
			throw new Exception('The file didnt upload successfully: '.$_FILES['file']['error']);
		ClientFile::upload(post('client_id'),post('client_folder_id'),$_FILES['file']['tmp_name'],$_FILES['file']['name']);
		//upload successfull, redirect
		alert('File uploaded successfully',true,true);
		redirect(Url::client_file_list_by_folder($client_id,$client_folder_id));
	} catch(Exception $e){
		//clean up tmp file on error
		if(isset($_FILES['file']['tmp_name']) && file_exists($_FILES['file']['tmp_name']))
			unlink($_FILES['file']['tmp_name']);
		//display error message
		alert($e->getMessage(),false);
	}
}

//---------------------------------------------------------
//Generate Preview Image for a Video
//---------------------------------------------------------
if(post('action') == 'gen_preview'){
	try {
		foreach(post('file') as $client_file_id)
			ClientFile::genPreviewFromClientFile($client_file_id);
		alert('Preview images generated successfully',true,true);
		redirect(Url::client_file_list_by_folder($client_id,$client_folder_id));
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

//---------------------------------------------------------
//Publish File (Automatically Create Embed Object
//---------------------------------------------------------
if(post('action') == 'publish'){
	try {
		if(!is_array(post('file')) || !count(post('file')))
			throw new Exception('No files selected');
		if(!post('client_embed_tpl_id'))
			throw new Exception('No embed template selected');
		Embed::publish($client_id,$client_folder_id,post('file'),post('client_embed_tpl_id'));
		alert('Files have been published',true,true);
		redirect(Url::client_file_list_by_folder($client_id,$client_folder_id));
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

//def arrays
$files = $folders = array();

//folders
foreach(Folder::fetchAllByParent($client_folder_id,$client_id) as $folder){
	$folder['url_edit'] = Url::client_folder_manage($client_id,$folder['client_folder_id']);
	$folder['url'] = Url::client_file_list_by_folder($client_id,$folder['client_folder_id']);
	$folders[] = $folder;
}

//files
foreach(ClientFile::fetchAllByFolder($client_id,$client_folder_id) as $file){
	$file['url'] = Url::client_file_manage($client_id,$file['client_file_id']);
	//list related embed objects
	$file['embed'] = array();
	foreach(Embed::fetchAllHandlesByFile($file['client_file_id']) as $embed){
		$embed['url'] = Url::client_embed_manage($client_id,$embed['handle']);
		$file['embed'][] = $embed;
	}
	$files[] = $file;
}

$params = array();
$params['path'] = Folder::path($client_id,$client_folder_id);
$params['folders'] = $folders;
$params['files'] = $files;
$params['client_id'] = $client_id;
$params['client_folder_id'] = $client_folder_id;
$params['embed_tpl_drop'] = EmbedTpl::drop($client_id);
$params['url_current'] = Url::client_file_list_by_folder($client_id,$client_folder_id);
$params = array_merge($params,Client::adminHeaderParams($client_id,$client['company']));

Tpl::_get()->output('client_file_list',$params);
