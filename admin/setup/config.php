<?php
//THIS MUST BE SET, either blank string (sane default), or set tag to suffix this installs files (dev/vhost/multiple install per server mode)
$config['tag'] = '';

//general settings
$config['dns_zone'] = 'localhost'; //DNS Zone of cluster
$config['site_name'] = 'Vidcache Management'; //title of cluster
#$config['log']['file'] = $config['paths']['lss'].'/log/vc-admin'; //log file
//database configuration
$config['db']['user'] = 'vidcache';
$config['db']['password'] = 'vidcache';

//auth and crypt settings (make sure these match across all cluster servers/portals
$config['xport']['auth_key'] = ''; //must be some string (use bin/crypt_keygen)
$config['crypt']['key'] = ''; //must be some string (use bin/crypt_keygen)
$config['crypt']['iv'] = ''; //must be some base64 encoded string (use bin/crypt_ivgen)

$config['server']['http']['port'] = 80; //http port of cluster servers (usually 80) NOT THE PORT OF THE ADMIN PORTAL
$config['server']['timeout'] = 180; //timeout in seconds, any node that hasn't registered within this window will be considered down

$config['stream']['http']['port'] = 8081; //port used for streaming
$config['stream']['local_proxy'] = 'http://127.0.0.1:'.$config['stream']['http']['port'];

$config['admin']['http']['port'] = 8082; //port used to access the Admin interface
$config['admin']['db']['database'] = 'vidcache_admin'.((strlen($config['tag'])>0)?'_'.$config['tag']:'');
$config['admin']['lockfile'] = '/dev/shm/vc-admin'.((strlen($config['tag'])>0)?'-'.$config['tag']:'').'.lck'; //cronjob lockfile
$config['file']['staging_url_prefix'] = 'http://admin.'.$config['dns_zone'].':'.$config['admin']['http']['port'].'/staging'; //staging prefix

$config['client']['http']['port'] = 8083; //port used to access the Client interface

$config['api']['http']['port'] = 8084; //port used to access the API
