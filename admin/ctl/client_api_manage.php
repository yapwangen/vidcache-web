<?php
use \LSS\Config;
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Client\API;

$client_id = get('client_id');
$client = Client::fetch($client_id);

if(post('generate')){
	try {
		if(!post('confirm'))
			throw new \Exception('API Key generation not confirmed');
		API::generate($client_id);
		alert('API keys updated successfully',true,true);
		redirect(Url::client_api_manage($client_id));
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

//get embed vars
$api_vars = API::fetch($client_id);
if(!$api_vars) $api_vars = API::createParams();

//setup params
$params = $api_vars;
$params = array_merge($params,Client::adminHeaderParams($client_id,$client['company']));

//set sessions
$params['sessions'] = API::fetchAllSessionsByClient($client_id);

//config params
$params['host'] = Config::get('api','http.host');
$params['port'] = Config::get('api','http.port');
$params['scheme'] = Config::get('api','http.scheme');

//form settings
$params['form_action'] = 'generate';
$params['button_label'] = 'Generate Keys';
$params['url_form'] = Url::client_api_manage($client_id);

Tpl::_get()->output('client_api_manage',$params);
