<?php
use \LSS\Tpl;
use \LSS\Url;
use \Vidcache\Admin\File;
use \Vidcache\Admin\Node;


//---------------------------------------------------------
//Delete Files or Folders (Even recursively)
//---------------------------------------------------------
if(post('action') == 'delete'){
	try {
		if(!post('confirm_delete'))
			throw new Exception('Deletion not confirmed');
		if(is_array(post('file'))){
			foreach(post('file') as $file_id)
				File::delete($file_id);
		}
		alert('Deletion successful',true,true);
		redirect(Url::files());
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

//files
$start = 0;
$limit = 25;
$params = array();
$params['files'] = File::fetchAllWithStats($start,$limit);
$params['url_current'] = Url::file_list($start,$limit);

Tpl::_get()->output('file_list',$params);
