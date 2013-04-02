<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Staff;
use \Vidcache\Admin\Staff\Session as StaffSession;

if(post('edit')){
	try {
		Staff::update(post('staff_id'),post());
		alert('staff profile updated successfully',true,true);
		redirect(Url::profile());
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = array_merge(Staff::fetch(StaffSession::get('staff_id')),post());
Tpl::_get()->output('staff_profile',$params);
