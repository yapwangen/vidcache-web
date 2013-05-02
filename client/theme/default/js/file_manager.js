/* Manager Init */
$(document).ready(function() {
	$('#options').hide();
});

/* Table initialisation */
$(document).ready(function() {

	$('#file-manager').dataTable( {
		 "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>"
		,"sPaginationType": "bootstrap"
		,"oLanguage": {
			"sLengthMenu": "_MENU_ records per page"
		}
		,"aoColumns": [
			 {"bSortable": false}
			,{"bSortable": false}
			,null
			,null
			,null
			,null
			,null
			,null
			,{"bSortable": false}
		]
	} );
} );

/* Option Handling */
var option_control = '#file-manager > thead > tr > th > input[type=checkbox]';
var option_elements = '#file-manager > tbody > tr > td > input[type=checkbox]';

var showOptions = function(){
	if($(option_elements+':checked').length > 0){
		$('#options').slideDown();
	} else {
		$('#options').slideUp();
	}
}

/* Only show options when stuff is selected */
$(document).ready(function(){
	//single checking
	$(option_elements).click(showOptions);
	//mass checking
	$(option_control).click(function(){
		if($(option_control).prop('checked')){
			$(option_elements).prop('checked',true);
		} else {
			$(option_elements).prop('checked',false);
		}
		showOptions();
	});
});


/* Configure Dropzone */
$(document).ready(function(){
	Dropzone.options.fileUpload = {
		 maxFilesize: 4096 //4gb
		,parallelUploads: 8
	}
});
