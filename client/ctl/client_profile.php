<?php
use \LSS\Tpl;
use \LSS\Url;
use \LSS\Validate;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Client\Contact;
use \Vidcache\Client\Session;

//bounce to login page if not logged in
if(!Session::isLoggedIn())
	redirect(Url::login());

$client_id = Session::get('client_id');

if(post('update')){
	try {
		Validate::prime(post());
		Validate::go('email')->not('blank')->is('email');
		Validate::go('current_password')->not('blank')->min(6)->max(128);
		Validate::paint();
		//validate current password
		$client = Client::fetch($client_id);
		if(!bcrypt_check(post('current_password'),$client['password']))
			throw new Exception('Password is invalid, cannot update');
		//validate new passwords if we have em
		if(post('new_password') != '' && post('new_password') != post('new_confirm_password'))
			throw new Exception('New passwords do not match!');
		
		//update email if it changed
		if($client['email'] != post('email')){
			//make sure the email doesnt already exist
			if(Client::fetchByEmail(post('email')) !== false)
				throw new Exception('Modified email address already exists in system');
			Contact::update($client['primary_contact_id'],array('email'=>post('email')));
		}
		//update the password if needed
		if(post('new_password') != ''){
			Client::update($client_id,array('password'=>post('new_password')));
			Contact::update($client['primary_contact_id'],array('password'=>post('new_password')));
		}
		alert('Profile updated successfully!',true,true);
		redirect(Url::client_home());
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = Client::fetch($client_id);
Tpl::_get()->output('client_profile',$params);
