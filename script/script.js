jQuery(document).ready(function($) {
	
	if(window.location.href.indexOf("forms_to_pdf")!==-1)
	
	{	
		document.getElementById("bulk-export").style.display = "block";
		document.getElementById("data-filter").style.display = "block";
		document.getElementById("display_setup").style.display = "block";
	
		
			//Set date input field's max date to today

		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		var yyyy = today.getFullYear();
		if(dd<10){
				dd='0'+dd
			} 
			if(mm<10){
				mm='0'+mm
			} 

		today = yyyy+'-'+mm+'-'+dd;
		document.getElementById("startdate").setAttribute("max", today);
		document.getElementById("enddate").setAttribute("max", today);
	
}
		//datatable
	//	document.getElementById('table_id').DataTable();

        jQuery('#img_historique').hide();

        jQuery('#table_id tbody').on('mouseover', 'tr', function () {
            var hex_data = jQuery(this).attr('value');

            var extencion = jQuery(this).attr('value2');

            jQuery('#img_historique').attr('src', 'data:image/'+extencion+';base64,'+hex_data);
            jQuery('#img_historique').show();
        });

        jQuery( "#table_id tbody" ).mouseleave(function() {
            jQuery('#img_historique').hide();
        });

	

	(function () {
		'use strict'
		feather.replace()

		})()


});

function select_f2p(){
   var url = jQuery('#f2p_name').attr('action');
	var form_name =jQuery('#form-name').val();
	if(isNaN(form_name)){
		url+="&form-name="+form_name;
   	}
	window.location = url;
}

function displayModal(button){
	var	id_launch_modal=parseInt(jQuery(button).data("id"));
	jQuery('#f2p_edit-information'+id_launch_modal).modal('show');  
}

function displaySettingsModal(){
	jQuery('#f2p_display-settings').modal('show'); 
}

function renameField(input){
	console.log("text")
	var fieldName = jQuery(input).val()
	var fieldNameToChange = jQuery('.et_pb_contact_name_0').data("key")
	var fieldNameToChangeEmail = jQuery('#et_pb_contact_email_0').data("key")
	
			document.getElementsByClassName('et_pb_contact_name_0').innerText=fieldName
		//	document.getElementsByClassName('et_pb_contact_email_0').innerText=fieldName

}

function launchModalEditTemplate(button){
	var	id_launch_modal=parseInt(jQuery(button).data("id"));
	jQuery('#editTemplateModal'+id_launch_modal).modal('show'); 
}

// function setWidthHeight(select) {
// 	var	widthPDF  = parseInt(jQuery(select).data("width"));
// 	var heightPDF = parseInt(jQuery(select).data("height"));
// 	console.log(widthPDF)
// 	console.log(heightPDF)
	
// 	if(!isNaN(widthPDF) && !isNaN(heightPDF)){
// 		jQuery("#editWidthPDF").attr('value', widthPDF)
// 		jQuery("#editHeightPDF").attr('value', heightPDF)
// 	}
// }


function launchModalAddTemplate(){
	jQuery('#addTemplateModal').modal('show'); 
}

function select_f2p_import_csv(){
	var url = jQuery('#f2p_name').attr('action');
	var form_name =jQuery('#form-name').val();
	if(isNaN(form_name)){
		url+="&form-name="+form_name;
   	}
	window.location = url;
}


//Check import file is CSV file or not
function checkfile(sender) {

	var validExts = new Array(".csv");
	var fileExt = sender.value;
	fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
	if (validExts.indexOf(fileExt) < 0) {
		sender.value = '';
		alert("Invalid file uploaded, Please upload '" + validExts.toString() + "' types file only.");
	  return false;
	}
	else return true;
}
