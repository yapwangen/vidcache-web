<?php
use \LSS\Url;

Url::_register('ticket_department',Url::home().'?act=ticket_department');
Url::_register('ticket_department_edit',Url::ticket_department().'&do=edit&ticket_department_id=$1');
Url::_register('ticket_department_create',Url::ticket_department().'&do=create');
