<?php
use \LSS\Config;
use \LSS\Url;
use \Vidcache\Client\Session;

Session::tokenDestroy(Session::getTokenFromSession());
Session::destroySession();
//unset any cookie if there was one
Session::destroyCookie();
//redirect
redirect(Url::login());
