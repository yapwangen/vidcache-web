<?php
namespace Vidcache\Client;
use \LSS\Config;
use \LSS\Db;
use \LSS\Url;
use \Vidcache\SDK;
use \Vidcache\ModelService;

abstract class FS {

	public static function fetchFolderById($folder_id){
		return Db::_get()->fetch(
			'SELECT * FROM `folders` WHERE `folder_id` = ?'
			,array($folder_id)
		);
	}

	public static function fetchFileById($file_id){
		return Db::_get()->fetch(
			'SELECT f.*,fh.handle,feh.handle as embed_handle FROM `files` AS f'
			.' LEFT JOIN `file_handles` AS fh ON fh.path = f.path'
			.' LEFT JOIN `file_embed_handles` AS feh ON feh.path = f.path'
			.' WHERE f.file_id = ?'
			,array($file_id)
		);
	}

	public static function fetchFolderByPath($path){
		return Db::_get()->fetch(
			'SELECT * FROM `folders` WHERE `path` = ?'
			,array($path)
		);
	}
	
	public static function fetchFoldersByParentDataTables($columns,$where,$order,$limit,$path){
		$folder = self::fetchFolderByPath(rtrim($path,'/'));
		$result = Db::_get()->fetchAll(
				 ' SELECT SQL_CALC_FOUND_ROWS'
				.' *,"--" AS `type`,"--" AS `size`, "--" AS `hits`, "--" AS `transfer`'
				.' FROM `folders` '
				.' '.(!empty($where[0]) ? $where[0].' AND ' : 'WHERE ').' `parent_folder_id` = ?'
				.$order
				.$limit
			,array_merge($where[1],array($folder['folder_id']))
		);
		$count_result = Db::_get()->fetch('SELECT FOUND_ROWS() AS `row_count`');
		$count_total = Db::_get()->fetch(
			'SELECT count(*) AS `row_count` FROM `folders` WHERE `parent_folder_id` = ?'
			,array($folder['folder_id'])
		);
		return array($result,$count_result['row_count'],$count_total['row_count']);
	}

	public static function fetchFilesByParentDataTables($columns,$where,$order,$limit,$path){
		$folder = self::fetchFolderByPath(rtrim($path,'/'));
		$sql =	 ' SELECT SQL_CALC_FOUND_ROWS'
				.'	f.*,feh.handle as `embed_handle`, fh.handle as `file_handle`'
				.' FROM `files` AS f'
				.' LEFT JOIN `file_handles` AS fh ON fh.path = f.path'
				.' LEFT JOIN `file_embed_handles` AS feh ON feh.path = f.path'
				.' '.(!empty($where[0]) ? $where[0].' AND ' : 'WHERE ').' f.folder_id = ?'
				.$order
				.$limit;
		$vars = array_merge($where[1],array($folder['folder_id']));
		$result = Db::_get()->fetchAll($sql,$vars);
		$count_result = Db::_get()->fetch('SELECT FOUND_ROWS() AS `row_count`');
		$count_total = Db::_get()->fetch(
			'SELECT count(*) AS `row_count` FROM `files` WHERE `folder_id` = ?'
			,array($folder['folder_id'])
		);
		return array($result,$count_result['row_count'],$count_total['row_count']);
	}

	public static function fetchFileByPath($path){
		return Db::_get()->fetch(
			'SELECT f.*,fh.handle,feh.handle as embed_handle FROM `files` AS f'
			.' LEFT JOIN `file_handles` AS fh ON fh.path = f.path'
			.' LEFT JOIN `file_embed_handles` AS feh ON feh.path = f.path'
			.' WHERE f.path = ?'
			,array($path)
		);
	}

	public static function fetchFoldersByParent($path){
		$folder = self::fetchFolderByPath(rtrim($path,'/'));
		return Db::_get()->fetchAll(
			'SELECT * FROM `folders` WHERE `parent_folder_id` = ?'
			,array($folder['folder_id'])
		);
	}

	public static function fetchFilesByParent($path){
		$folder = self::fetchFolderByPath(rtrim($path,'/'));
		return Db::_get()->fetchAll(
			'SELECT f.*,fh.handle,feh.handle as embed_handle FROM `files` AS f'
			.' LEFT JOIN `file_handles` AS fh ON fh.path = f.path'
			.' LEFT JOIN `file_embed_handles` AS feh ON feh.path = f.path'
			.' WHERE f.folder_id = ?'
			,array($folder['folder_id'])
		);
	}

	public static function fetchFileByHandle($handle){
		return Db::_get()->fetch(
			'SELECT f.*,fh.handle,feh.handle as embed_handle FROM `files` AS f'
			.' LEFT JOIN `file_handles` AS fh ON fh.path = f.path'
			.' LEFT JOIN `file_embed_handles` AS feh ON feh.path = f.path'
			.' WHERE fh.handle = ?'
			,array($handle)
		);
	}

	public static function fetchFileByHandleOrEmbedHandle($handle){
		return Db::_get()->fetch(
			'SELECT f.*,fh.handle,feh.handle as embed_handle FROM `files` AS f'
			.' LEFT JOIN `file_handles` AS fh ON fh.path = f.path'
			.' LEFT JOIN `file_embed_handles` AS feh ON feh.path = f.path'
			.' WHERE fh.handle = ? or feh.handle = ?'
			,array($handle,$handle)
		);
	}

	public static function fetchSignature(){
		return Db::_get()->fetch(
			'SELECT * FROM `fs_signature` WHERE `vc_client_id` = ?'
			,array(Config::get('vidcache','client_id'))
		);
	}

	public static function updateSignature($sig){
		return Db::_get()->insert(
			'fs_signature'
			,array(
				 'vc_client_id'	=>	Config::get('vidcache','client_id')
				,'sha1'			=>	$sig['sha1']
				,'md5'			=>	$sig['md5']
				,'updated'		=>	microtime(true)
			)
			,true	//update on duplicate
		);
	}

	public static function updateCache($vc){
		//get the current signature
		$sig = $vc->FSSnapshot(true);
		$our_sig = self::fetchSignature();
		if($sig['sha1'] == $our_sig['sha1'])
			return true; //no update needed
		//now we need to download the updated cache
		$snap = $vc->FSSnapshot();
		return self::import($sig,$snap);
	}

	public static function import($sig,$snap){
		//takes a snapshot and imports it into the database
		self::flush();
		//forward to our recursive import function to do the import
		self::_doImport($snap['fs']['snapshot']);
		//update the signature
		self::updateSignature($sig);
		return true;
	}

	protected static function _doImport($arr){
		if(isset($arr['folders']) && is_array($arr['folders']) && count($arr['folders'])){
			foreach($arr['folders'] as $folder){
				//create the folder
				self::folderCreate($folder);
				//recurse
				self::_doImport($folder);
			}
		}
		if(isset($arr['files']) && is_array($arr['files']) && count($arr['files'])){
			foreach($arr['files'] as $file){
				self::fileCreate($file);
			}
		}
		return true;
	}

	public static function folderCreate($folder){
		return Db::_get()->insert(
			'folders'
			,array(
				 'folder_id'		=>	$folder['client_folder_id']
				,'parent_folder_id'	=>	$folder['parent_client_folder_id']
				,'path'				=>	$folder['path']
				,'name'				=>	$folder['name']
				,'created'			=>	$folder['created']
				,'updated'			=>	$folder['updated']
				,'deleted'			=>	$folder['deleted']
			)
		);
	}

	public static function fileCreate($file){
		//create handle if we dont have done
		self::fileCreateHandle($file);
		//create the embed handle reference (we dont support multiples, so take the last one)
		if(isset($file['embed']) && is_array($file['embed'])){
			$handle = array_pop($file['embed']);
			if(!is_null($handle))
				self::fileStoreEmbedHandle($handle,$file['path']);
		}
		//create the file
		return Db::_get()->insert(
			'files'
			,array(
				 'file_id'				=>	$file['client_file_id']
				,'folder_id'			=>	$file['client_folder_id']
				,'file_chksum'			=>	$file['file_chksum']
				,'path'					=>	$file['path']
				,'name'					=>	$file['name']
				,'mime_type'			=>	$file['mime_type']
				,'size'					=>	$file['size']
				,'hits_this_month'		=>	$file['hits_this_month']
				,'bytes_this_month'		=>	$file['bytes_this_month']
				,'hits_lifetime'		=>	$file['hits_lifetime']
				,'bytes_lifetime'		=>	$file['bytes_lifetime']
				,'created'				=>	$file['created']
				,'updated'				=>	$file['updated']
				,'deleted'				=>	$file['deleted']
		));
	}

	public static function fileStoreEmbedHandle($handle,$path){
		return Db::_get()->insert(
			'file_embed_handles'
			,array(
				 'handle'		=>	$handle
				,'path'			=>	$path
			)
			,true //update on duplicate
		);
	}

	public static function fileCreateHandle($file){
		//check handles until we get a unique one
		do {
			//check if we already have a handle for this path
			$result = Db::_get()->fetch(
				'SELECT * FROM `file_handles` WHERE `path` = ?'
				,array($file['path'])
			);
			if(isset($result['handle'])) return $result['handle'];
			//create a handle nad make sure its unique
			$handle = gen_handle();
			$result = Db::_get()->fetch(
				'SELECT * FROM `file_handles` WHERE `handle` = ?'
				,array($handle)
			);
			//if we got not result that means its unique insert it
			if(!$result) break;
			//if we got a result and its the same path return that
			if($result && $result['path'] == $file['path']) return $result['handle'];
			//otherwise try again
		} while(true);
		//insert the new handle
		Db::_get()->insert(
			'file_handles'
			,array(
				 'handle'	=>	$handle
				,'path'		=>	$file['path']
			)
		);
		//send the handle back
		return $handle;
	}

	public static function flush(){
		Db::_get()->run('TRUNCATE TABLE `folders`');
		Db::_get()->run('TRUNCATE TABLE `files`');
		return true;
	}

	public static function actionType($mime_type){
		if(in_array($mime_type,Config::get('embed','audio_types')))
			return 'listen';
		if(in_array($mime_type,Config::get('embed','video_types')))
			return 'watch';
		if(in_array($mime_type,Config::get('embed','static_types')))
			return 'view';
		return 'download';
	}

	public static function URLsByFile($file){
		$arr = array(
			//cluster urls
			 'url_server_static'		=>	self::buildClusterURL($file['file_id'],$file['file_chksum'],'static')
			,'url_server_embed'			=>	self::buildClusterURL($file['file_id'],$file['embed_handle'],'embed')
			,'url_server_download'		=>	self::buildClusterURL($file['file_id'],$file['file_chksum'],'download')
			,'url_server_stream'		=>	self::buildClusterURL($file['file_id'],$file['file_chksum'],'stream')
		);
		//add action url
		$action = self::actionType($file['mime_type']);
		$arr['url_'.$action] = $arr['url_file'] = 'http://'.$_SERVER['HTTP_HOST'].Url::client_file_view($action,$file['handle']);
		return $arr;
	}

	public static function buildClusterURL($file_id,$uri,$service_type){
		$cluster_url  = Config::get('vidcache','server_scheme');
		//add service type
		if(!is_null($service_type))
			$cluster_url .= $service_type;
		else
			$cluster_url .= 'lookup';
		//dns zone
		$cluster_url .= Config::get('vidcache','dns_zone');
		//cluster http port
		$port = Config::get('vidcache','server_port');
		if($port != '80')
			$cluster_url .= ':'.$port;
		//uri
		$cluster_url .= '/'.$uri;
		//append client_id for tracking
		$cluster_url .= '?client_file_id='.$file_id;
		return $cluster_url;
	}
}
