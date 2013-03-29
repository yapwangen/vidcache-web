<?php

require_once('boot.php');

__boot_pre();
__init_load_files('conf.d/client',false,'__export_config',array(&$config));
@include('config.php');
__boot_post();

//do specific init
try {
	//urls
	__init_load_files(ROOT.'/url.d/client',true);
	
	//init modules
	__init_load_files(ROOT.'/init.d/client',true);
	
	//router
	Router::init();
	Router::_get()->setDefault('/ctl/client/client_profile.php');
	__init_load_files(ROOT.'/rtr.d/client');
	require(Router::_get()->route(req('act'),req('do'),req('fire')));

} catch(Exception $e){
	sysError($e->getMessage());
}
