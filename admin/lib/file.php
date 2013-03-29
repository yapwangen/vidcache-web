<?php
namespace Vidcache\Admin;

use \Exception;
use \LSS\Config;
use \LSS\Db;
use \Vidcache\VC;
use \Vidcache\Server;
use \Vidcache\FileCommon;

abstract class File extends FileCommon {

	public static function fetchAll(){
		return Db::_get()->fetchAll('SELECT * FROM `files`');
	}

	public static function createParams(){
		$time = time();
		return array(
			 'folder_id'		=> ''
			,'chksum'			=> ''
			,'mime_type'		=> ''
			,'size'				=> 0
			,'stream_rate'		=> 0
			,'download_rate'	=> 0
			,'data_copies'		=> 2
			,'data_copies_req'	=> 2
			,'cache_copies'		=> 0
			,'cache_copies_req'	=> 0
			,'created'			=> $time
			,'updated'			=> $time
		);
	}

	public static function fetchAllWithStats(){
		$files = self::fetchAll();
		//merge in some stats info
		foreach($files as &$file){
			$file['bandwidth'] = $file['bandwidth_mtd'] = $file['hits'] = $file['hits_mtd'] = 0;
			foreach(self::statsGet($file['file_id']) as $s){
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

	public static function fetch($file_id){
		return Db::_get()->fetch(
			'SELECT * FROM `files` WHERE `file_id`=?'
			,array($file_id)
			,'File could not be found: '.$file_id
		);
	}

	public static function fetchByChksum($chksum){
		return Db::_get()->fetch(
			'SELECT * FROM `files` WHERE `chksum`=?'
			,array($chksum)
		);
	}

	public static function create($data=array()){
		//calculate appropriate data_copies values
		if(!($data_copies = mda_get($data,'data_copies'))) $data['data_copies'] = 2;
		if(!($data_copies_req = mda_get($data,'data_copies_req'))) $data['data_copies_req'] = 2;
		return Db::_get()->insert('files',$data);
	}

	public static function update($file_id,$data=array()){
		$data['updated'] = time();
		return Db::_get()->update('files','file_id',$file_id,$data);
	}

	//by default this also tells the cluster to delete the file
	public static function delete($file_id,$cluster_delete=true){
		//setup stream wrapper
		VC::register();
		if($cluster_delete){
			$file = self::fetch($file_id);
			if(!$file)
				throw new Exception('Could not find file');
			if(!unlink('vc://'.$file['chksum']))
				throw new Exception('Failed to remove file from cluster');
		}
		//delete clones
		if(!self::deleteClones($file_id)) throw new Exception('Failed to remove file clones');
		//delete stats
		if(!self::deleteStats($file_id)) throw new Exception('Failed to remove file stats');
		//delete file and return
		return Db::_get()->run('DELETE FROM `files` WHERE `file_id` = ?',array($file_id));
	}

	//this will find unused files that are not linked to any clients
	//	they are considered candidates for deletion from the cluster
	public static function findUnused(){
		$files = array();
		foreach(self::fetchAll() as $file){
			$rv = Db::_get()->fetch(
				'SELECT COUNT(*) AS `count` FROM `client_files`'
				.' WHERE `file_id` = ? AND `deleted` IS NULL'
				,array($file['file_id'])
			);
			if($rv === false) continue;
			if($rv['count'] == 0) $files[] = $file;
		}
		return $files;
	}

	//--------------------------------------------------------
	//File Clone Functions
	//--------------------------------------------------------
	public static function fetchClone($file_id,$node_id){
		return Db::_get()->fetch(
			'SELECT `file_clones`.*,`files`.`chksum` AS `chksum` FROM `file_clones`,`files` WHERE `file_clones`.`file_id` = `files`.`file_id` AND `file_clones`.`file_id` = ? AND `node_id` = ?'
			,array($file_id,$node_id)
		);
	}

	public static function fetchAllClones($file_id){
		return Db::_get()->fetchAll(
			'SELECT `file_clones`.*,`files`.`chksum` AS `chksum` FROM `file_clones`,`files` WHERE `file_clones`.`file_id` = `files`.`file_id` AND `file_clones`.`file_id` = ?'
			,array($file_id)
		);
	}

	public static function createClone($data=array()){
		unset($data['chksum']);
		$data['updated'] = time();
		return Db::_get()->insert('file_clones',$data);
	}

	public static function updateClone($file_id,$node_id,$data=array()){
		unset($data['chksum']);
		$data['updated'] = time();
		return Db::_get()->update('file_clones',array('file_id'=>$file_id,'node_id'=>$node_id),$data);
	}

	public static function cloneExists($file_id,$node_id){
		$rv = Db::_get()->fetch(
			'SELECT COUNT(*) AS `count` FROM `file_clones` WHERE `file_id` = ? AND `node_id` = ?'
			,array($file_id,$node_id)
		);
		return ((int)($rv['count']) === 0) ? false : true;
	}

	public static function expireClones($timestamp=null){
		if(!is_numeric($timestamp)) return 0;
		return Db::_get()->run('DELETE FROM `file_clones` WHERE `updated` < ?',array($timestamp))->rowCount();
	}

	public static function deleteClones($file_id){
		return Db::_get()->run('DELETE FROM `file_clones` WHERE `file_id` = ?',array($file_id));
	}

	//--------------------------------------------------------
	//File Stat Functions
	//--------------------------------------------------------

	//checks stat period pass the current time or desired time to check
	//	return false means period is over and a new should be started
	//	return true means the period is still active and stats should be added to it
	public static function statsCheckPeriod($file_id,$client_id,$time){
		$result = self::statsGetLatestPeriod($file_id,$client_id);
		if($result === false) return false;
		$start = $result['period_start'];
		$length = $result['period_length'];
		if($time > ($start + $length)) return false;
		return true;
	}

	public static function statsGet($file_id,$client_id=null){
		if(is_numeric($client_id)){
			return Db::_get()->fetchAll(
				'SELECT * FROM `file_stats` WHERE `file_id` = ? AND `client_id` = ? ORDER BY `period_start`'
				,array($file_id,$client_id)
			);
		} else {
			return Db::_get()->fetchAll(
				'SELECT * FROM `file_stats` WHERE `file_id` = ? ORDER BY `period_start`'
				,array($file_id)
			);
		}
	}

	public static function statsGetLatestPeriod($file_id,$client_id){
		return Db::_get()->fetch(
			'SELECT * FROM `file_stats` WHERE `file_id` = ? AND `client_id` = ? ORDER BY `period_start` DESC LIMIT 1'
			,array($file_id,$client_id)
		);
	}

	public static function statsCreatePeriod($file_id,$client_id,$period_start=null,$period_length=null){
		if(is_null($period_start)) $period_start = time();
		if(is_null($period_length)) $period_length = Config::get('file','stat_period_length');
		return Db::_get()->insert('file_stats',array(
			'file_id'		=>	$file_id
			,'client_id'	=>	$client_id
			,'period_start'	=>	$period_start
			,'period_length'=>	$period_length
		));
	}

	public static function statsAdd($file_id,$client_id,$hits,$bytes){
		$result = self::statsGetLatestPeriod($file_id,$client_id);
		if(!$result)
			throw new Exception('No period exists for stat add: file['.$file_id.'] client['.$client_id.']');
		return Db::_get()->update(
			'file_stats'
			,array(
				'file_id'			=>	$file_id
				,'client_id'		=>	$client_id
				,'period_start'		=>	$result['period_start']
			)
			,array(
				'hits'				=>	($result['hits'] + $hits)
				,'bytes'			=>	($result['bytes'] + $bytes)
			)
		);
	}

	public static function deleteStats($file_id){
		return Db::_get()->run('DELETE FROM `file_stats` WHERE `file_id` = ?',array($file_id));
	}

	//-------------------------------------------------------
	//File URL Functions (Not node related)
	//-------------------------------------------------------
	public static function urlStatic($chksum){
		return self::serviceUrl('static').'/'.$chksum;
	}

	public static function urlStream($chksum,$client_id){
		return self::serviceUrl('stream').'/'.$chksum.'?client_id='.$client_id;
	}

	public static function urlDownload($chksum,$client_id){
		return self::serviceUrl('download').'/'.$chksum.'?client_id='.$client_id;
	}

	public static function urlLookup($chksum){
		return self::serviceUrl('lookup').'/'.$chksum;
	}
	
	public static function serviceUrl($action=null){
		return sprintf('%s%s%s%s'
			,self::httpScheme()
			,(is_string($action)) ? $action.'.' : ''
			,self::dnsZone()
			,self::httpPort()
		);
	}

	public static function httpScheme(){
		$rv = Config::getMerged('server','http.scheme');
		if(is_null($rv)) $rv = 'http://';
		return $rv;
	}

	public static function dnsZone(){
		return ltrim(Config::getMerged('server','dns_zone'),'.');
	}

	public static function httpPort(){
		if(Config::getMerged('server','http.port') == 80) return null;
		return ':'.Config::getMerged('server','http.port');
	}

}

