<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Log;
use \Vidcache\Admin\Todo;

//================================================
//Name:			Todo List
//Reference:	todo_list
//Position: 	09
//Purpose: 		Ticket TODO List
//Deps:			none
//================================================

//------------------------
//Module definition
//------------------------
$module = array(
	 'initCallback'		=>	'todoListInit'
	,'displayCallback'	=>	'todoListDisplay'
	,'postCallback'		=>	null
	,'shutdownCallback'	=>	null
	
);

//------------------------
//Module Methods
//------------------------

function todoListInit($account,$ticket,&$params){
	Url::_register(
		 'client_ticket_todo_list_complete'
		,Url::client_ticket_manage($account['account_id'],$ticket['ticket_id']).'&amp;todo_complete=true&amp;todo_id=$todo_id'
		,array('todo_id')
	);
	if(get('todo_complete')){
		Todo::complete(get('todo_id'));
		$todo = Todo::fetch(get('todo_id'));
		Log::add('ticket',$ticket['ticket_id'],'Todo #'.get('todo_id').' completed by '.StaffSession::get('name').' "'.$todo['message'].'"');
		alert('Todo item completed',true,true);
		redirect(Url::client_ticket_manage($account['account_id'],$ticket['ticket_id']));
	}
}

function todoListDisplay($account,$ticket,$_params){
	$params = array();
	$params['todo'] = array();
	$now = time();
	foreach(Todo::fetchAll('ticket',$ticket['ticket_id'],0,'ASC') as $todo){
		$todo['date'] = date('m/d/Y g:i:sA',$todo['date']);
		$todo['due'] = $todo['due'] > $now ? 'in '.future_age($todo['due']) : age($todo['due']).' ago';
		$todo['url_complete'] = Url::client_ticket_todo_list_complete($todo['todo_id']);
		$params['todo'][] = $todo;
	}
	return Tpl::_get()->output('client_ticket_todo_list',array_merge($_params,$params),Tpl::OUTPUT_RETURN);
}

