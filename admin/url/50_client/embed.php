<?php
use \LSS\Url;

//embed management
Url::_register('client_embed_list',Url::client().'&do=embed_list&client_id=$1');
Url::_register('client_embed_create',Url::client().'&do=embed_create&client_id=$1');
Url::_register('client_embed_manage',Url::client().'&do=embed_manage&client_id=$1&handle=$2');