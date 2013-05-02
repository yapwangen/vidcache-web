<?php
use \LSS\Tpl;
use \LSS\Url;
use \LSS\Validate;
use \Vidcache\Client\ContactUs;

//setup params for different forms
$overrides = array();
switch(get('do')){
	case 'bug_report':
		$url = Url::bug_report();
		$overrides['subject'] = $title = 'Bug report';
		break;
	case 'premium_signup':
		$url = Url::premium_signup();
		$overrides['subject'] = $title = 'Premium Sign Up';
		break;
	default:
		$url = Url::contact_us();
		$title = 'Contact Us';
		break;
}

if(post('contact')){
	try {
		Validate::prime(post());
		Validate::go('email')->not('blank')->is('email');
		Validate::go('subject')->not('blank');
		Validate::go('comments')->not('blank');
		Validate::paint();
		ContactUs::submit(post('email'),post('subject'),post('comments'));
		alert('Your message has been sent',true,true);
		redirect($url);
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = array_merge(ContactUs::params(),$overrides,post());
$params['title'] = $title;
$params['url_action'] = $url;

Tpl::_get()->output('contact_us',$params);
