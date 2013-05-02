<?php
use \LSS\Config;
use \LSS\Tpl;

//load tpl
$theme = (Config::get('theme','name') ? Config::get('theme','name') : 'default');
Tpl::_get()->setPath(ROOT_GROUP.'/theme/'.$theme);
Tpl::_get()->setUri('/'.basename(ROOT_GROUP).'/theme/'.$theme.'/');
Tpl::_get()->set(array(
	 'lss_version'		=>	LSS_VERSION
	,'version'			=>	VERSION
	,'site_name'		=>	Config::get('site_name')
	,'site_title'		=>	Config::get('site_name')
	,'uri'				=>	Tpl::_get()->uri
	,'url'				=>	Config::get('url','url')
	,'theme_path'		=>	Tpl::_get()->uri
	,'copyright'		=>	'© '.date('Y').' '.Config::get('site_name')
	,'meta_keywords'	=>	Config::get('meta_keywords')
	,'meta_description'	=>	Config::get('meta_description')
	//routing stuff for selective nav handling
	,'act'				=>	get('act')
	,'do'				=>	get('do')
	,'fire'				=>	get('fire')
));

//set delayed alerts
if(session('delayed_alert')){
	$alert = Tpl::_get()->get('alert');
	if(!is_array($alert)) $alert = array();
	$alert = array_merge($alert,session('delayed_alert'));
	Tpl::_get()->set('alert',$alert);
	session('delayed_alert','');
}

//cleanup
unset($theme);
