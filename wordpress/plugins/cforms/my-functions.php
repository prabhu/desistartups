<?php

// Find below examples for your custom routines. Do not change the function names.
//
// my_cforms_action() : gets triggered after user input validation and processing
// my_cforms_filter() : after validation, before processing (nonAJAX)
// my_cforms_ajax_filter() : after validation, before processing (AJAX)

// un-comment if you require custom processing and modify the below examples 
// to meet your requirements

/*

//
// Your custom user data input filter 
//
function my_cforms_action($cformsdata) {

	// Extract Data
	// Note: $formID = '' (empty) for the first form! 
	
	$formID = $cformsdata['id'];      
	$form   = $cformsdata['data'];
	
	// triggers on your third form 
	if ( $formID == '3' ) {
	
		// Do something with the data or not, up to you
		$form['Your Name'] = 'Mr./Mrs. '.$form['Your Name'];
		
		// Send to 3d party or do something else
		@mail('your@email.com', 'cforms my_action test', implode(', ',$form), 'From: your@blog.com');	
		
	}
	
}



//
// Your custom user data input filter (non ajax)
//
function my_cforms_filter($POSTdata) {

	// triggers on your third form 
	if ( isset($POSTdata['sendbutton3']) ) {

			// do something with field name 'cf3_field_3'
			// (! check you HTML source to properly reference your form fields !)
			$POSTdata['cf3_field_3'] = 'Mr./Mrs. '.$POSTdata['cf3_field_3'];
	
			// perhaps send an email or do somethign different			
			@mail('your@email.com', 'cforms my_filter_nonAjax test', 'Just a test.', 'From: your@blog.com');	
	}		
	return $POSTdata;
	
}



//
// Your custom user data input filter (ajax)
//
function my_cforms_ajax_filter($params) {

	// triggers on your third form 
	if ( $params['id']=='3' ) {

			// do something with field #1
			// (! for ajax, all form fields are counted sequentially! !)
			$params['field_1'] = 'Mr./Mrs. '.$params['field_1'];
	
			// perhaps send an email or do somethign different			
			@mail('your@email.com', 'cforms my_filter_Ajax test', 'Just a test, triggeres by cforms Ajax routine.', 'From: your@blog.com');	

	}
	return $params;
	
}

*/
	
?>
