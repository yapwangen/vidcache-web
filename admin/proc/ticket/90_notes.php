<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Notes;
use \Vidcache\Admin\Staff;
use \Vidcache\Admin\Staff\Session as StaffSession;

//================================================
//Name:			Notes
//Reference:	notes
//Position: 	90
//Purpose: 		Ticket Notes
//Deps:			None
//================================================

//------------------------
//Module definition
//------------------------
$module = array(
	 'initCallback'		=>	null
	,'displayCallback'	=>	'noteDisplay'
	,'postCallback'		=>	'notePost'
	,'shutdownCallback'	=>	null
	
);

//------------------------
//Module Methods
//------------------------
function noteDisplay($account,$ticket,$_params){
	$params['notes'] = array();
	foreach(Notes::fetchAll('ticket',$ticket['ticket_id']) as $note){
		$note['date'] = date('m/d/Y g:i:sA',$note['date']);
		//display author
		switch($note['author_type']){
			case 'staff':
				$note['author'] = $note['staff_author_name'];
				break;
			default:
				$note['author'] = 'Anonymous';
				break;
		}
		$params['notes'][] = $note;
	}
	return Tpl::_get()->output('client_ticket_notes',array_merge($_params,$params),Tpl::OUTPUT_RETURN);
}

function notePost($post,$account,$ticket){
	if(mda_get($post,'note_create')){
		Notes::add('ticket',$ticket['ticket_id'],'staff',StaffSession::get('staff_id'),mda_get($post,'message'));
		alert('Note added to ticket',true,true);
		redirect(Url::client_ticket_manage($account['account_id'],$ticket['ticket_id']));
	}
}

