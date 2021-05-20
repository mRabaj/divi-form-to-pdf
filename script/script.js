jQuery(document).ready(function($) {
	
	if(window.location.href.indexOf("forms_to_pdf_home")!==-1)
	
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

		(function () {
			'use strict'
			feather.replace()
	
			})()

		// if(jQuery("#noRecordsFounds")){
		// 	jQuery("#buttonDownload").attr('disabled', true)
		// }
	
}

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



		//Setup icon functionality in setting page
	jQuery('#f2p_display-settings li span.dashicons').click(function(event) {
        var $this = jQuery(this);
        var $parent = $this.parent();
        var $custom_label = $parent.find('.txt_show');
        //currently visible
        if ($this.hasClass('dashicons-visibility')) {
            $this.removeClass('dashicons-visibility').addClass('dashicons-hidden');
            $parent.removeClass('show').addClass('hide');
            $custom_label.val('0');
        } else if ($this.hasClass('dashicons-hidden')) {
            $this.removeClass('dashicons-hidden').addClass('dashicons-visibility');
            $parent.removeClass('hide').addClass('show');
            $custom_label.val('1');
        }
    });


	// var triggerTabList = [].slice.call(document.querySelectorAll('#myTab a'))
	// triggerTabList.forEach(function (triggerEl) {
	// var tabTrigger = new bootstrap.Tab(triggerEl)

	// triggerEl.addEventListener('click', function (event) {
	// 	event.preventDefault()
	// 	tabTrigger.show()
	// })
	// })


});
// function which allows to direct to a new url 
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


function launchModalEditTemplate(button){
	var	id_launch_modal=parseInt(jQuery(button).data("id"));
	jQuery('#editTemplateModal'+id_launch_modal).modal('show'); 
}

function changeWidthNHeight(select) {	

	switch (jQuery(select).val()) {
		case 'a4_portrait':
			jQuery("#editWidthPDF").attr('value', 595)
			jQuery("#editHeightPDF").attr('value', 842)
			jQuery("#addWidthPDF").attr('value', 595)
			jQuery("#addHeightPDF").attr('value', 842)
		break;
		case 'a4_landscape':
			jQuery("#editWidthPDF").attr('value', 842)
			jQuery("#editHeightPDF").attr('value', 595)
			jQuery("#addWidthPDF").attr('value', 842)
			jQuery("#addHeightPDF").attr('value', 595)
		break;
		case 'letter':
			jQuery("#editWidthPDF").attr('value', 612)
			jQuery("#editHeightPDF").attr('value', 792)
			jQuery("#addWidthPDF").attr('value', 612)
			jQuery("#addHeightPDF").attr('value', 792)
		break;
		case 'note':
			jQuery("#editWidthPDF").attr('value', 540)
			jQuery("#editHeightPDF").attr('value', 842)
			jQuery("#addWidthPDF").attr('value', 540)
			jQuery("#addHeightPDF").attr('value', 842)
		break;
		case 'legal':
			jQuery("#editWidthPDF").attr('value', 612)
			jQuery("#editHeightPDF").attr('value', 1008)
			jQuery("#addWidthPDF").attr('value', 612)
			jQuery("#addHeightPDF").attr('value', 1008)
		break;
		case 'tabloid':
			jQuery("#editWidthPDF").attr('value', 792)
			jQuery("#editHeightPDF").attr('value', 1224)
			jQuery("#addWidthPDF").attr('value', 792)
			jQuery("#addHeightPDF").attr('value', 1224)
		break;
		case 'executive':
			jQuery("#editWidthPDF").attr('value', 522)
			jQuery("#editHeightPDF").attr('value', 756)
			jQuery("#addWidthPDF").attr('value', 522)
			jQuery("#addHeightPDF").attr('value', 756)
		break;
		case 'postcard':
			jQuery("#editWidthPDF").attr('value', 283)
			jQuery("#editHeightPDF").attr('value', 416)
			jQuery("#addWidthPDF").attr('value', 283)
			jQuery("#addtHeightPDF").attr('value', 416)
		break;
		
	}
}

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

//  if the end date is selected then the start of date search receives the maximum end date 
function verifyDate(type){
	if(type==1){
		document.getElementById("enddate").setAttribute("min", jQuery('#startdate').val());
	}
	else if(type==2){
		document.getElementById("startdate").setAttribute("max", jQuery('#enddate').val());
	}	
}
	