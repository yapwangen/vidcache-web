<?php
use \LSS\Config;
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Staff;
use \Vidcache\Admin\Staff\Session as StaffSession;

StaffSession::requireManager();

$staff = array();
foreach(Staff::fetchAll() as $row){
	$params = $row;
	$params['last_login'] = date(Config::get('date','general_format'),$row['last_login']);
	$params['manager'] = $row['is_manager'] ? 'Yes' : 'No';
	$params['url_edit'] = Url::staff_edit($row['staff_id']);
	$staff[] = $params;
}

$params = array();
$params['staff'] = $staff;

Tpl::_get()->output('staff_list',$params);
