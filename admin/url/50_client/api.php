<?php
use \LSS\Url;

Url::_register('client_api_manage',Url::client().'&do=api_manage&client_id=$1');
Url::_register('client_api',Url::client_api_manage());
