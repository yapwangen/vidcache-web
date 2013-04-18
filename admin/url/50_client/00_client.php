<?php
use \LSS\Url;

Url::_register('client',Url::home().'?act=client');
//client management
Url::_register('client_create',Url::client().'&do=create');
Url::_register('client_manage',Url::client().'&do=manage&client_id=$1');
Url::_register('client_edit',Url::client().'&do=edit&client_id=$1');