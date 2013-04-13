window.addEvent('domready', function(){

	var upload = new Form.Upload('file', {
		dropMsg: "Drop files here",
		onComplete: function(response){
			if(response.match(/http/)){
				//alert('Files have been uploaded!');
				window.location.href = response;
			} else alert(response);
		}
	});

	// using iFrameFormRequest from the forge to upload the files
	// in an IFrame.
	if (!upload.isModern()) {
		new iFrameFormRequest('myForm', {
			onComplete: function(response){
				alert('Completed uploading the files');
			}
		});
	}

});