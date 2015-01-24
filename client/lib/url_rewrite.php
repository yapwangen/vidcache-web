<?php
namespace Vidcache\Client;

abstract class URLRewrite {

	public static function setup($uri){
		//if nothing unusual is there return blank
		if(is_null($uri)) return array();
		//strip the first part as our act
		$parts = explode('/',ltrim($uri,'/'));
		if(!is_array($parts) || !count($parts)) return array();
		switch(mda_get($parts,0)){

			//legacy YU urls
			case 'file':
			case 'embed':
			case 'embed_ext':
				$arr['act'] = 'client';
				$arr['do'] = 'legacy_uri';
				$arr['uri'] = $uri;
				break;	

			//new urls
			case 'watch':
			case 'listen':
			case 'view':
			case 'download':		
				$arr['act'] = 'client';
				$arr['do'] = 'file_view';
				$arr['fire'] = mda_get($parts,0);
				$arr['handle'] = mda_get($parts,1);
				break;
			default:
				return array();
				break;
		}
		return $arr;
	}

}
