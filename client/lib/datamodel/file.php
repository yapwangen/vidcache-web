<?php
namespace Vidcache\Client\DataModel;
use \LSS\Url;
use \Vidcache\Client\FS;

class File extends \LSS\DataModel {

	public function getCheckbox(){
		return '<input type="checkbox" name="file[]" value="'.$this->getFileId().'" />';
	}

	public function getIcon(){
		return '<i class="icon icon-file">&#160;</i>';
	}

	public function getName(){
		if(!empty($this->data['embed_handle'])) $url_handle = $this->data['embed_handle'];
		else $url_handle = $this->data['file_handle'];
		$url = Url::client_file_view(FS::actionType($this->data['mime_type']),$url_handle);
		return '<a href="'.$url.'">'.$this->data['name'].'</a>';
	}

	public function getType(){
		return $this->getMimeType();
	}

	public function getSize(){
		return \format_bytes($this->data['size']);
	}

	public function getHits(){
		return $this->data['hits_this_month'];
	}

	public function getBytesThisMonth(){
		return \format_bytes($this->data['bytes_this_month']);
	}

	public function getCreated(){
		return \age($this->data['created']).' ago';
	}

}
