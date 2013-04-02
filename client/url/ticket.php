<?php
use \LSS\Url;

//ticket management
Url::_register('client_ticket_list',Url::client().'&do=ticket_list');
Url::_register('client_ticket_create',Url::client().'&do=ticket_create');
Url::_register('client_ticket_manage',Url::client().'&do=ticket_manage&ticket_id=$1');