<?php
use \LSS\Config;
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;

$clients = array();
foreach(Client::fetchAll() as $row){
	$params = $row;
	$params['created'] = ($row['created'] != 0) ? date(Config::get('date','general_format'),$row['created']) : 'N/A';
	$params['last_login'] = ($row['last_login'] != 0) ? date(Config::get('date','general_format'),$row['last_login']) : '(never)';
	$params['url_client_manage'] = Url::client_manage($row['client_id']);
	$clients[] = $params;
}

Tpl::_get()->output('client_list',array('clients'=>$clients));
