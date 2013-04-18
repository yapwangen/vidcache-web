<?php
use \LSS\Url;

//file management
Url::_register('client_file_list',Url::client().'&do=file_list');
Url::_register('client_file_list_by_folder',Url::client().'&do=file_list&client_folder_id=$1');
Url::_register('client_file_manage',Url::client().'&do=file_manage&client_file_id=$1');
Url::_register('client_folder_manage',Url::client().'&do=folder_manage&client_folder_id=$1');

//public file management
Url::_register('file_upload_public',Url::client().'&do=file_upload_public');