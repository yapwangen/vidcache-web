<?php

Url::_register('client',Url::home().'?act=client');
Url::_register('signup',Url::client().'&do=signup');
Url::_register('login',Url::client().'&do=login');
Url::_register('logout',Url::client().'&do=logout');
Url::_register('profile',Url::home().'?act=profile');
