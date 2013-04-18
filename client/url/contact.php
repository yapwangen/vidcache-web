<?php
use \LSS\Url;

//contact management
Url::_register('client_contact_create',Url::client().'&do=contact_create');
Url::_register('client_contact_edit',Url::client().'&do=contact_edit&contact_id=$1');
