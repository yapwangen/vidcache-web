<?php
namespace Vidcache\Admin;

use \LSS\Url;

class Search {

	public static function find($keywords){
		$result = $url = null;
		//try app matches
		switch(strtolower($keywords)){
			case 'catalog':
				return Url::catalog();
				break;
			case 'catalog brands':
				return Url::catalog_brands();
				break;
			case 'catalog types':
				return Url::catalog_types();
				break;
			case 'catalog categories':
				return Url::catalog_categories();
				break;
			case 'staff':
				return Url::staff();
				break;
			case 'pages':
				return Url::pages();
				break;
			case 'clients':
				return Url::client();
				break;
			default:
				break;
		}
		//try specific searches
		switch(strtolower(substr($keywords,0,1))){
			case 't':
				$result = self::findTicket(self::intval($keywords));
				if(!is_null($result['client_id']))
					return Url::client_ticket_manage($result['client_id'],$result['ticket_id']);
				elseif(!is_null($result['vendor_id']))
					return Url::vendor_ticket_manage($result['vendor_id'],$result['ticket_id']);
				else
					continue;
				break;
			case 'c':
				$result = self::findClient(self::intval($keywords));
				return Url::client_manage($result);
				break;
			// case 'o':
				// $result = self::findOrder(self::intval($keywords));
				// $url = Url::order_manage($result);
				// break;
			default:
				break;
		}
		alert('Could not find any matching results',false,true);
		return Url::home();
	}
	
	public static function intval($keywords){
		return preg_replace('/[^0-9]+/','',$keywords);
	}
	
	public static function findTicket($val){
		return Ticket::fetch($val);
	}
	
	public static function findClient($val){
		$rv = Client::fetch($val);
		return $rv['account_id'];
	}
	
	public static function findOrder($val){
		$rv = Order::fetch($val);
		return $rv['order_id'];
	}
	
}
