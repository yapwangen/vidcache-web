<?php
use \LSS\Config;
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\SDK;

//setup the SDK
$vc = SDK::load();
$vc->connect(Config::get('vidcache','api_key'));

//get args
$handle = get('handle');

$template = get('template') ? get('template') : Config::get('vidcache','embed_tpl_handle');
$url = get('url') == "false" ? false : true;
$redirect = get('redirect') == "false" ? false : true;

//call to the sdk for the republish
$new = $vc->pathRepublish($handle,$template);
$new = $new['embed_handle'];
$embed_url = 'http://embed'.Config::get('vidcache','dns_zone').'/'.$new;

if($redirect){
	header("Location: ".$embed_url);
	exit;
} else if($url){
	echo $embed_url;
	exit;
} else {
	echo $new;
}
