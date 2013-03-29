<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Staff;
use \Vidcache\Admin\Staff\Session as StaffSession;

StaffSession::requireManager();

if(post('edit')){
	try {
		Staff::update(post('staff_id'),post());
		alert('Staff member updated successfully',true,true);
		redirect(Url::staff());
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

if(post('delete')){
	try {
		Staff::deactivate(post('staff_id'));
		alert('Staff member deleted successfully',true,true);
		redirect(Url::staff());
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = Staff::fetch(req('staff_id'));
$params = array_merge($params,post());
$params['edit'] = true;
$params['form_action'] = 'edit';
$params['button_label'] = 'Edit';
$params['is_manager'] = $params['is_manager'] ? true : false;
$params['url_staff_edit'] = $params['url_form'] = Url::staff_edit(get('staff_id'));

Tpl::_get()->output('staff_form',$params);
