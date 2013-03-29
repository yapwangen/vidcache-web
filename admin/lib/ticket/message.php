<?php
namespace Vidcache\Admin\Ticket;

use \Exception;
use \LSS\Db;
use \Vidcache\Admin\Ticket;

abstract class Message {

	//author types
	const AT_STAFF	= 'staff';
	const AT_CLIENT		= 'client';
	const AT_VENDOR		= 'vendor';
	const AT_ANONYMOUS	= 'anonymous';

	//message types
	const MT_STAFF_POST			= 'staff_post';
	const MT_STAFF_REPLY		= 'staff_reply';
	const MT_STAFF_COMMENT		= 'staff_comment';
	const MT_CLIENT_POST		= 'client_post';
	const MT_CLIENT_REPLY		= 'client_reply';
	const MT_ANONYMOUS_POST		= 'anonymous_post';
	const MT_ANONYMOUS_REPLY	= 'anonymous_reply';

	public static function fetchAllByTicket($ticket_id){
		$where = self::where(array('ticket_id'=>$ticket_id));
		return Db::_get()->fetchAll(
				'SELECT * FROM `ticket_messages`'
				.array_shift($where)
				.' ORDER BY `posted` ASC'
			,$where
		);
	}

	public static function countByTicket($ticket_id){
		$where = self::where(array('ticket_id'=>$ticket_id));
		$result = Db::_get()->fetch(
				'SELECT count(*) AS message_count FROM `ticket_messages`'
				.array_shift($where)
			,$where
		);
		if($result) return $result['message_count'];
		return 0;
	}

	public static function fetch($ticket_message_id){
		$pairs['ticket_message_id'] = $ticket_message_id;
		$where = Db::prepWhere($pairs);
		return Db::_get()->fetch('SELECT * FROM `ticket_messages`'.array_shift($where),$where);
	}


	public static function create($ticket_id,$params=array()){
		$params['ticket_id'] = $ticket_id;
		$params['posted'] = microtime(true);
		return Db::_get()->insert('ticket_messages',$params);
	}

	public static function update($ticket_message_id,$params=array()){
		return Db::_get()->update('ticket_messages','ticket_message_id',$ticket_message_id,$params);
	}

	public static function delete($ticket_message_id){
		return self::update($ticket_message_id,array('deleted'=>microtime(true)));
	}

	public static function hardDelete($ticket_message_id){
		$pairs['ticket_message_id'] = $ticket_message_id;
		$where = Db::prepWhere($pairs);
		return Db::_get()->run('DELETE FROM `ticket_messages`'.array_shift($where),$where);
	}

	public static function where($pairs=array(),$deleted=false){
		if(!is_null($deleted)) $pairs['deleted'] = $deleted ? array(Db::IS_NOT_NULL) : array(Db::IS_NULL);
		return Db::prepWhere($pairs);
	}

	public static function emailUpdate($ticket_message_id){
		//collect data
		$ticket_message = self::fetch($ticket_message_id);
		$ticket = Ticket::fetch($ticket_message['ticket_id']);
		$ticket_department = Department::fetch($ticket['ticket_department_id']);
		//setup mail transport
		$transport = \Swift_SendmailTransport::newInstance();
		//setup mailer
		$mailer = \Swift_Mailer::newInstance($transport);
		//setup mail header
		$message = \Swift_Message::newInstance();
		$message->setSubject('Ticket [#'.$ticket['ticket_id'].'] '.$ticket['subject']);
		$message->setFrom(array($ticket_message['author_email']=>$ticket_message['author_name']));
		$message->setSender(array($ticket_department['email']=>$ticket_department['name']));
		$message->setReplyTo(array($ticket_department['email']=>$ticket_department['name']));
		//set to
		switch($ticket_message['message_type']){
			case self::MT_STAFF_POST:
			case self::MT_STAFF_REPLY:
				$proto_to = explode(';',$ticket['sender_emails']);
				break;
			case self::MT_CLIENT_POST:
			case self::MT_CLIENT_REPLY:
			case self::MT_ANONYMOUS_POST:
			case self::MT_ANONYMOUS_REPLY:
			case self::MT_STAFF_COMMENT:
				$proto_to = explode(';',$ticket['staff_emails']);
				break;
			default:
				throw new Exception('No emails destined for email update');
				break;
		}
		$to = array();
		foreach($proto_to as $row){
			$parts = explode(',',$row);
			if(count($parts) == 2) $to[$parts[1]] = $parts[0];
			elseif(count($parts) == 1) $to[] = $parts[0];
			else unset($row);
		}
		$message->setTo($to);
		//set cc
		if(!empty($ticket_department['cc']))
			$message->setCc(explode(',',$ticket_department['cc']));
		//set bcc
		if(!empty($ticket_department['bcc']))
			$message->setBcc(explode(',',$ticket_department['bcc']));
		//add message
		$message->setBody($ticket_message['message']);
		//add attachments
		foreach(Ticket\MessageAttachment::fetchAllByTicketMessage($ticket_message_id,true) as $attachment){
			$attach = \Swift_Attachment::newInstance();
			$attach->setFilename($attachment['file_name']);
			$attach->setContentType($attachment['mime_type']);
			$attach->setBody($attachment['content']);
			$message->attach($attach);
		}
		//send the email
		return $mailer->send($message);
	}

}
