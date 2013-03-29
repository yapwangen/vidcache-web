<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Staff;
use \Vidcache\Admin\Staff\Session as StaffSession;

StaffSession::requireManager();

if(post('create')){
	try {
		$params = post();
		unset($params['create'],$params['confirm_password']);
		Staff::create($params);
		alert('Staff member created successfully',true,true);
		redirect(Url::staff());
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = Staff::createParams();
$params = array_merge($params,post());
$params['create'] = true;
$params['form_action'] = 'create';
$params['button_label'] = 'Create';
$params['url_form'] = Url::staff_create();
$params['is_manager'] = post('is_manager') ? true : false;

Tpl::_get()->output('staff_form',$params);
