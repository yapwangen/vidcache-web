<?php
namespace Vidcache\Admin\Client;

use \Exception;
use \LSS\Db;
use \Vidcache\Admin\Node;
use \Vidcache\Server;

abstract class EmbedTpl {

	public static function fetchAll(){
		return Db::_get()->fetchAll(
			'SELECT * FROM `client_embed_tpl`'
		);
	}

	public static function fetchAllByClient($client_id){
		return Db::_get()->fetchAll(
			'SELECT * FROM `client_embed_tpl` WHERE `client_id` = ?'
			,array($client_id)
		);
	}

	public static function fetchByHandle($handle){
		return Db::_get()->fetch(
			'SELECT * FROM `client_embed_tpl` WHERE `handle` = ?'
			,array($handle)
		);
	}

	public static function fetch($client_embed_tpl_id){
		return Db::_get()->fetch(
			'SELECT * FROM `client_embed_tpl` WHERE `client_embed_tpl_id` = ?'
			,array($client_embed_tpl_id)
		);
	}

	public static function createParams(){
		return array(
			'name'		=>	''
			,'content'	=>	''
		);
	}

	public static function create($data){
		$data['handle'] = self::genHandle();
		//create
		$id = Db::_get()->insert('client_embed_tpl',$data);
		//setup var defs
		self::setupVars($data['handle'],$data['content']);
		//push template to cluster
		$tpl = self::fetch($id);
		foreach(Node::fetchAll() as $node)
			self::pushTpl($node,$tpl);
		return $id;
	}

	public static function update($client_embed_tpl_id,$data){
		if(isset($data['content'])){
			$tpl = self::fetch($client_embed_tpl_id);
			self::setupVars($tpl['handle'],$data['content']);
			//push template to nodes
			foreach(Node::fetchAll() as $node)
				self::pushTpl($node,$tpl);
		}
		return Db::_get()->update(
			'client_embed_tpl'
			,'client_embed_tpl_id'
			,$client_embed_tpl_id
			,$data
		);
	}

	public static function delete($client_embed_tpl_id){
		//grab tpl
		$tpl = self::fetch($client_embed_tpl_id);
		//check if objects are still using it
		if(Embed::countTplUsage($tpl['handle']) > 0)
			throw new Exception('Template is still in use by embed objects');
		//delete from nodes
		$vc = Server::load();
		foreach(Node::fetchAll() as $node){
			$vc->setNode($node['hostname']);
			$vc->embedDropTpl($tpl['handle']);
		}
		//drop var list
		self::dropVars($tpl['handle']);
		//delete template
		return Db::_get()->run(
			'DELETE FROM `client_embed_tpl` WHERE `client_embed_tpl_id` = ?'
			,array($client_embed_tpl_id)
		);
	}

	public static function setupVars($handle,$content){
		//drop vars
		self::dropVars($handle);
		//scan content
		preg_match_all('/{(.+?)}/six',$content,$matches);
		if(!is_array($matches[1])) return true;
		foreach($matches[1] as $var)
			Db::_get()->insert('client_embed_tpl_vars',array('handle'=>$handle,'name'=>$var),true);
		return true;
	}

	public static function dropVars($handle){
		return Db::_get()->run(
			'DELETE FROM `client_embed_tpl_vars` WHERE `handle` = ?'
			,array($handle)
		);
	}

	public static function genHandle(){
		do {
			$handle = gen_handle();
			$rv = self::fetchByHandle($handle);
		} while($rv !== false);
		return $handle;
	}

	public static function drop($client_id,$value=NULL,$name='client_embed_tpl_id'){
		$arr = array();
		foreach(self::fetchAllByClient($client_id) as $row)
			$arr[$row['client_embed_tpl_id']] = $row['name'];
		$drop = \LSS\Form\Drop::_get()->setOptions($arr);
		$drop->setName($name);
		$drop->setValue($value);
		$drop->allowNull();
		return $drop;
	}

	public static function pushTpl($node,$tpl){
		$vc = Server::load()->setNode($node['hostname']);
		$rv = $vc->embedStoreTpl($tpl['handle'],$tpl['content']);
		if(isset($rv['success'])) return true;
	}
	
}

