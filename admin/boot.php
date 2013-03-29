<?php
//include environment
define('ROOT_GROUP',__DIR__);
require_once(dirname(__DIR__).'/vendor/autoload.php');
require_once('lss_boot.php');

//import libs
use \LSS\Router;

//start output buffers and sessions
ob_start();
session_start();

//load the openlss environment
__boot();


//router
Router::init();
Router::_get()->setRoot(ROOT_GROUP);
Router::_get()->setDefault('/ctl/home.php');
__init_load_files(ROOT_GROUP.'/rtr');
require(Router::_get()->route(req('act'),req('do'),req('fire')));
