<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Ticket\Department;

$ticket_department = array();
foreach(Department::fetchAll() as $row){
	$params = $row;
	$params['url_ticket_department_edit'] = Url::ticket_department_edit($row['ticket_department_id']);
	$ticket_department[] = $params;
}

Tpl::_get()->output('ticket_department_list',array('ticket_department'=>$ticket_department));
