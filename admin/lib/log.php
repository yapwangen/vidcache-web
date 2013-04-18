<?php
namespace Vidcache\Admin;

use \LSS\Db;

abstract class Log {

	public static function add($type,$reference,$msg){
		return Db::_get()->insert('log',array('type'=>$type,'reference'=>$reference,'message'=>$msg,'date'=>microtime(true)));
	}
	
	public static function get($log_id){
		return Db::_get()->fetch('SELECT * FROM log WHERE log_id = ?',array($log_id));
	}
	
	public static function fetchAll($type=null,$reference=null,$direction='DESC'){
		$pairs = array();
		if(!is_null($type)) $pairs['type'] = $type;
		if(!is_null($reference)) $pairs['reference'] = $reference;
		$where = Db::prepWhere($pairs);
		return Db::_get()->fetchAll('SELECT * FROM log '.array_shift($where).' ORDER BY date '.$direction,$where);
	}
	
}
