<?php
namespace Vidcache\Client\DataModel;
use \LSS\Url;
use \Vidcache\Client\FS;
use \Vidcache\Client\Session;

class Folder extends \LSS\DataModel {

	public function getCheckbox(){
		return '<input type="checkbox" name="folder[]" value="'.$this->getFolderId().'" />';
	}

	public function getIcon(){
		return '<i class="icon icon-folder-close">&#160;</i>';
	}

	public function getType(){
		return 'folder';
	}

	public function getSize(){
		return '--';
	}

<<<<<<< HEAD
	public function getHitsLifetime(){
		return $this->data['hits_lifetime'];
	}

	public function getBytesThisMonth(){
		return \format_bytes($this->data['bytes_this_month']);
=======
	public function getHits(){
		return '--';
	}

	public function getTransfer(){
		return '--';
>>>>>>> parent of 6873fb9... updates for initial release needs modified back to generic
	}

	public function getCreated(){
		return \age($this->data['created']).' ago';
	}

	public function getName(){
		$path = str_replace('/home/'.Session::get('client_id'),'',$this->getPath());
		return '<a href="'.Url::client_home_path($path).'">'.$this->data['name'].'</a>';
	}

	public function getActions(){
		return '
			<p class="text-right">
				<a href="#" title="Rename"><i class="icon-edit">&#160;</i></a>
				<a href="#" title="Delete"><i class="icon-remove">&#160;</i></a>
			</p>
		';
	}

}
