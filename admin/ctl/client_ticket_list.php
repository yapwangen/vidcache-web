<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Ticket;

$client = Client::fetch(get('client_id'));

$tickets = array();
foreach(Ticket::fetchAllByClient(get('client_id')) as $ticket){
	$params = $ticket;
	$params['url'] = Url::client_ticket_manage($ticket['client_id'],$ticket['ticket_id']);
	$tickets[] = $params;
}

$params = array();
$params['tickets'] = $tickets;
$params = array_merge($params,Client::adminHeaderParams($client['client_id'],$client['company']));

Tpl::_get()->output('client_ticket_list',$params);
