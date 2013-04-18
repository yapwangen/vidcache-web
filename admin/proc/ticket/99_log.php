<?php
use \LSS\Tpl;
use \Vidcache\Admin\Log;

//================================================
//Name:			Log
//Reference:	log
//Position: 	99
//Purpose: 		Show ticket log
//Deps:			none
//================================================

//------------------------
//Module definition
//------------------------
$module = array(
	 'initCallback'		=>	null
	,'displayCallback'	=>	'logDisplay'
	,'postCallback'		=>	null
	,'shutdownCallback'	=>	null
	
);

//------------------------
//Module Methods
//------------------------

function logDisplay($account,$ticket,$_params){
	$params['log'] = array();
	foreach(Log::fetchAll('ticket',$ticket['ticket_id']) as $log){
		$log['date'] = date('m/d/Y g:i:sA',$log['date']);
		$params['log'][] = $log;
	}
	return Tpl::_get()->output('client_ticket_log',array_merge($_params,$params),Tpl::OUTPUT_RETURN);
}

