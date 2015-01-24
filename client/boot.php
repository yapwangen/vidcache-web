<?php
//include environment
define('ROOT_GROUP',__DIR__);
require_once(dirname(__DIR__).'/vendor/autoload.php');
require_once('lss_boot.php');

//import libs
use \LSS\Config;
use \LSS\Router;
use \Vidcache\Client\URLRewrite;
use \Vidcache\SDK;

//start output buffers and sessions
ob_start();
session_start();

//load the openlss environment
__boot();

//check for maintenance
$vc = SDK::load();
if($vc->maintenanceCheck(Config::get('vidcache','api_key'))){
	require(ROOT_GROUP.'/ctl/maintenance_home.php');
	exit;
}

//check for a rewritten url and handle it
$arr = URLRewrite::setup(get('uri'));
$_GET = array_merge($_GET,$arr);
$_REQUEST = array_merge($_GET,$arr);
unset($arr);

//router
Router::init();
Router::_get()->setRoot(ROOT_GROUP);
Router::_get()->setDefault('/ctl/home.php');
__init_load_files(ROOT_GROUP.'/rtr');
require(Router::_get()->route(req('act'),req('do'),req('fire')));
