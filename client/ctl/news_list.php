<?php
use \LSS\Tpl;
use \Vidcache\Admin\News;

$params['news'] = News::fetchAll();
Tpl::_get()->output('news',$params);
