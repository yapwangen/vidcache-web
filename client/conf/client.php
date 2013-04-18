<?php
$config['client']['token_life'] = 31536000;
$config['client']['cookie_prefix'] = 'vc_web';
$config['client']['cookie_life'] = 31536000;
$config['client']['cookie_domain'] = substr($_SERVER['HTTP_HOST'],0,strpos($_SERVER['HTTP_HOST'],':'));
$config['client']['cookie_path'] = '/';