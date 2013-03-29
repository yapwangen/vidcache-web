<?php
namespace Vidcache\Admin\Client;

use \Exception;
use \LSS\Crypt;
use \LSS\Db;

abstract class API {

	public static function fetch($client_id){
		return Db::_get()->fetch(
			 'SELECT * FROM `client_api_keys` WHERE `client_id` = ?'
			,array($client_id)
		);
	}

	public static function fetchKeysByAPIKey($api_key){
		return Db::_get()->fetch(
			 'SELECT * FROM `client_api_keys` WHERE `api_key` = ?'
			,array($api_key)
		);
	}

	public static function fetchKeysByClient($client_id){
		return Db::_get()->fetch(
			 'SELECT * FROM `client_api_keys` WHERE `client_id` = ?'
			,array($client_id)
		);
	}

	public static function createParams(){
		return array(
			 'client_id'		=>	''
			,'api_key'			=>	''
			,'crypt_key'		=>	''
			,'crypt_iv'			=>	''
		);
	}

	public static function generate($client_id){
		//delete old keys
		$rv = Db::_get()->run(
			 'DELETE FROM `client_api_keys` WHERE `client_id` = ?'
			,array($client_id)
		);
		if(!$rv)
			throw new Exception('Failed to clear old keys');
		$api_key = Crypt::keyCreate();
		$crypt_key = Crypt::keyCreate();
		$crypt_iv = Crypt::IVCreate();
		return Db::_get()->insert('client_api_keys',array(
			 'client_id'		=>	$client_id
			,'api_key'			=>	$api_key
			,'crypt_key'		=>	$crypt_key
			,'crypt_iv'			=>	$crypt_iv
		));
	}

	public static function fetchAllSessionsByClient($client_id,$expired=false){
		return Db::_get()->fetchAll(
			 'SELECT * FROM `client_api_sessions` WHERE `client_id` = ? AND `is_expired` = ?'
			,array($client_id,($expired ? 1 : 0))
		);
	}

	public static function fetchSessionByToken($token){
		return Db::_get()->fetch(
			 'SELECT * FROM `client_api_sessions` WHERE `token` = ?'
			,array($token)
		);
	}

	public static function fetchSessionByHost($host){
		return Db::_get()->fetch(
			 'SELECT * FROM `client_api_sessions` WHERE `remote_addr` = ?'
			,array($host)
		);
	}

	public static function incSession($token){
		return Db::_get()->run(
			 'UPDATE `client_api_sessions` SET'
			.' `request_count` = `request_count` + 1, `last_request` = ?'
			.' WHERE `token` = ?'
			,array(time(),$token)
		);
	}

	public static function createSession($api_key){
		$host = server('REMOTE_ADDR');
		//lookup client by api_key
		$keys = self::fetchKeysByAPIKey($api_key);
		if(!$keys)
			throw new Exception('API key is invalid');
		//check if we already have a token
		$rv = self::fetchSessionByHost($host);
		if($rv !== false){
			//inc hits
			self::incSession($rv['token']);
			//return token
			return $rv['token'];
		}
		//make token
		do {
			$token = sha1(microtime(true).mt_rand(0,10000));
		}while(self::fetchSessionByToken($token) !== false);
		//store session
		$time = time();
		$rv = Db::_get()->insert('client_api_sessions',array(
			 'client_id'		=>	$keys['client_id']
			,'remote_addr'		=>	$host
			,'token'			=>	$token
			,'crypt_key'		=>	$keys['crypt_key']
			,'crypt_iv'			=>	$keys['crypt_iv']
			,'created'			=>	$time
			,'expires'			=>	$time + Config::get('api','key_life')
		));
		if($rv === false)
			throw new Exception('Failed to store session');
		//return token
		return $token;
	}

}
