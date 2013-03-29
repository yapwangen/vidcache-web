<?php
namespace Vidcache\Admin;

use \LSS\Db;
use \LSS\Config;
use \Vidcache\Admin\File;

abstract class Node {

	public static function fetchAll(){
		//filter out expired nodes
		$current = time() - Config::get('server','timeout');
		$where = Db::prepWhere(array('last_updated'=>array('>=',$current)));
		return Db::_get()->fetchAll('SELECT * FROM `nodes`'.array_shift($where),$where);
	}

	public static function createParams(){
		return array(
			 'hostname'			=>	NULL
			,'address'			=>	NULL
			,'port'				=>	80
			,'load_1m'			=>	0.00
			,'load_5m'			=>	0.00
			,'load_15m'			=>	0.00
			,'total_space'		=>	0
			,'free_space'		=>	0
			,'pct_data'			=>	0
			,'pct_cache'		=>	0
			,'max_pct_data'		=>	0
			,'max_pct_cache'	=>	0
			,'cache_total'		=>	0
			,'cache_free'		=>	0
			,'cache_file_count'	=>	0
			,'last_updated'		=>	NULL
		);
	}

	public static function fetch($node_id){
		return Db::_get()->fetch(
			'SELECT * FROM `nodes` WHERE `node_id`=?'
			,array($node_id)
			,'Node could not be found: '.$node_id
		);
	}

	public static function fetchByHost($hostname,$ip){
		return Db::_get()->fetch(
			'SELECT * FROM `nodes` WHERE `hostname` = ? OR `address` = ?'
			,array($hostname,$ip)
		);
	}

	public static function fetchByHostname($hostname){
		return Db::_get()->fetch(
			'SELECT * FROM `nodes` WHERE `hostname`=?'
			,array($hostname)
		);
	}

	public static function fetchByAddress($address){
		return Db::_get()->fetch(
			'SELECT * FROM `nodes` WHERE `address`=?'
			,array($address)
		);
	}

	public static function create($data=array()){
		if(isset($data['node_id'])) unset($data['node_id']);
		return Db::_get()->insert('nodes',$data);
	}

	public static function update($node_id,$data=array()){
		if(isset($data['node_id'])) unset($data['node_id']);
		$data['last_updated'] = time();
		return Db::_get()->update('nodes','node_id',$node_id,$data);
	}

	public static function delete($node_id){
		return Db::_get()->run('DELETE FROM `nodes` WHERE `node_id`=?',array($node_id));
	}

	public static function drop($value=null,$name='node_id'){
		foreach(self::fetchAll() as $nodes)
			$arr[$nodes['node_id']] = $nodes['hostname'];
		$drop = \LSS\Form\Drop::_get()->setOptions($arr);
		$drop->setName($name);
		$drop->setValue($value);
		return $drop;
	}

	//selects a node to import to
	//	requires files size
	//	returns false if no node could be found
	public static function selectForImport($file_size){
		//select a node to import to
		$free_space = $using_node = false;
		foreach(self::fetchAll() as $node){
			//check out the quotas
			if($node['pct_data'] > $node['max_pct_data']) continue; //node has too much data
			//at this point the node qualifys but we want the one with the least amount of free space
			if(!$free_space){
				$free_space = $node['free_space'];
				$using_node = $node;
			}
			if($node['free_space'] < $free_space) continue; //node doesnt need more files right now
			//this server seems like the best candidate unless there is a better one
			$free_space = $node['free_space'];
			$using_node = $node;
		}
		return $using_node;
	}

	//selects a node that has a file copy
	//	will select by lowest load
	public static function selectByFile($file_id){
		$load = $using_node = false;
		foreach(File::fetchAllClones($file_id) as $row){
			//get node
			$node = self::fetch($row['node_id']);
			if(!$load || $node['load_1m'] < $load){
				$load = $node['load_1m'];
				$using_node = $node;
			} else continue;
		}
		return $using_node;
	}


}
