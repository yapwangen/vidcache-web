<?php
use \LSS\Url;
Url::_register('contact_us',Url::home().'?act=contact_us');
Url::_register('bug_report',Url::contact_us().'&do=bug_report');
Url::_register('premium_signup',Url::contact_us().'&do=premium_signup');
