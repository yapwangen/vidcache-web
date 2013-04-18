<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Client\FS;
use \Vidcache\Client\Session;

$client_id = Session::get('client_id');

$params = array();
$params['file'] = FS::fetchFileByHandle(get('handle'));
//merge in urls
$params['file'] = array_merge($params['file'],FS::URLsByHandle(get('handle'));
//set the action type
$params['fire'] = get('fire');
Tpl::_get()->output('client_file_view',$params);
