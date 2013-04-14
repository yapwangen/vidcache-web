<?php
use \LSS\Url;

Url::_register('client',Url::home().'?act=client');
//client management
Url::_register('client_create',Url::client().'&do=create');
Url::_register('client_manage',Url::client().'&do=manage');
Url::_register('client_edit',Url::client().'&do=edit');
Url::_register('client_home',Url::client().'&do=home');
Url::_register('client_home_path',Url::client().'&do=home&path=$1');

Url::_register('register',Url::client().'&do=register');
Url::_register('profile',Url::client().'&do=profile');
Url::_register('login',Url::client().'&do=login');
Url::_register('logout',Url::client().'&do=logout');
