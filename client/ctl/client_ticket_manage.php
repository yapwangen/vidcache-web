<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Staff;
use \Vidcache\Admin\Ticket;
use \Vidcache\Admin\Ticket\Department as TicketDepartment;

//functions
function registerTicketModule($file,&$modules,$client,$ticket,&$params){
	include_once($file);
	if(!isset($module)) return; //not a valid module
	$modules[] = $module;
	//init if we can
	if(function_exists($module['initCallback'])) $module['initCallback']($client,$ticket,$params);
}

//gather global data
$client = Client::fetch(get('client_id'));
$ticket = Ticket::fetch(get('ticket_id'));
$params = array_merge($client,$ticket,post());

//load ticket modules
$modules = array();
__init_load_files(ROOT_GROUP.'/proc/ticket','registerTicketModule',array(&$modules,$client,$ticket,&$params));

//deal with post data
if(post()){
	try {
		$errors = false;
		try {
			//generic post
			if(post('update_ticket_details') == 'true'){
				Ticket::update($ticket['ticket_id'],array(
					'subject'					=>	post('subject')
					,'ticket_department_id'		=>	post('ticket_department_id')
					,'staff_id'					=>	post('staff_id')
					,'status'					=>	post('status')
				));
				Ticket::updateRecipients($ticket['ticket_id']);
				alert('Ticket details updated',true,true);
				redirect(Url::client_ticket_manage($client['client_id'],$ticket['ticket_id']));
			}
		} catch(Exception $e){
			$errors = true;
			alert($e->getMessage(),false);
		}
		//module post
		foreach($modules as $module){
			try {
				if(function_exists($module['postCallback'])) $module['postCallback'](post(),$client,$ticket);
			} catch(Exception $e){
				$errors = true;
				alert($e->getMessage(),false);
			}
		}
		if($errors) throw new Exception('There were errors on the submission');
		else redirect(Url::client_ticket_manage(get('client_id'),get('ticket_id')));
	} catch(Exception $e){
		//void (the other errors are already handled)
	}
}

//add display params
$params['url_client_ticket_manage'] = Url::client_ticket_manage($client['client_id'],$ticket['ticket_id']);
$params['ticket_department_drop'] = TicketDepartment::drop($ticket['ticket_department_id']);
$params['ticket_status_drop'] = Ticket::statusDrop($ticket['status']);
$params['staff_drop'] = Staff::drop($ticket['staff_id']);
$params = array_merge($params,Client::adminHeaderParams($client['client_id'],$client['company']));

//module display
$params['module_display'] = '';
foreach($modules as $module){
	if(function_exists($module['displayCallback']))
		$params['module_display'] .= $module['displayCallback']($client,$ticket,$params);
}

//parse page
$out = Tpl::_get()->output('client_ticket_manage',$params,Tpl::OUTPUT_RETURN);

//module shutdown
foreach($modules as $module){
	if(function_exists($module['shutdownCallback'])) $module['shutdownCallback']($client,$ticket,$params);
}

echo $out; exit;
