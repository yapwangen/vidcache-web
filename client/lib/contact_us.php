<?php
namespace Vidcache\Client;

use \LSS\Config;

abstract class ContactUs {

	public static function params(){
		return array(
			'email'			=>	'',
			'subject'		=>	'',
			'comments'		=>	'',
		);
	}

	public function submit($email,$subject,$comments){
		$subject = 'YourUpload Contact: '.$subject;
		//setup mail transport
		$transport = \Swift_SendmailTransport::newInstance();
		$transport->setLocalDomain('[127.0.0.1]');
		//setup mailer
		$mailer = \Swift_Mailer::newInstance($transport);
		//setup mail header
		$message = \Swift_Message::newInstance();
		$message->setSubject($subject);
		$message->setFrom($email);
		$message->setSender($email);
		$message->setReplyTo($email);
		$message->setTo(Config::get('contact','email'));
		$message->setBody(htmlentities($comments));
		return $mailer->send($message);
	}

}
