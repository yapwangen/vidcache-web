<?php
use \LSS\Url;
use \Vidcache\Admin\Staff\Session as StaffSession;

StaffSession::tokenDestroy(StaffSession::getTokenFromSession());
StaffSession::destroySession();
redirect(Url::login());
