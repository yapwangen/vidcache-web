<?php
use \LSS\Db;
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Staff;
use \Vidcache\Admin\Staff\Session as StaffSession;
use \Vidcache\Admin\Ticket;
use \Vidcache\Admin\Ticket\Message as TicketMessage;
use \Vidcache\Admin\Ticket\MessageAttachment as TicketMessageAttachment;

//================================================
//Name:			Ticket Messages
//Reference:	ticket_messages
//Position: 	80
//Purpose: 		Ticket Messages
//Deps:			ticket
//================================================

//------------------------
//Module definition
//------------------------
$module = array(
	 'initCallback'		=>	'ticketMessageInit'
	,'displayCallback'	=>	'ticketMessageDisplay'
	,'postCallback'		=>	'ticketMessagePost'
	,'shutdownCallback'	=>	null
	
);

//------------------------
//Module Methods
//------------------------
function ticketMessageInit($account,$ticket,$_params){
	//Check and see if we are downloading a file
	if(get('download_attachment') && get('ticket_message_attachment_id')){
		//kill the buffer
		ob_end_clean();
		$attachment = TicketMessageAttachment::fetch(get('ticket_message_attachment_id'),true);
		if(!$attachment) throw new \Exception('Attachment could not be found: '.get('ticket_message_attachment_id'));
		//send attachment to browser
		$headers = array(
			 'Content-length'		=>	$attachment['file_size']
			,'Content-type'			=>	$attachment['mime_type']
			,'Content-disposition'	=>	'attachment; filename="'.$attachment['file_name'].'"'
			,'Cache-control'		=>	'private'
		);
		foreach($headers as $key => $val) header($key.': '.$val);
		echo $attachment['content'];
		exit;
	}
}

function ticketMessageDisplay($account,$ticket,$_params){
	Tpl::_get()->addCss('theme/admin/css/ticket_message.css');
	Url::_register(
		'client_ticket_message_attachment'
		,Url::client_ticket_manage($account['account_id'],$ticket['ticket_id'])
			.'&amp;ticket_message_attachment_id=$ticket_message_attachment_id'
			.'&amp;download_attachment=true'
		,array('ticket_message_attachment_id')
	);
	$params = array();
	$params['messages'] = array();
	foreach(TicketMessage::fetchAllByTicket($ticket['ticket_id']) as $message){
		$message['class'] = 'ticket_message_'.$message['message_type'];
		$message['posted'] = date('m/d/Y g:i:sA',$message['posted']);
		$message['message'] = nl2br($message['message']);
		$message['attachments'] = array();
		foreach(TicketMessageAttachment::fetchAllByTicketMessage($message['ticket_message_id']) as $attachment){
			$attachment['url'] = Url::client_ticket_message_attachment($attachment['ticket_message_attachment_id']);
			$attachment['file_size'] = format_bytes($attachment['file_size']);
			$message['attachments'][] = $attachment;
		}
		$params['messages'][] = $message;
	}
	return Tpl::_get()->output('client_ticket_message_list',array_merge($_params,$params),Tpl::OUTPUT_RETURN);
}

function ticketMessagePost($post,$account,$ticket){
	if(mda_get($post,'ticket_message_create')){
		try {
			Db::_get()->beginTransaction();
			$message_count = TicketMessage::countByTicket($ticket['ticket_id']);
			//gather some data
			$author_name = StaffSession::get('name');
			$author_email = StaffSession::get('email');
			$author_type = TicketMessage::AT_STAFF;
			$author_id = StaffSession::get('staff_id');
			$message = mda_get($post,'message');
			if(mda_get($post,'is_comment')) $message_type = TicketMessage::MT_STAFF_COMMENT;
			else
				$message_type = $message_count ? TicketMessage::MT_STAFF_REPLY : TicketMessage::MT_STAFF_POST;
			//create ticket message
			$ticket_message_id = TicketMessage::create($ticket['ticket_id'],array(
				 'author_name'		=>	$author_name
				,'author_email'		=>	$author_email
				,'author_type'		=>	$author_type
				,'author_id'		=>	$author_id
				,'message'			=>	$message
				,'message_type'		=>	$message_type
			));
			//add attachments if any
			if(is_array(mda_get($_FILES,'attachments.name'))){
				foreach(mda_get($_FILES,'attachments.name') as $key => $name){
					if(empty($name)) continue;
					$checksum = sha1($content = file_get_contents(mda_get($_FILES,'attachments.tmp_name.'.$key)));
					TicketMessageAttachment::create($ticket_message_id,$ticket['ticket_id'],array(
						  'mime_type'		=>	mda_get($_FILES,'attachments.type.'.$key)
						 ,'file_name'		=>	$name
						 ,'file_size'		=>	mda_get($_FILES,'attachments.size.'.$key)
						 ,'content'			=>	$content
						 ,'checksum'		=>	$checksum
					));
				}
			}
			//update ticket update receipients
			Ticket::updateRecipients($ticket['ticket_id']);
			//email alerts about new reply
			TicketMessage::emailUpdate($ticket_message_id,'Bryan Tong','contact@nullivex.com');
			Db::_get()->commit();
			alert('Message added to ticket',true,true);
			redirect(Url::client_ticket_manage($account['account_id'],$ticket['ticket_id']));
		} catch(Exception $e){
			Db::_get()->rollback();
			throw $e;
		}
	}
}

