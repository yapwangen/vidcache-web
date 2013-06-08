<?php
use \LSS\Tpl;
use \Vidcache\Admin\Page;

$params = Page::fetchByURLName(get('page'));
if(!$params)
	throw new Exception('Page not found');
Tpl::_get()->output('page',$params);
