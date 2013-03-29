<?php
use \LSS\Url;

Url::_register('files',Url::home().'?act=file');
Url::_register('file_list',Url::files().'&do=list&start=$1&limit=$2');
