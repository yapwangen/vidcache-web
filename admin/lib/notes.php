<?php
namespace Vidcache\Admin;

use \LSS\Db;

abstract class Notes {

	public static function add($type,$reference,$author_type,$author_reference,$msg){
		return Db::_get()->insert('notes',array(
			 'type'				=>	$type
			,'reference'		=>	$reference
			,'author_type'		=>	$author_type
			,'author_reference'	=>	$author_reference
			,'message'			=>	$msg
			,'date'				=>	microtime(true)
		));
	}
	
	public static function fetch($note_id){
		return Db::_get()->fetch('SELECT * FROM notes WHERE note_id = ?',array($note_id));
	}
	
	public static function fetchAll($type=null,$reference=null,$direction='ASC'){
		$pairs = array();
		if(!is_null($type)) $pairs['type'] = $type;
		if(!is_null($reference)) $pairs['reference'] = $reference;
		$where = Db::prepWhere($pairs);
		return Db::_get()->fetchAll(
				'SELECT n.*, s.name AS staff_author_name FROM `notes` AS n'
				.' LEFT JOIN staff AS s ON s.staff_id = n.author_reference'
				.array_shift($where)
				.' ORDER BY date '.$direction
			,$where
		);
	}
	
}
