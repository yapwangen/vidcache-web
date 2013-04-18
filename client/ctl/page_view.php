<?php
use \LSS\Tpl;
use \Vidcache\Admin\Page;

$params = Page::fetchByURLName(get('page'));
Tpl::_get()->output('page',$params);
