<?php
namespace Vidcache\Client;

abstract class URLRewrite {

	public static function setup($uri){
		//if nothing unusual is there return blank
		if(is_null($uri)) return array();
		//strip the first part as our act
		$parts = explode('/',ltrim($uri,'/'));
		if(!is_array($parts) || !count($parts)) return array();
		$arr['act'] = 'client';
		$arr['do'] = 'file_view';
		$arr['fire'] = array_shift($parts);
		switch($arr['fire']){
			case 'watch':
			case 'listen':
			case 'view':
			case 'download':
				$arr['handle'] = array_shift($parts);
				break;
			default:
				return array();
				break;
		}
		return $arr;
	}

}
