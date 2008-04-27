<?php

	$noDISP='1'; $no='';
	if( isset($_REQUEST['no']) ) {
		if( $_REQUEST['no']<>'1' )
			$noDISP = $no = $_REQUEST['no'];
	}
	
	$FORMCOUNT=$FORMCOUNT+1;

	//sorry, but WP2.2 doesn quickly enough flush the cache!
	if ( function_exists (wp_cache_close) ) {
		wp_cache_flush();
		wp_cache_close();
	}
	update_option('cforms_formcount', (string)($FORMCOUNT));
	
	add_option('cforms'.$FORMCOUNT.'_count_fields', get_option('cforms'.$no.'_count_fields'));
	
	for ( $j=1; $j<=get_option('cforms'.$no.'_count_fields'); $j++)  //delete all extra fields!
		  add_option('cforms'.$FORMCOUNT.'_count_field_'.$j, get_option('cforms'.$no.'_count_field_'.$j));
	
	add_option('cforms'.$FORMCOUNT.'_required', get_option('cforms'.$no.'_required'));
	add_option('cforms'.$FORMCOUNT.'_emailrequired', get_option('cforms'.$no.'_emailrequired'));
	
	add_option('cforms'.$FORMCOUNT.'_ajax', get_option('cforms'.$no.'_ajax'));
	add_option('cforms'.$FORMCOUNT.'_confirm', get_option('cforms'.$no.'_confirm'));
	add_option('cforms'.$FORMCOUNT.'_fname', "Duplicate of form #$noDISP");
	add_option('cforms'.$FORMCOUNT.'_csubject', get_option('cforms'.$no.'_csubject'));
	add_option('cforms'.$FORMCOUNT.'_cmsg', get_option('cforms'.$no.'_cmsg'));
	add_option('cforms'.$FORMCOUNT.'_cmsg_html', get_option('cforms'.$no.'_cmsg_html'));
	add_option('cforms'.$FORMCOUNT.'_email', get_option('cforms'.$no.'_email'));
	add_option('cforms'.$FORMCOUNT.'_bcc', get_option('cforms'.$no.'_bcc'));
	add_option('cforms'.$FORMCOUNT.'_header', get_option('cforms'.$no.'_header'));
	add_option('cforms'.$FORMCOUNT.'_header_html', get_option('cforms'.$no.'_header_html'));
	add_option('cforms'.$FORMCOUNT.'_formdata', get_option('cforms'.$no.'_formdata'));
	add_option('cforms'.$FORMCOUNT.'_space', get_option('cforms'.$no.'_space'));
	add_option('cforms'.$FORMCOUNT.'_noattachments', get_option('cforms'.$no.'_noattachments'));
	
	add_option('cforms'.$FORMCOUNT.'_subject', get_option('cforms'.$no.'_subject'));
	add_option('cforms'.$FORMCOUNT.'_submit_text', get_option('cforms'.$no.'_submit_text'));
	add_option('cforms'.$FORMCOUNT.'_success', get_option('cforms'.$no.'_success'));
	add_option('cforms'.$FORMCOUNT.'_failure', get_option('cforms'.$no.'_failure'));
	add_option('cforms'.$FORMCOUNT.'_limittxt', get_option('cforms'.$no.'_limittxt'));
	add_option('cforms'.$FORMCOUNT.'_working', get_option('cforms'.$no.'_working'));
	add_option('cforms'.$FORMCOUNT.'_popup', get_option('cforms'.$no.'_popup'));
	add_option('cforms'.$FORMCOUNT.'_showpos', get_option('cforms'.$no.'_showpos'));

	add_option('cforms'.$FORMCOUNT.'_redirect', get_option('cforms'.$no.'_redirect'));
	add_option('cforms'.$FORMCOUNT.'_redirect_page', get_option('cforms'.$no.'_redirect_page'));		
	add_option('cforms'.$FORMCOUNT.'_action', get_option('cforms'.$no.'_action'));
	add_option('cforms'.$FORMCOUNT.'_action_page', get_option('cforms'.$no.'_action_page'));		

	add_option('cforms'.$FORMCOUNT.'_upload_dir', get_option('cforms'.$no.'_upload_dir'));
	add_option('cforms'.$FORMCOUNT.'_upload_ext', get_option('cforms'.$no.'_upload_ext'));
	add_option('cforms'.$FORMCOUNT.'_upload_size', get_option('cforms'.$no.'_upload_size'));
		
	add_option('cforms'.$FORMCOUNT.'_tracking', get_option('cforms'.$no.'_tracking'));
	add_option('cforms'.$FORMCOUNT.'_tellafriend', get_option('cforms'.$no.'_tellafriend'));
	add_option('cforms'.$FORMCOUNT.'_dashboard', get_option('cforms'.$no.'_dashboard'));
	add_option('cforms'.$FORMCOUNT.'_maxentries', get_option('cforms'.$no.'_maxentries'));
	
	echo '<div id="message" class="updated fade"><p>'.__('The form has been duplicated, you\'re now working on the copy.', 'cforms').'</p></div>';

	//sorry, but WP2.2 doesn quickly enough flush the cache!
	if ( function_exists (wp_cache_init) ){
		wp_cache_init();
		wp_cache_flush();
	}
	
	//set $no afterwards: need it to duplicate fields
	$no = $noDISP = $FORMCOUNT;
		
?>
