<?php
use \LSS\Url;
use \Vidcache\Client\Session;

Session::tokenDestroy(Session::getTokenFromSession());
Session::destroySession();
redirect(Url::login());
