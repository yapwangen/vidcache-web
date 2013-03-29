<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Session as ClientSession;

page_header_client();
ClientSession::requireLogin();

if(post('edit')){
	try {
		Client::update(post('account_id'),post());
		alert('client profile updated successfully',true,true);
		redirect(Url::profile());
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = Client::fetchByContact(ClientSession::get('contact_id'));
$params = array_merge($params,$_POST);
Tpl::_get()->parse('client','profile',$params);

page_footer_client();
output(Tpl::_get()->output());
