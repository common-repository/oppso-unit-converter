/** this converts **/ jQuery(document).ready(	jQuery(document).on("click","#oppso_convert",	function() {		from = jQuery('#oppso_from').val();		to = jQuery('#oppso_to').val();		converter_type = jQuery('#oppso_converter_type').val();		oppso_value = jQuery('#oppso_value').val();		var data = {				action: 'oppso_do_convert',				from: from,				to: to,				converter_type: converter_type,				oppso_value: oppso_value			};		jQuery.post(ajaxurl, data, function(response) {						response = JSON.parse(response);			jQuery("#oppso_convert_result").html('Result: '+response+' '+to);			jQuery("#oppso_convert_result").fadeIn();	}  ); }));/** end this converts **//** converter selection ***/jQuery(document).ready(		jQuery(document).on("change"," #converter-selection .oppso-select",		function() {				converter_type =  (jQuery(this).val());				var data = {						action: 'oppso_do_change',						converter_type: converter_type					};				jQuery.post(ajaxurl, data, function(response) {										jQuery('#oppso-converter-type').html(response);				//	jQuery("#oppso_convert_result").html('Result: '+response+' '+to);				///	jQuery("#oppso_convert_result").fadeIn();			}  );		}	));/*** end converter selection ***/