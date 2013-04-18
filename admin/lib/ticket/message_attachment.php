<?php
namespace Vidcache\Admin\Ticket;

use \LSS\Db;

abstract class MessageAttachment {
	
	public static function fetchAllByTicketMessage($ticket_message_id,$full=false){
		$where = self::where(array('ticket_message_id'=>$ticket_message_id));
		return Db::_get()->fetchAll(
				'SELECT '.self::selectParams($full).' FROM `ticket_message_attachment`'
				.array_shift($where)
			,$where
		);
	}

	public static function fetch($ticket_message_attachment_id,$full=false){
		$where = self::where(array(
			'ticket_message_attachment_id'	=> $ticket_message_attachment_id
		));
		return Db::_get()->fetch(
				'SELECT '.self::selectParams($full).' FROM `ticket_message_attachment`'
				.array_shift($where)
			,$where
		);
	}

	public static function create($ticket_message_id,$ticket_id,$params=array()){
		$params['ticket_message_id'] = $ticket_message_id;
		$params['ticket_id'] = $ticket_id;
		$params['added'] = microtime(true);
		return Db::_get()->insert('ticket_message_attachment',$params);
	}

	public static function where($pairs=array()){
		return Db::prepWhere($pairs);
	}

	public static function selectParams($full=false){
		$params = array(
			 'ticket_message_attachment_id'
			,'ticket_message_id'
			,'ticket_id'
			,'mime_type'
			,'file_name'
			,'file_size'
			,'content'
			,'checksum'
			,'added'	
		);
		if(!$full) unset($params['content']);
		return implode(',',Db::escape($params));
	}

}
