<?php
namespace Vidcache\Admin;
use \LSS\Account;
use \LSS\AccountInterface;
use \LSS\Url;

abstract class Client extends Account implements AccountInterface {

	static $accounts_table = 'clients';
	static $account_key = 'client_id';

	public static function adminHeaderParams($client_id,$company){
		return array(
			 'client_id'				=> $client_id
			,'client_company'			=> $company
			,'url_client_file_list'		=> Url::client_file_list($client_id)
			,'url_client_ticket_create'	=> Url::client_ticket_create($client_id)
			,'url_client_ticket_list'	=> Url::client_ticket_list($client_id)
			,'url_client_manage'		=> Url::client_manage($client_id)
			,'url_client_edit'			=> Url::client_edit($client_id)
		);
	}

	public static function createParams(){
		return self::_createParams(array(
			 'is_active'	=> 1
			)
		);
	}

	public static function create($data){
		return self::_create($data
			,array('contact_is_active' => 1)
			,array(
				  'is_active'		=> 1
			 )
		);
	}

	public static function fetchAll(){
		return self::_fetchAll(array(
			  static::$accounts_table.'.is_active'		=> 1
			 )
		);
	}

	public static function fetch($client_id){
		return self::_fetch(array(
			 static::$accounts_table.'.client_id'		=> $client_id
			)
		);
	}

	public static function fetchByContact($contact_id){
		return self::_fetch(array(
			 static::$contacts_table.'.contact_id'		=> $contact_id
			)
		);
	}

	public static function fetchByEmail($email,$except=false){
		return self::_fetchByEmail(array(
			 static::$contacts_table.'.email'			=> $email
			)
			,$except
		);
	}

	public static function register($data){
		return self::create($data);
	}

}
