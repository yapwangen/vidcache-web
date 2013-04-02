<?php
use \LSS\Url;

//file management
Url::_register('client_file_list',Url::client().'&do=file_list&client_id=$1');
Url::_register('client_file_list_by_folder',Url::client().'&do=file_list&client_id=$1&client_folder_id=$2');
Url::_register('client_file_manage',Url::client().'&do=file_manage&client_id=$1&client_file_id=$2');
Url::_register('client_folder_manage',Url::client().'&do=folder_manage&client_id=$1&client_folder_id=$2');