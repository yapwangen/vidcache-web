<?php
use \LSS\Config;
use \LSS\Tpl;
use \Vidcache\SDK;

$vc = SDK::load();

$params['site_title'] = Config::get('site_name').' - Undergoing Maintenance';
$params['message'] = $vc->maintenanceRead();

Tpl::_get()->output('maintenance_home',$params);
