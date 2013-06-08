<?php
use \Vidcache\Client\FS;

$parts = explode('/',ltrim(get('uri'),'/'));

try {
	//file url
	if(mda_get($parts,0) == 'file'){
		$hash = mda_get($parts,1);
		$file_map = FS::fetchFileMapByHash($hash);
		$file = FS::fetchFileByPath($file_map['path']);
		$handle = $file_map['embed_handle'] ? $file_map['embed_handle'] : $file_map['file_handle'];
		//redirect to the proper url
		header('Location: /'.FS::actionType($file['mime_type']).'/'.$handle);
		exit;
	}

	//embed url
	if(mda_get($parts,0) == 'embed' || mda_get($parts,0) == 'embed_ext'){
		if(mda_get($parts,0) == 'embed_ext'){
			$source = mda_get($parts,1);
			$id = mda_get($parts,2);
			$file_map = FS::fetchFileMapBySource($source,$id);
		} else {
			$hash = mda_get($parts,1);
			$file_map = Fs::fetchFileMapByHash($hash);
		}
		$file = FS::fetchFileByPath($file_map['path']);
		if(!$file_map['embed_handle'])
			throw new Exception('This file is not valid for embedding');
		//redirect to cluster URL
		header('Location: '.FS::buildClusterURL($file['file_id'],$file_map['embed_handle'],'embed'));
		exit;
	}
	throw new Exception('Invalid URL');
} catch(Exception $e){
	$msg = 'Could not redirect legacy request: '."http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]".' reason: '.$e->getMessage();
	dolog($msg,LOG_ERROR);
}

