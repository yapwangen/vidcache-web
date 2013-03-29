<?php
namespace Vidcache\Admin\Client;

use \Exception;
use \LSS\Db;
use \LSS\Url;
use \LSS\Validate;

abstract class Folder {

	public static function fetchAll($client_id){
		return Db::_get()->fetchAll(
			'SELECT * FROM `client_folders` WHERE `client_id` = ? AND `deleted` IS NULL'
			,array($client_id)
		);
	}

	public static function fetchAllByParent($parent_client_folder_id=null,$client_id=null){
		if(is_null($parent_client_folder_id) && is_null($client_id))
			throw new Exception('To list folders either a parent folder or client_id is required');
		//figure values for where
		if(is_null($parent_client_folder_id)) $parent_client_folder_id = array(Db::IS_NULL);
		if(!is_null($client_id)) $arr['client_id'] = $client_id;
		$arr['parent_client_folder_id'] = $parent_client_folder_id;
		$arr['deleted'] = array(Db::IS_NULL);
		//make where stmt
		$where = Db::prepWhere($arr);
		//query and return
		return Db::_get()->fetchAll(
				'SELECT * FROM `client_folders`'
				.array_shift($where)
			,$where
		);
	}

	public static function createParams(){
		return array(
			 'client_id'				=> ''
			,'parent_client_folder_id'	=> NULL
			,'name'						=> '(unnamed)'
			,'created'					=> time()
			,'updated'					=> NULL
			,'deleted'					=> NULL
		);
	}

	public static function countByParent($parent_client_folder_id){
		$result = Db::_get()->fetch(
				'SELECT count(*) AS `folder_count` FROM `client_folders`'
				.' WHERE `parent_client_folder_id` = ? AND `deleted` IS NULL'
			,array($parent_client_folder_id)
		);
		if(!$result) return 0;
		return $result['folder_count'];
	}

	public static function max(){
		return array_shift(Db::_get()->fetch('SELECT MAX(`client_folder_id`) FROM `client_folders`'));
	}

	public static function validate($data){
		Validate::prime($data);
		Validate::go('name')->not('blank')->is('alnums');
		Validate::go('client_id')->not('blank')->is('num');
		Validate::paint();
	}

	public static function fetch($client_folder_id){
		return Db::_get()->fetch(
			'SELECT * FROM `client_folders` WHERE `client_folder_id`=?'
			.' AND `deleted` IS NULL'
			,array($client_folder_id)
			,'Folder could not be found: '.$client_folder_id
		);
	}

	public static function create($data=array()){
		$data['created'] = time();
		if(!$data['parent_client_folder_id']) $data['parent_client_folder_id'] = NULL;
		return Db::_get()->insert('client_folders',$data);
	}

	public static function update($client_folder_id,$data=array()){
		$data['updated'] = time();
		return Db::_get()->update('client_folders','client_folder_id',$client_folder_id,$data);
	}

	public static function delete($client_folder_id,$recurse=false){
		//get file and folder counts
		$folder = self::fetch($client_folder_id);
		$folder_count = self::countByParent($client_folder_id);
		$file_count = File::countByFolder($client_folder_id);
		//check if files or folders exist without recursion being true
		if(!$recurse && ($folder_count > 0 || $file_count > 0))
			throw new Exception('No recursive deletion selection and file(s) and/or folder(s) still exist');
		//recurse and delete any files in this folder
		if($recurse && $file_count > 0){
			foreach(File::fetchAllByFolder($folder['client_id'],$client_folder_id) as $file)
				File::delete($file['client_file_id']);
		}
		//recurse and recursively delete any folders
		if($recurse && $folder_count >0){
			foreach(self::fetchAllByParent($client_folder_id) as $folder)
				self::delete($folder['client_folder_id'],true);
		}
		//delete this folder
		return self::update($client_folder_id,array('deleted'=>time()));
	}

	public static function path($client_id,$client_folder_id){
		$arr = array();
		if(!is_null($client_folder_id)){
			do {
				$folder = self::fetch($client_folder_id);
				$array['name'] = $folder['name'];
				if(class_exists('\LSS\Url') && Url::_isCallable('client_file_list_by_folder'))
					$array['url'] =	Url::client_file_list_by_folder($client_id,$folder['client_folder_id']);
				$arr[] = $array;
				$client_folder_id = $folder['parent_client_folder_id'];
			} while(!is_null($client_folder_id));
			$arr = array_reverse($arr);
		}
		if(class_exists('\LSS\Url') && Url::_isCallable('client_file_list'))
			array_unshift($arr,array('url'=>Url::client_file_list($client_id),'name'=>'Root'));
		else
			array_unshift($arr,array('name'=>'Root'));
		return $arr;
	}

}
