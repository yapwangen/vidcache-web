<?php
namespace Vidcache\Admin\Client;

use \Exception;
use \LSS\Db;
use \Vidcache\Admin\Client\Folder as ClientFolder;
use \Vidcache\Admin\Client\File as ClientFile;
use \Vidcache\Admin\File as AdminFile;
use \Vidcache\Admin\Node;
use \Vidcache\VC;
use \Vidcache\Server;

abstract class File {

	public static function fetchAll($client_id){
		return Db::_get()->fetchAll(
			'SELECT * FROM `client_files` WHERE `client_id` = ? AND `deleted` IS NULL'
			,array($client_id)
		);
	}

	public static function fetchAllByFolder($client_id,$client_folder_id){
		$where = Db::prepWhere(array(
			'client_id'			=>	$client_id
			,'client_folder_id'	=>	$client_folder_id
			,'deleted'			=>	array(Db::IS_NULL)
		));
		$files = Db::_get()->fetchAll(
				'SELECT f.*, c.* FROM `client_files` AS c'
				.' LEFT JOIN `files` AS f ON f.file_id = c.file_id'
				.array_shift($where)
			,$where
		);
		//merge in some stats info
		foreach($files as &$file){
			$file['bandwidth'] = $file['bandwidth_mtd'] = $file['hits'] = $file['hits_mtd'] = 0;
			foreach(AdminFile::statsGet($file['file_id'],$client_id) as $s){
				$file['bandwidth'] += $s['bytes'];
				$file['hits'] += $s['hits'];
				if(
					($s['period_start'] >= strtotime('first day of this month 00:00:00 UTC'))
					&&
					($s['period_start'] <= strtotime('last day of this month 00:00:00 UTC'))
				){
					$file['bandwidth_mtd'] += $s['bytes'];
					$file['hits_mtd'] += $s['hits'];
				}
			}
		}
		return $files;
	}

	public static function countByFolder($client_folder_id){
		$result = Db::_get()->fetch('SELECT COUNT(*) AS `file_count` FROM `client_files` WHERE `client_folder_id` = ?',array($client_folder_id));
		if(!$result) return 0;
		return $result['file_count'];
	}

	public static function max(){
		return array_shift(Db::_get()->fetch('SELECT MAX(`client_file_id`) FROM `client_files`'));
	}

	public static function createParams(){
		return array(
			 'client_id'			=>''
			,'client_folder_id'		=> ''
			,'file_id'				=> ''
			,'file_chksum'			=> ''
			,'name'					=> ''
		);
	}

	public static function fetch($client_file_id){
		return Db::_get()->fetch(
			'SELECT * FROM `client_files` WHERE `client_file_id`=?'
			,array($client_file_id)
			,'File could not be found: '.$client_file_id
		);
	}

	public static function fetchFull($client_file_id){
		return Db::_get()->fetch(
			'SELECT f.*, c.* FROM `client_files` AS c'
			.' LEFT JOIN `files` AS f ON f.`file_id` = c.`file_id`'
			.' WHERE c.`client_file_id` = ?'
			,array($client_file_id)
		);
	}

	public static function fetchByChksum($client_id,$file_chksum){
		return Db::_get()->fetch(
			'SELECT * FROM `client_files` WHERE `client_id`=? AND `file_chksum`=?'
			,array($client_id,$file_chksum)
		);
	}

	public static function fetchByFileId($client_id,$file_id){
		return Db::_get()->fetch(
			'SELECT * FROM `client_files` WHERE `client_id`=? AND `file_id`=? AND `deleted` IS NOT NULL'
			,array($client_id,$file_id)
		);
	}

	public static function create($data=array()){
		$data['created'] = time();
		return Db::_get()->insert('client_files',$data);
	}

	public static function update($client_file_id,$data=array()){
		$data['updated'] = time();
		return Db::_get()->update('client_files','client_file_id',$client_file_id,$data);
	}

	public static function delete($client_file_id){
		//we dont do any hard deletion here instead we wait for the garbage collection
		return self::update($client_file_id,array('deleted'=>time()));
	}

	public static function upload($client_id,$client_folder_id,$tmp_file,$file_name){
		//setup the filesystem wrapper
		VC::register();
		//find chksum
		$chksum = sha1_file($tmp_file);
		//check if the file already exists in our database
		$file = AdminFile::fetchByChksum($chksum);

		//only import the file if we have to
		if(!$file){

			//import the file into the cluster
			$filesize = filesize($tmp_file);
			$node = Node::selectForImport($filesize);
			if(!$node)
				throw new Exception('Could not find node for import');
			//copy the temp file to the cluster
			if(!copy($tmp_file,'vc://?node='.$node['hostname']))
				throw new Exception('Failed to transfer file to cluster');
			//store in our local file database
			$file_id = AdminFile::create(array(
				'chksum'		=>		$chksum
				,'mime_type'	=>		mime_content_type($tmp_file)
				,'size'			=>		filesize($tmp_file)
				,'created'		=>		time()
			));
			if(!$file_id)
				throw new Exception('Could not store file in our local database');
			//store clone location of the node
			$rv = AdminFile::createClone(array('file_id'=>$file_id,'node_id'=>$node['node_id'],'is_cache'=>0));
			if($rv === false)
				throw new Exception('Could not store file clone location');
		} else {
			$file_id = $file['file_id'];
		}

		//make sure the client doesnt already have the file
		if(($client_file = self::fetchByFileId($client_id,$file['file_id']))){
			throw new Exception('File already exists in tree: '.$client_file['client_file_id'].' '.$client_file['name']);
			return $client_file['client_file_id'];
		} else {
			//store the file in our user database
			$client_file_id = ClientFile::create(array(
				 'client_folder_id'		=>	$client_folder_id
				,'client_id'			=>	$client_id
				,'file_id'				=>	$file_id
				,'file_chksum'			=>	$chksum
				,'name'					=>	$file_name
			));
		}
		//unlink staging file
		if(file_exists($tmp_file)) unlink($tmp_file);
		return $client_file_id;

	}

	public static function genPreviewFromClientFile($client_file_id){
		$client_file = self::fetch($client_file_id);
		$client_folder = ClientFolder::fetch($client_file['client_folder_id']);
		$file = AdminFile::fetch($client_file['file_id']);
		//select a node to download from
		$node = Node::selectByFile($file['file_id']);
		if(!$node)
			throw new Exception('Could not find the file on any nodes');
		//send the tmp file to the generation
		$vc = Server::load()->setNode($node['hostname']);
		$preview_chksum = array_shift($vc->fileGenVideoThumbnail($file['chksum']));
		$file = $vc->fileInfo($preview_chksum);
		//now the file needs to be added to the cluster
		$file_name = preg_replace('/\..*$/','',$client_file['name']).'-preview.png';
		try {
			//store file in main database
			if(!($sys_file = AdminFile::fetchByChksum($preview_chksum))){
				//create the file
				$file_id = AdminFile::create(array(
					'chksum'		=>		$file['chksum']
					,'mime_type'	=>		$file['mime_type']
					,'size'			=>		$file['size']
					,'created'		=>		$file['created']
				));
			} else {
				$file_id = $sys_file['file_id'];
				//add a clone if we need to
				if($sys_file['node_id'] != $node['node_id'] && !AdminFile::cloneExists($file_id,$node['node_id']))
					$rv = AdminFile::createClone(array('file_id'=>$file_id,'node_id'=>$node['node_id'],'is_cache'=>0));
			}
			//store the file in our user database
			$client_file_id = ClientFile::create(array(
				 'client_folder_id'		=>	$client_folder['client_folder_id']
				,'client_id'			=>	$client_file['client_id']
				,'file_id'				=>	$file_id
				,'file_chksum'			=>	$file['chksum']
				,'name'					=>	$file_name
			));
		} catch(Exception $e){
			if(file_exists($preview_tmp_file)) unlink($preview_tmp_file);
			if(file_exists($staging_path)) unlink($staging_path);
			throw $e;
		}
		return $client_file_id;
	}

}
