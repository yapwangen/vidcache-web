<?php
namespace Vidcache\Admin\Ticket;

use \LSS\Db;

abstract class Department {

	public static function fetchAll(){
		return Db::_get()->fetchAll('SELECT * FROM `ticket_departments` ORDER BY `name` ASC');
	}

	public static function fetch($ticket_department_id){
		$pairs['ticket_department_id'] = $ticket_department_id;
		$where = Db::prepWhere($pairs);
		return Db::_get()->fetch(
				'SELECT * FROM `ticket_departments`'
				.array_shift($where)
			,$where
		);
	}

	public static function fetchByEmail($email){
		$where = Db::prepWhere(array('email'=>$email));
		return Db::_get()->fetch(
				'SELECT * FROM `ticket_departments`'
				.array_shift($where)
			,$where
		);
	}

	public static function createParams(){
		return array(
			 'name'		=>	''
			,'email'	=>	''
			,'cc'		=>	''
			,'bcc'		=>	''
		);
	}

	public static function create($params=array()){
		return Db::_get()->insert('ticket_departments',$params);
	}

	public static function update($ticket_department_id,$params=array()){
		return Db::_get()->update('ticket_departments','ticket_department_id',$ticket_department_id,$params);
	}

	public static function delete($ticket_department_id){
		$pairs['ticket_department_id'] = $ticket_department_id;
		$where = Db::prepWhere($pairs);
		return Db::_get()->run('DELETE FROM `ticket_departments`'.array_shift($where),$where);
	}

	public static function drop($value=null,$name='ticket_department_id'){
		$arr = array();
		foreach(self::fetchAll() as $ticket_department)
			$arr[$ticket_department['ticket_department_id']] = $ticket_department['name'].' <'.$ticket_department['email'].'>';
		$drop = \LSS\Form\Drop::_get()->setOptions($arr);
		$drop->setName($name);
		$drop->setValue($value);
		return $drop;
	}

}

