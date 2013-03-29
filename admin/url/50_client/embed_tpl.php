<?php
use \LSS\Url;

//embed tpl management
Url::_register('client_embed_tpl_list',Url::client().'&do=embed_tpl_list&client_id=$1');
Url::_register('client_embed_tpl_create',Url::client().'&do=embed_tpl_create&client_id=$1');
Url::_register('client_embed_tpl_edit',Url::client().'&do=embed_tpl_edit&client_id=$1&client_embed_tpl_id=$2');
