<?php
//admin portal
$config['admin']['http']['port'] = 8082; //port used to access the Admin interface
$config['admin']['http']['scheme'] = 'http://';
$config['admin']['db']['database'] = 'vidcache_admin'.((strlen($config['tag'])>0)?'_'.$config['tag']:'');
$config['admin']['lockfile'] = '/dev/shm/vc-admin'.((strlen($config['tag'])>0)?'-'.$config['tag']:'').'.lck'; //cronjob lockfile
$config['pdns']['db']['host'] = 'localhost';
$config['pdns']['db']['user'] = 'pdns';
$config['pdns']['db']['password'] = '';
$config['log']['file'] = ROOT.'/log/vc-admin';

//client portal
$config['client']['http']['port'] = 8083; //port used to access the Client interface

//api info
$config['api']['http']['port'] = 8084; //port used to access the API
$config['api']['admin_url'] = '';

//folders
$config['folder']['max_file_count'] = 1024;

//files
$config['file']['staging_url_prefix'] = 'http://admin.'.$config['dns_zone'].':'.$config['admin']['http']['port'].'/staging'; //staging prefix
