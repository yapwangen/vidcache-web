<?php
namespace Vidcache\Client;
use \LSS\Config;
use \LSS\Db;
use \Vidcache\SDK;

abstract class FS {

	public static function fetchFolderByPath($path){
		return Db::_get()->fetch(
			'SELECT * FROM `folders` WHERE `path` = ?'
			,array($path)
		);
	}

	public static function fetchFileByPath($path){
		return Db::_get()->fetch(
			'SELECT * FROM `files` WHERE `path` = ?'
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
			'SELECT * FROM `files` WHERE `folder_id` = ?'
			,array($folder['folder_id'])
		);
	}

	public static function fetchFileByHandle($handle){
		return Db::_get()->fetch(
			'SELECT f.* FROM `file_handles` AS fh'
			.' LEFT JOIN `files` AS f ON f.path = fh.path'
			.' WHERE fh.handle = ?'
			,array($handle)
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
		//create the file
		return Db::_get()->insert(
			'files'
			,array(
				 'file_id'		=>	$file['client_file_id']
				,'folder_id'	=>	$file['client_folder_id']
				,'file_chksum'	=>	$file['file_chksum']
				,'path'			=>	$file['path']
				,'name'			=>	$file['name']
				,'mime_type'	=>	$file['mime_type']
				,'size'			=>	$file['size']
				,'hits'			=>	$file['hits']
				,'hits_mtd'		=>	$file['hits_mtd']
				,'bandwidth'	=>	$file['bandwidth']
				,'bandwidth_mtd'=>	$file['bandwidth_mtd']
				,'created'		=>	$file['created']
				,'updated'		=>	$file['updated']
				,'deleted'		=>	$file['deleted']
		));
	}

	public static function fileCreateHandle($file){
		//check handles until we get a unique one
		do {
			$handle = gen_handle();
			$result = Db::_get()->fetch(
				'SELECT * FROM `file_handles` WHERE `handle` = ?'
				,array($handle)
			);
			if(!$result) break;
			if($result && $result['path'] == $file['path']) break;
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

}
