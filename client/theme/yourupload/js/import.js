window.addEvent('domready',function(){
	$('import-form').addEvent('submit',importSubmit);
	$('import-progress').slide('hide').show();
	$('upload-file').addEvent('focus',function(e){e.target.set('value','');});
});

var importSubmit = function(e){
	//create the file handle
	$('import-options').slide('out');
	$('import-progress').slide('in');
	$('progress-upload-complete').slide('hide');
	setTimeout(importCheck,'3000');
}

var importCheck = function(){
	var hash = $('import-hash').get('value');
	new Request.XML({
			noCache: true,
			method: 'get',
			url: '/ajax.php',
			data: {'act':'file_progress','hash':hash},
			onSuccess: function(xml,text){
				var el = xml;
				if((el.getElement('message') && el.getElement('message').get('text') == 'nofile') || text == 'nofile'){
					importComplete();
					return true;
				}
				complete_pct = parseInt(el.getElement('complete_pct').get('text'));
				$('progress-filename').set('text',unescape(el.getElement('filename').get('text')));
				$('progress-complete-bar').setStyle('width',complete_pct+'%');
				$('progress-bytes-uploaded').set('text',el.getElement('bytes_uploaded').get('text'));
				$('progress-bytes-total').set('text',el.getElement('bytes_total').get('text'));
				$('progress-rate').set('text',el.getElement('speed_avg').get('text'));
				$('progress-time-remaining').set('text',el.getElement('time_remaining').get('text'));
				setTimeout(importCheck,'500');
			}
	}).send();
}

var importComplete = function(){
	$('progress-complete-bar').setStyle('width','99%');
	$('progress-bytes-uploaded').set('text',$('progress-bytes-total').get('text'));
	$('progress-time-remaining').set('text','import complete');
	$('progress-upload-complete').slide('in');
	var hash = $('import-hash').get('value');
	new Request.XML({
			noCache: true,
			method: 'get',
			url: '/ajax.php',
			data: {'act':'file_info','hash':hash},
			onSuccess: function(xml){
				var el = xml;
			}
	}).send();
}