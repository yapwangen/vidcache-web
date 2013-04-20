<?php
$config['site_name'] = 'Vidcache Web';
$config['log']['file'] = ROOT.'/log/vc-web';
$config['theme']['name'] = 'yourupload';
$config['contact']['email'] = 'contact@nullivex.com';

//embed mime types
$config['embed']['audio_types'] = array(
	 'audio/aac'
	,'audio/mp4'
	,'audio/mpeg'
	,'audio/ogg'
	,'audio/wav'
	,'audio/x-wav'
	,'audio/webm'
);
$config['embed']['video_types'] = array(
	 'video/mp4'
	,'video/ogg'
	,'video/webm'
	,'video/x-flv'
);
$config['embed']['static_types'] = array(
	 'image/jpg'
	,'image/jpeg'
	,'image/png'
	,'image/bmp'
	,'text/plain'
);
$config['embed']['types'] = array_merge($config['embed']['audio_types'],$config['embed']['video_types']);
