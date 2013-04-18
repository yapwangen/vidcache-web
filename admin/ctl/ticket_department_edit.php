<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\Ticket\Department;

if(post('edit')){
	try {
		Department::update(
			 post('ticket_department_id')
			,array(
				 'name'		=>	post('name')
				,'email'	=>	post('email')
				,'cc'		=>	post('cc')
				,'bcc'		=>	post('bcc')
			)
		);
		
		alert('Ticket Department updated successfully',true,true);
		redirect(Url::ticket_department());
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

if(post('delete')){
	if(post('confirm_delete'))
	{
		try {
			Department::delete(post('ticket_department_id'));
			alert('Ticket Department deleted successfully',true,true);
			redirect(Url::ticket_department());
		} catch (Exception $e){
			alert($e->getMessage(),false);
		}
	}
	else
	{
		alert('You did not check the delete box, failed to delete department.',false);
	}
}

$params = Department::fetch((get('ticket_department_id')));
$params = array_merge($params,get(),post());
$params['url_ticket_department_edit'] = $params['url_form'] = Url::ticket_department_edit(get('ticket_department_id'));
$params['edit'] = true;
$params['form_action'] = 'edit';
$params['button_label'] = 'Update';
Tpl::_get()->output('ticket_department_form',$params);
