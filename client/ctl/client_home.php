<?php
use \LSS\Config;
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Client\Session;
use \Vidcache\SDK\VCFS;
use \Vidcache\SDK;

if(!Session::isLoggedIn())
	redirect(Url::login());

//setup SDK and VCFS
VCFS::register();
$vc = SDK::load();
$vc->connect(Config::get('vidcache','api_key'));

//setup path info
$root_path = '/home/'.Session::get('client_id');
$path = !is_null(get('path')) ? get('path') : '/';

if(get('upload')){
	foreach($_FILES['file']['tmp_name'] as $key => $tmp_name)
		move_uploaded_file($tmp_name,VCFS::getPrefix().$root_path.$path.'/'.$_FILES['file']['name'][$key]);
	alert('Files uploaded successfully',true,true);
	echo 'http://'.$_SERVER['HTTP_HOST'].Url::client_home_path($path);
	exit;
}

if(post('folder')){
	try {
		mkdir(VCFS::getPrefix().$root_path.$path.'/'.post('folder'));
		alert('Folder created successfully',true,true);
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
$built = '';
foreach(explode('/',ltrim($path,'/')) as $v){
	$built .= '/'.$v;
	$params['path'][] = array('url'=>Url::client_home_path($built),'name'=>$v);
	
}
unset($built);

$info = $vc->pathInfo($root_path.$path);
foreach($info['files'] as $file){
	$file['created'] = age($file['created']);
	$file['size'] = format_bytes($file['size']);
	$file['bandwidth'] = format_bytes($file['bandwidth']);
	$file['bandwidth_mtd'] = format_bytes($file['bandwidth_mtd']);
	$file['url'] = Url::client_home_path(str_replace($root_path,'',$file['path']));
	$params['files'][] = $file;
}
foreach($info['folders'] as $folder){
	$folder['url'] = Url::client_home_path(str_replace($root_path,'',$folder['path']));
	$folder['created'] = age($folder['created']);
	$params['folders'][] = array_merge(array('url'=>''),$folder);
}
Tpl::_get()->output('client_home',$params);
