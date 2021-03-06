<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Client\Contact;

$params = Client::fetch(get('client_id'));

//account contacts
$params['url_client_edit'] = Url::client_edit(get('client_id'));
$params['url_client_contact_create'] = Url::client_contact_create(get('client_id'));
$params['contacts'] = array();
foreach(Contact::fetchAllByAccount(get('client_id'),Client::$accounts_table) as $contact){
	$contact['url_edit'] = Url::client_contact_edit($contact['client_id'],$contact['contact_id']);
	$contact['address'] = nl2br(Contact::formatBlock($contact));
	$contact['phone'] = Contact::formatPhone($contact);
	$params['contacts'][] = $contact;
}

$params = array_merge($params,Client::adminHeaderParams($params['client_id'],$params['company']));

Tpl::_get()->output('client_manage',$params);
