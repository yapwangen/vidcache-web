<?php
namespace Vidcache\Admin;

abstract class Publish {

	public static function fetchAll(){
		return Db::_get()->fetchAll('SELECT * FROM publish');
	}

	public static function createParams(){
		return array(
			 'chksum'	=> ''
			,'content'	=> ''
		);
	}

	public static function fetch($chksum){
		return Db::_get()->fetch(
			'SELECT * FROM publish WHERE chksum = ?'
			,array($chksum)
			,'Published content could not be found: '.$chksum
		);
	}

	public static function fetchById($publish_id){
		return Db::_get()->fetch(
			'SELECT * FROM publish WHERE publish_id = ?'
			,array($publish_id)
			,'Published content could not be found: '.$publish_id
		);
	}

	public static function fetchByUrlname($urlname){
		return Db::_get()->fetch(
			'SELECT * FROM publish WHERE urlname = ?'
			,array($urlname)
			,'Published content could not be found: '.$urlname
		);
	}

	public static function validate($data){
		Validate::prime($data);
		Validate::go('chksum')->not('blank');
		Validate::paint();
	}

	public static function create($data){
		return Db::_get()->insert(
			'publish'
			,array(
				 'chksum'	=> mda_get($data,'chksum')
				,'urlname'	=> urlname(mda_get($data,'chksum'))
				,'content'	=> html_entity_decode(mda_get($data,'content'))
			)
		);
	}

	public function update($publish_id,$data){
		return Db::_get()->update(
			 'publish'
			,'publish_id'
			,$publish_id
			,array(
				 'chksum'	=> mda_get($data,'chksum')
				,'urlname'	=> urlname(mda_get($data,'chksum'))
				,'content'	=> html_entity_decode(mda_get($data,'content'))
			)
		);
	}

	public function delete($publish_id){
		return Db::_get()->run('DELETE FROM publish WHERE publish_id = ?',array($publish_id));
	}

}
