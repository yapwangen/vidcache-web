<?php
use \LSS\Config;
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Staff;
use \Vidcache\Admin\Staff\Session;

if(post('login')){
	try {
		//get the staff member
		$staff = Staff::fetchByEmail(post('email'));
		if(!$staff) throw new Exception('Staff member doesnt exist');
		//check password
		if(!bcrypt_check(post('password'),$staff['password']))
			throw new Exception('Password is invalid');
		//generate token and setup session
		$token = Session::tokenCreate($staff['staff_id'],server('REMOTE_ADDR'),server('HTTP_USER_AGENT'));
		Session::startSession($token);
		//update last login
		Staff::updateLastLogin($staff['staff_id']);
		//redirect request
		if(session('login_referrer') && strpos(session('login_referrer'),Url::login()) === false)
			redirect(session('login_referrer'));
		else redirect(Url::home());
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

session('login_referrer',server('HTTP_REFERER'));

$params = array();
$params['url_login'] = Url::login();
$params['page_title'] = Config::get('site_name').' - Admin Login';
Tpl::_get()->output('staff_login',$params);
