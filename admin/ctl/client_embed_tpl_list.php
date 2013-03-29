<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Client\EmbedTpl;

$client_id = get('client_id');
$client = Client::fetch($client_id);

$templates = array();
foreach(EmbedTpl::fetchAllByClient($client_id) as $row){
	unset($row['content']);
	$params = $row;
	$params['url_edit'] = Url::client_embed_tpl_edit($client_id,$row['client_embed_tpl_id']);
	$templates[] = $params;
}

$params = array();
$params['url_list'] = Url::client_embed_tpl_list($client_id);
$params['url_create'] = Url::client_embed_tpl_create($client_id);
$params['templates'] = $templates;
$params = array_merge($params,Client::adminHeaderParams($client_id,$client['company']));

Tpl::_get()->output('client_embed_tpl_list',$params);
