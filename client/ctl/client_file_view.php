<?php
use \LSS\Config;
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Client\FS;
use \Vidcache\Client\Session;
use \Vidcache\SDK;

//setup the SDK
$vc = SDK::load();
$vc->connect(Config::get('vidcache','api_key'));

//get args
$client_id = Session::get('client_id');
$handle = get('handle');

//check if we have a different signature cached for this client
FS::updateCache($vc);

$params = array();
$params['file'] = $file = FS::fetchFileByHandle($handle);
//merge in urls
$params['file'] = array_merge($params['file'],FS::URLsByFile($file));
//set the action type
$params['fire'] = get('fire');
Tpl::_get()->output('client_file_view',$params);
