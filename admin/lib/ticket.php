<?php
namespace Vidcache\Admin;

use \LSS\Db;

abstract class Ticket {

	//statuses
	const S_OPEN	= 'open';
	const S_HOLDING	= 'holding';
	const S_CLOSED	= 'closed';
	const S_DELETED	= 'deleted';

	static $statuses = array(
		 self::S_OPEN		=>	'Open'
		,self::S_HOLDING	=>	'Holding'
		,self::S_CLOSED		=>	'Closed'
		,self::S_DELETED	=>	'Deleted'
	);
	
	public static function fetchAllByClient($client_id){
		return Db::_get()->fetchAll('SELECT * FROM `tickets` WHERE `client_id`=?',array($client_id));
	}

	public static function fetch($ticket_id){
		return Db::_get()->fetch('SELECT * FROM `tickets` WHERE `ticket_id`=?',array($ticket_id));
	}

	public static function create($params=array()){
		return Db::_get()->insert('tickets',$params);
	}

	public static function update($ticket_id,$params){
		$params['updated'] = microtime(true);
		return Db::_get()->update(
			 'tickets'
			 ,'ticket_id'
			 ,$ticket_id
			 ,$params
		);
	}

	public static function serialize($ticket_id,$return_serialized=true){
		$arr = array(
			 'items'		=>	array()
			,'ticket'		=>	array()
		);
		$arr['ticket'] = self::fetch($ticket_id);
		foreach(ItemGroupOrder::fetchByTicket($ticket_id) as $item)
			$arr['items'][] = Item::serialize($item['item_id'],false);	
		return $return_serialized ? serialize($arr) : $arr;
	}

	public static function saveSerial($ticket_id,$serial){
		$serial_id = Serial::create(array('serial'=>$serial));
		self::update($ticket_id,array(
			'serial_id'		=>		$serial_id
		));
		return $serial_id;
	}

	public static function fetchSerial($ticket_id){
		$ticket = self::fetch($ticket_id);
		$result = Serial::fetch($ticket['serial_id']);
		if(!$result || !is_array($result) || !isset($result['serial']) || empty($result['serial']))
			throw new \Exception('Serial could not be found by ticket: '.$ticket_id);
		return $result['serial'];
	}

	public static function statusDrop($value=null,$name='status'){
		$drop = \LSS\Form\Drop::_get()->setOptions(self::$statuses);
		$drop->setName($name);
		$drop->setValue($value);
		return $drop;
	}

	public static function updateRecipients($ticket_id){
		//collect data
		$ticket = self::fetch($ticket_id);
		$staff = Staff::fetch($ticket['staff_id']);
		$client = Client::fetch($ticket['client_id']);
		if($client) $client_contact = Client\Contact::fetch($client['primary_contact_id']);
		else $client_contact = false;
		$ticket_messages = Ticket\Message::fetchAllByTicket($ticket_id);
		//setup holders
		$staff_emails = $used_staff_emails = $sender_emails = $used_sender_emails = array();
		//add assigned to staff
		$staff_emails[] = array($staff['name'],$staff['email']);
		//add primary contact for client
		if($client_contact) $sender_emails[] = array($client_contact['first_name'].' '.$client_contact['last_name'],$client_contact['email']);
		//loop through messages and add emails
		foreach($ticket_messages as $message){
			switch($message['author_type']){
				case Ticket\Message::AT_STAFF:
					if(!in_array($message['author_email'],$used_staff_emails)){
						$staff_emails[] = array(trim($message['author_name']),trim($message['author_email']));
						$used_staff_emails[] = $message['author_email'];
					} else continue;
					break;
				default:
					if(!in_array($message['author_email'],$used_sender_emails)){
						$sender_emails[] = array(trim($message['author_name']),trim($message['author_email']));
						$used_sender_emails[] = $message['author_email'];
					} else continue;
					break;
			}
		}
		//serialize the results and store them
		foreach($sender_emails as &$row) $row = implode(',',$row);
		foreach($staff_emails as &$row) $row = implode(',',$row);
		$sender_emails = implode(';',$sender_emails);
		$staff_emails = implode(';',$staff_emails);
		self::update($ticket_id,array(
			  'sender_emails'			=>	$sender_emails
			 ,'staff_emails'			=>	$staff_emails
		));
		return array($sender_emails,$staff_emails);
	}

}
