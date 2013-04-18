<?php
namespace Vidcache\Admin;

use \LSS\Db;
use \LSS\Validate;

abstract class Page {

	public static function fetchAll(){
		return Db::_get()->fetchAll('SELECT * FROM `pages`');
	}

	public static function fetch($page_id){
		return Db::_get()->fetch(
			 'SELECT * FROM `pages` WHERE `page_id` = ?'
			,array($page_id)
		);
	}

	public static function fetchByURLName($url_name){
		return Db::_get()->fetch(
			 'SELECT * FROM `pages` WHERE `urlname` = ?'
			,array($url_name)
		);
	}

	public static function validate($data){
		Validate::prime($data);
		Validate::go('name')->not('blank');
		Validate::paint();
	}

	public static function create($data){
		$data['urlname'] = urlname(mda_get($data,'name'));
		$data['content'] = html_entity_decode(mda_get($data,'content'));
	}

	public static function update($page_id,$data){
		if(!isset($data['urlname']))
			$data['urlname'] = urlname(mda_get($data,'name'));
		$data['content'] = html_entity_decode(mda_get($data,'content'));
		return Db::_get()->update('pages','page_id',$page_id,$data);
	}

	public static function delete($page_id){
		return Db::_get()->run('DELETE FROM `pages` WHERE `page_id` = ?',array($page_id));
	}

}
