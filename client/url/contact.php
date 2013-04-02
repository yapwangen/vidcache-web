<?php
use \LSS\Url;

//contact management
Url::_register('client_contact_create',Url::client().'&do=contact_create&client_id=$1');
Url::_register('client_contact_edit',Url::client().'&do=contact_edit&client_id=$1&contact_id=$2');
