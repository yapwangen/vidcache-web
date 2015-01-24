<?php
use \LSS\Tpl;
use \Vidcache\Admin\News;

$params['news'] = News::fetchAll();
foreach($params['news'] as &$row)
	$row['content'] = nl2br($row['content']);
Tpl::_get()->output('news',$params);
