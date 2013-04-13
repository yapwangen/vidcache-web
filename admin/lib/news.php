<?php
namespace Vidcache\Admin;

use \LSS\Db;

abstract class News {

	public static function fetch($news_id){
		return Db::_get()->fetch(
			'SELECT * FROM `news` WHERE `news_id` = ?'
			,array($news_id)
		);
	}

	public static function fetchAll(){
		return Db::_get()->fetchAll('SELECT * FROM `news` ORDER BY `posted` DESC');
	}

	public static function fetchAllHome(){
		return Db::_get()->fetchAll(
			'SELECT * FROM `news` WHERE `is_active` = ?'
			.' ORDER BY `posted` DESC'
			.' LIMIT 5'
			,array(1)
		);
	}

	public static function createParams(){
		return array(
			'title'		=>	'',
			'content'	=>	''
		);
	}

	public static function create($data){
		$data['posted'] = time();
		return Db::_get()->insert('news',$data);
	}
	
	public static function update($news_id,$data){
		return Db::_get()->update('news','news_id',$news_id,$data);
	}

	public function delete($news_id){
		return Db::_get()->run('DELETE FROM `news` WHERE `news_id` = ?',array($news_id));
	}

}
