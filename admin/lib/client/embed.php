<?php
namespace Vidcache\Admin\Client;

use \Exception;
use \LSS\Config;
use \LSS\Db;
use \Vidcache\Admin\File as NodeFile;
use \Vidcache\Admin\Node;
use \Vidcache\Server;

abstract class Embed {

	public static function publish($client_id,$client_folder_id,$post_files,$client_embed_tpl_id){
		$client_embed_tpl = EmbedTpl::fetch($client_embed_tpl_id);
		if(!$client_embed_tpl)
			throw new Exception('Could not embed template');
		//get more file information
		$files = array();
		foreach($post_files as $row)
			$files[] = File::fetchFull($row);
		//now take the files and magically use them correctily
		$handle = self::genHandle();
		$embed_vars = array(
			'file_std'		=>	null
			,'file_hd'		=>	null
			,'img_preview'	=>	null
			,'width'		=>	Config::get('video','default_width')
			,'height'		=>	Config::get('video','default_height')
			,'name'			=>	null
			,'tpl'			=>	$client_embed_tpl['handle']
			,'handle'		=>	$handle
		);
		$std_size = null;
		foreach($files as $file){
			if(strpos($file['mime_type'],'image/png') !== false){
				$embed_vars['img_preview'] = NodeFile::urlStatic($file['chksum']);
				continue;
			}
			if(strpos($file['mime_type'],'video/') !== false){
				if(is_null($embed_vars['file_std'])){
					$embed_vars['file_std'] = NodeFile::urlStream($file['chksum'],$client_id);
					$embed_vars['name'] = $file['name'];
					$std_size = $file['size'];
					continue;
				} else {
					if($file['size'] > $std_size){
						$embed_vars['file_hd'] = NodeFile::urlStream($file['chksum'],$client_id);
						continue;
					} else {
						$embed_vars['file_hd'] = $embed_vars['file_std'];
						$embed_vars['file_std'] = NodeFile::urlStream($file['chksum'],$client_id);
						$embed_vars['name'] = $file['name'];
						$std_size = $file['size'];
						continue;
					}
				}
			}
		}
		foreach($embed_vars as $name => $val)
			self::insertVar($client_id,$client_folder_id,$handle,$name,$val);
		foreach($files as $file)
			self::relateFile($handle,$file['client_file_id']);
		//push to nodes
		$embed = self::fetchObject($handle);
		foreach(Node::fetchAll() as $node)
			self::pushObject($node['hostname'],$embed);
		// return handle
		return $handle;
	}

	public static function fetchAllObjects(){
		$vars = Db::_get()->fetchAll('SELECT * FROM `client_embed_vars`');
		$arr = array();
		//collect all the random vars into collections of variables
		foreach($vars as $row){
			if(!isset($arr[$row['handle']]))
				$arr[$row['handle']]['handle'] = $row['handle'];
			$arr[$row['handle']]['vars'][$row['name']] = $row['value'];
		}
		return $arr;
	}

	public static function fetchObject($handle){
		$arr['handle'] = $handle;
		foreach(self::fetchByHandle($handle) as $row)
			$arr['vars'][$row['name']] = $row['value'];
		return $arr;
	}

	public static function fetchAllHandlesByFile($client_file_id){
		return Db::_get()->fetchAll(
			'SELECT * FROM `client_embed_files` WHERE `client_file_id` = ?'
			,array($client_file_id)
		);
	}

	public static function fetchByHandle($handle){
		return Db::_get()->fetchAll(
			'SELECT * FROM `client_embed_vars` WHERE `handle` = ?'
			,array($handle)
		);
	}

	public static function genHandle(){
		do {
			$handle = gen_handle();
			$rv = self::fetchByHandle($handle);
		} while($rv !== false && (is_array($rv) && count($rv) > 0));
		return $handle;
	}

	public static function fetchVars($handle){
		$vars = array();
		foreach(self::fetchByHandle($handle) as $row)
			$vars[$row['name']] = $row['value'];
		return $vars;
	}

	public static function insertVar($client_id,$client_folder_id,$handle,$name,$val){
		return Db::_get()->insert(
			'client_embed_vars'
			,array(
				'client_id'			=>	$client_id
				,'client_folder_id'	=>	$client_folder_id
				,'handle'			=>	$handle
				,'name'				=>	$name
				,'value'			=>	$val
			)
			,true
		);
	}

	public static function update($handle,$vars){
		Db::_get()->debug=true;
		foreach($vars as $name => $value)
			Db::_get()->update('client_embed_vars',array('handle'=>$handle,'name'=>$name),array('value'=>$value));
		//push updates to nodes
		$embed = self::fetchObject($handle);
		foreach(Node::fetchAll() as $node)
			self::pushObject($node['hostname'],$embed);
		return $handle;
	}

	public static function relateFile($handle,$client_file_id){
		return Db::_get()->insert(
			'client_embed_files'
			,array(
				'handle'			=>	$handle
				,'client_file_id'	=>	$client_file_id
			)
		);
	}

	public static function url($handle){
		return NodeFile::serviceUrl('embed').'/'.$handle;
	}

	public static function delete($handle){
		//delete from nodes
		$vc = Server::load();
		foreach(Node::fetchAll() as $node){
			$vc->setNode($node['hostname']);
			$vc->embedDropVars($handle);
		}
		//delete file entries
		Db::_get()->run('DELETE FROM `client_embed_files` WHERE `handle` = ?',array($handle));
		//delete embed vars
		Db::_get()->run('DELETE FROM `client_embed_vars` WHERE `handle` = ?',array($handle));
		return true;
	}

	public static function countTplUsage($tpl_handle){
		$result = Db::_get()->fetch(
			'SELECT count(*) AS `count` FROM `client_embed_vars`'
			.' WHERE `name` = ? AND `value` = ?'
			,array('tpl',$tpl_handle)
		);
		if(!$result) return 0;
		return $result['count'];
	}

	public static function pushObject($node,$embed){
		$vc = Server::load()->setNode($node);
		$rv = $vc->embedStoreVars($embed['handle'],$embed['vars']);
		if(isset($rv['success'])) return true;
	}

}
