<?php
use \LSS\Url;

//ticket management
Url::_register('client_ticket_list',Url::client().'&do=ticket_list&client_id=$1');
Url::_register('client_ticket_create',Url::client().'&do=ticket_create&client_id=$1');
Url::_register('client_ticket_manage',Url::client().'&do=ticket_manage&client_id=$1&ticket_id=$2');