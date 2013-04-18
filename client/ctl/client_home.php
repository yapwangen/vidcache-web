<?php
use \LSS\Config;
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Client\Session;
use \Vidcache\Client\FS;
use \Vidcache\SDK\VCFS;
use \Vidcache\SDK;

if(!Session::isLoggedIn())
	redirect(Url::login());

$client_id = Session::get('client_id');

//setup SDK and VCFS
VCFS::register();
$vc = SDK::load();
$vc->connect(Config::get('vidcache','api_key'));

//check if we have a different signature cached for this client
FS::updateCache($vc);

//setup path info
$root_path = '/home/'.$client_id;
$path = !is_null(get('path')) ? get('path') : '';

//make sure the root path exists
if($vc->pathExists($root_path) == 'none')
	$vc->folderCreate($root_path,true);

if(get('upload')){
	//make sure the root path exists
	if($vc->pathExists($root_path) == 'none')
		mkdir(VCFS::getPrefix().$root_path,true);
	//upload files
	foreach($_FILES['file']['tmp_name'] as $key => $tmp_name){
		$upload_path = $root_path.$path.'/'.$_FILES['file']['name'][$key];
		//upload the file
		move_uploaded_file($tmp_name,VCFS::getPrefix().$upload_path);
		//get info about the file
		$info = $vc->pathInfo($upload_path);
		$mime_type = $info['file']['mime_type'];
		//check our mimetype
		if(in_array($mime_type,Config::get('embed','types'))){
			$preview_path = null;
			//videos are a bit different and need a thumbnail first
			if(in_array($mime_type,Config::get('embed','video_types'))){
				//create the thumbnail
				try {
					$rv = $vc->pathGenPreview($upload_path);
					$preview_path = $rv['preview_path'];
				} catch(Exception $e){
					//ignore it
				}
			}
			//publish it
			$rv = $vc->pathPublish($upload_path,$preview_path,Config::get('vidcache','embed_tpl_handle'));
			FS::fileStoreEmbedHandle($rv['embed_handle'],$upload_path);
		}
	}
	//update our local cache
	//	we start a new instance here to workaround an xport bug with encoding types
	//	reported here: https://github.com/openlss/lib-xport/issues/1
	$vc = SDK::load();
	$vc->connect(Config::get('vidcache','api_key'));
	FS::updateCache($vc);
	//done
	alert('Files uploaded successfully',true,true);
	echo 'http://'.$_SERVER['HTTP_HOST'].Url::client_home_path($path);
	exit;
}

//create folder
if(post('create_folder')){
	try {
		mkdir(VCFS::getPrefix().$root_path.$path.'/'.post('folder'));
		alert('Folder created successfully',true,true);
		redirect(Url::client_home_path($path));
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

//delete files / folders
if(post('action') == 'delete'){
	try {
		$files = post('file');
		$folders = post('folder');
		if(!is_null($files) && is_array($files) && is_null(post('confirm_file_deletion')))
			throw new Exception('Files selected for deletion but not confirmed');
		if(!is_null($folders) && is_array($folders) && is_null(post('confirm_folder_deletion')))
			throw new Exception('Folders selected for deletion but not confirmed');
		//delete files
		if(is_array($files)){
			foreach($files as $file_id){
				$file = FS::fetchFileById($file_id);
				$delete_path = $file['path'];
				$vc->pathDelete($delete_path);
			}
		}
		//delete folders
		if(is_array($folders)){
			foreach($folders as $folder_id){
				$folder = FS::fetchFolderById($folder_id);
				$delete_path = $folder['path'];
				$vc->pathDelete($delete_path,true);
			}
		}
		//update the cache
		FS::updateCache($vc);
		alert('File(s) and/or Folder(s) deleted successfully!',true,true);
		redirect(Url::client_home_path($path));
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = array();
$params['url_action'] = Url::client_home_path($path);
$params['url_action_upload'] = $params['url_action'].'&upload=true';
$params['files'] = $params['folders'] = array();

$params['path'] = array();
$params['path'][] = array('url'=>Url::client_home(),'name'=>'Root');
$built = '';
foreach(explode('/',ltrim($path,'/')) as $v){
	if(empty($v)) continue;
	$built .= '/'.$v;
	$params['path'][] = array('url'=>Url::client_home_path($built),'name'=>$v);
	
}
unset($built);

$info['files'] = FS::fetchFilesByParent($root_path.$path);
$info['folders'] = FS::fetchFoldersByParent($root_path.$path);
foreach($info['files'] as $file){
	$file['created'] = age($file['created']);
	$file['size'] = format_bytes($file['size']);
	$file['bandwidth'] = format_bytes($file['bandwidth']);
	$file['bandwidth_mtd'] = format_bytes($file['bandwidth_mtd']);
	$file['url'] = Url::client_file_view(FS::actionType($file['mime_type']),$file['handle']);
	$params['files'][] = $file;
}
foreach($info['folders'] as $folder){
	$folder['url'] = Url::client_home_path(str_replace($root_path,'',$folder['path']));
	$folder['created'] = age($folder['created']);
	$params['folders'][] = array_merge(array('url'=>''),$folder);
}
Tpl::_get()->output('client_home',$params);
