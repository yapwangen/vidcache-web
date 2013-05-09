<?php
use \LSS\Config;
use \LSS\DataTables;
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Client\Session;
use \Vidcache\Client\FS;
use \Vidcache\SDK\VCFS;
use \Vidcache\SDK;
use \Vidcache\Client\DataModel\File;
use \Vidcache\Client\DataModel\Folder;

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

//set the page url
$url = Url::client_home_path($path);

//make sure the root path exists
if($vc->pathExists($root_path) == 'none')
	$vc->folderCreate($root_path,true);

if(get('upload')){
	//make sure the root path exists
	if($vc->pathExists($root_path) == 'none')
		mkdir(VCFS::getPrefix().$root_path,true);
	//upload files
	$tmp_name = $_FILES['file']['tmp_name'];
	$upload_path = $root_path.$path.'/'.$_FILES['file']['name'];
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
	//update our local cache
	//	we start a new instance here to workaround an xport bug with encoding types
	//	reported here: https://github.com/openlss/lib-xport/issues/1
	$vc = SDK::load();
	$vc->connect(Config::get('vidcache','api_key'));
	FS::updateCache($vc);
	//done
	// alert('Files uploaded successfully',true,true);
	echo 'success';
	// echo 'http://'.$_SERVER['HTTP_HOST'].Url::client_home_path($path);
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

if(post('action') == 'move'){
	try {
		$dest = post('destination_path');
		$vc->pathMove(post('file'),post('folder'),$root_path.$dest);
		alert('File(s) and/or Folder(s) moved successfully',true,true);
		redirect(Url::client_home_path($dest));
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

if(post('update_hidden')){
	$_SESSION['show_hidden_objects'] = post('show_hidden') ? true : false;
}

//data table loading
if(get('datatables') == 'true'){
	$files = DataTables::_get()
		->setColumns(array('checkbox','icon','name','mime_type','size','hits_lifetime','bytes_this_month','created','actions'))
		->setDataCallback('\Vidcache\Client\FS::fetchFilesByParentDatatables',$root_path.$path)
		->setDataModel('\Vidcache\Client\DataModel\File')
		->setupFromRequest()
		->process()
		->getResult();
	$folders = DataTables::_get()
		->setColumns(array('checkbox','icon','name','type','size','hits','transfer','created','actions'))
		->setDataCallback('\Vidcache\Client\FS::fetchFoldersByParentDatatables',$root_path.$path)
		->setDataModel('\Vidcache\Client\DataModel\Folder')
		->setupFromRequest()
		->process()
		->getResult();
	//build merged result
	$result = $files;
	$result['iTotalRecords'] += $folders['iTotalRecords'];
	$result['iTotalDisplayRecords'] += $folders['iTotalDisplayRecords'];
	$result['aaData'] = array_merge($folders['aaData'],$result['aaData']);
	echo json_encode($result);
	exit;
}

$show_hidden = session('show_hidden_objects');

$params = array();

//setup basic params
$params['is_show_hidden'] = $show_hidden;
$params['url_action'] = $url;
$params['url_action_upload'] = $params['url_action'].'&upload=true';
$params['files'] = $params['folders'] = array();

//build the breadcrumb path
$params['path'] = array();
$params['path'][] = array('url'=>Url::client_home(),'name'=>'Root');
$built = '';
foreach(explode('/',ltrim($path,'/')) as $v){
	if(empty($v)) continue;
	$built .= '/'.$v;
	$params['path'][] = array('url'=>Url::client_home_path($built),'name'=>$v);
	
}
unset($built);

//output the template
Tpl::_get()->output('client_home',$params);
