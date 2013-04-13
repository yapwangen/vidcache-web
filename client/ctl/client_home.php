<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Client\Session;

if(!Session::isLoggedIn())
	redirect(Url::login());

$params = array();
$params['files_pct'] = 0;
$params['files_used'] = 0;
$params['tier_files'] = 100;
$params['space_pct'] = 0;
$params['space_used'] = 0;
$params['tier_space'] = 100;
$params['transfer_pct'] = 0;
$params['transfer_used'] = 0;
$params['tier_transfer'] = 100;
$params['next_month'] = date('F',strtotime('next month'));
$params['files'] = array();
Tpl::_get()->output('client_home',$params);
