<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Ticket\Department;

if(post('create')){
	try {
		Department::create(array(
			 "name"		=>	post('name')
			,"email"	=>	post('email')
			,"cc"		=>	post('cc')
			,"bcc"		=>	post('bcc')
		));
		alert('Ticket Department created successfully',true,true);
		redirect(Url::ticket_department());
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = array_merge(Department::createParams(),post());
$params['create'] = true;
$params['form_action'] = 'create';
$params['url_form'] = Url::ticket_department_create();
$params['button_label'] = 'Create';
Tpl::_get()->output('ticket_department_form',$params);
