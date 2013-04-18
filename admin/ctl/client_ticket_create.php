<?php
use \LSS\Url;
use \Vidcache\Admin\Ticket;

$ticket_id = Ticket::create(array(
		'client_id'		=>	get('client_id')
));
redirect(Url::client_ticket_manage(get('client_id'),$ticket_id));
