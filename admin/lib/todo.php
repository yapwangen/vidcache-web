<?php
namespace Vidcache\Admin;

use \LSS\Db;

abstract class Todo {

	public static function add($type,$reference,$msg,$due=null){
		return Db::_get()->insert(
			 'todo'
			,array(
				 'type'			=>	$type
				,'reference'	=>	$reference
				,'message'		=>	$msg
				,'date'			=>	microtime(true)
				,'due'			=>	$due
			)
		);
	}
	
	public static function fetchAll($type=null,$reference=null,$is_complete=null,$direction='DESC'){
		$pairs = array();
		if(!is_null($type)) $pairs['type'] = $type;
		if(!is_null($reference)) $pairs['reference'] = $reference;
		if(!is_null($is_complete)) $pairs['is_complete'] = $is_complete ? 1 : 0;
		$where = Db::prepWhere($pairs);
		return Db::_get()->fetchAll(
				'SELECT * FROM todo '
				.array_shift($where)
				.' ORDER BY date '.$direction
			,$where
		);
	}
	
	public static function fetch($todo_id){
		return Db::_get()->fetch(
				'SELECT * FROM todo WHERE todo_id = ?'
			,array($todo_id)
		);
	}
	
	public static function complete($todo_id){
		return Db::_get()->update('todo','todo_id',$todo_id,array('is_complete'=>1));
	}
	
}
