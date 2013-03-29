<?php
use \LSS\Url;
use \Vidcache\Admin\Session as ClientSession;

ClientSession::tokenDestroy(ClientSession::getTokenFromSession());
ClientSession::destroySession();
redirect(Url::login());
