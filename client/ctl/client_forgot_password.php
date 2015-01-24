<?php
use \LSS\Config;
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Client;
use \Vidcache\Admin\Client\Contact;
use \Vidcache\Client\Session;

if(Session::isLoggedIn())
	redirect(Url::client_home());

if(post('forgot_password')){
	try {
		//get the client member
		$client = Client::fetchByEmail(post('email'));
		if(!$client) throw new Exception('Client doesnt exist');
		//reset the password
		$temp_pass = gen_handle(8);
		Client::update($client['client_id'],array('password'=>$temp_pass));
		Contact::update($client['primary_contact_id'],array('password'=>$temp_pass));
		//email the new password
		mail(
			 $client['email']
			,'YourUpload Account Password has been reset'
			,   "Hello,\r\n\r\n"
				."Your account has had its password reset.\r\n\r\n"
				."*** In order to login use the following temporary password. ***\r\n"
				."Your new password is: ".$temp_pass."\r\n\r\n"
				."Thank You,\r\n"
				."YourUpload Team"
			,    "Reply-To: noreply@yourupload.com\r\n"
				."From: support@yourupload.com\r\n"
		);
		//alert and redirect
		alert('A new temporary password has been emailed to you',true,true);
		redirect(Url::login());
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = array();
Tpl::_get()->output('client_forgot_password',$params);
