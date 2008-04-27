<?php

	//sorry, but WP2.2 doesn quickly enough flush the cache!
	if ( function_exists (wp_cache_close) ){ 
		wp_cache_flush();
		wp_cache_close();
	}
	
	// current form
	$noDISP = '1'; $no='';
	if( $_REQUEST['no']<>'1' )
		$noDISP = $no = $_REQUEST['no'];

	for ( $i=(int)$noDISP; $i<$FORMCOUNT; $i++) {  // move all forms "to the left"
  
		for ( $j=1; $j<=get_option('cforms'.$i.'_count_fields'); $j++)  //delete all extra fields!
		  delete_option('cforms'.$i.'_count_field_'.$j);
		  
		for ( $j=1; $j<=get_option('cforms'.($i+1).'_count_fields'); $j++) {
		  $tempo = get_option('cforms'.($i+1).'_count_field_'.$j);
		  add_option('cforms'.$i.'_count_field_'.$j,$tempo);
		}
		
		$tempo = get_option('cforms'.($i+1).'_count_fields');
		update_option('cforms'.$i.'_count_fields',$tempo);
		$tempo = get_option('cforms'.($i+1).'_required');
		update_option('cforms'.$i.'_required',$tempo);
		$tempo = get_option('cforms'.($i+1).'_emailrequired');
		update_option('cforms'.$i.'_emailrequired',$tempo);
		
		$tempo = get_option('cforms'.($i+1).'_confirm');
		update_option('cforms'.$i.'_confirm',$tempo);
		$tempo = get_option('cforms'.($i+1).'_ajax');
		update_option('cforms'.$i.'_ajax',$tempo);
		$tempo = get_option('cforms'.($i+1).'_fname');
		update_option('cforms'.$i.'_fname',$tempo);
		$tempo = get_option('cforms'.($i+1).'_csubject');
		update_option('cforms'.$i.'_csubject',$tempo);
		$tempo = get_option('cforms'.($i+1).'_cmsg');
		update_option('cforms'.$i.'_cmsg',$tempo);
		$tempo = get_option('cforms'.($i+1).'_cmsg_html');
		update_option('cforms'.$i.'_cmsg_html',$tempo);
		$tempo = get_option('cforms'.($i+1).'_email');
		update_option('cforms'.$i.'_email',$tempo);
		$tempo = get_option('cforms'.($i+1).'_fromemail');
		update_option('cforms'.$i.'_fromemail',$tempo);
		$tempo = get_option('cforms'.($i+1).'_bcc');
		update_option('cforms'.$i.'_bcc',$tempo);
		$tempo = get_option('cforms'.($i+1).'_header');
		update_option('cforms'.$i.'_header',$tempo);
		$tempo = get_option('cforms'.($i+1).'_header_html');
		update_option('cforms'.$i.'_header_html',$tempo);
		$tempo = get_option('cforms'.($i+1).'_formdata');
		update_option('cforms'.$i.'_formdata',$tempo);
		$tempo = get_option('cforms'.($i+1).'_space');
		update_option('cforms'.$i.'_space',$tempo);
		$tempo = get_option('cforms'.($i+1).'_noattachments');
		update_option('cforms'.$i.'_noattachments',$tempo);
		
		$tempo = get_option('cforms'.($i+1).'_upload_dir');
		update_option('cforms'.$i.'_upload_dir',$tempo);
		$tempo = get_option('cforms'.($i+1).'_upload_ext');
		update_option('cforms'.$i.'_upload_ext',$tempo);
		$tempo = get_option('cforms'.($i+1).'_upload_size');
		update_option('cforms'.$i.'_upload_size',$tempo);
		
		$tempo = get_option('cforms'.($i+1).'_subject');
		update_option('cforms'.$i.'_subject',$tempo);
		$tempo = get_option('cforms'.($i+1).'_submit_text');
		update_option('cforms'.$i.'_submit_text',$tempo);
		$tempo = get_option('cforms'.($i+1).'_success');
		update_option('cforms'.$i.'_success',$tempo);
		$tempo = get_option('cforms'.($i+1).'_failure');
		update_option('cforms'.$i.'_failure',$tempo);
		$tempo = get_option('cforms'.($i+1).'_limittxt');
		update_option('cforms'.$i.'_limittxt',$tempo);
		$tempo = get_option('cforms'.($i+1).'_working');
		update_option('cforms'.$i.'_working',$tempo);
		$tempo = get_option('cforms'.($i+1).'_popup');
		update_option('cforms'.$i.'_popup',$tempo);
		$tempo = get_option('cforms'.($i+1).'_showpos');
		update_option('cforms'.$i.'_showpos',$tempo);
		
		$tempo = get_option('cforms'.($i+1).'_redirect');
		update_option('cforms'.$i.'_redirect',$tempo);
		$tempo = get_option('cforms'.($i+1).'_redirect_page');
		update_option('cforms'.$i.'_redirect_page',$tempo);
		$tempo = get_option('cforms'.($i+1).'_action');
		update_option('cforms'.$i.'_action',$tempo);
		$tempo = get_option('cforms'.($i+1).'_action_page');
		update_option('cforms'.$i.'_action_page',$tempo);
		
		$tempo = get_option('cforms'.($i+1).'_tracking');
		update_option('cforms'.$i.'_tracking',$tempo);
		
		$tempo = get_option('cforms'.($i+1).'_tellafriend');
		update_option('cforms'.$i.'_tellafriend',$tempo);
		$tempo = get_option('cforms'.($i+1).'_dashboard');
		update_option('cforms'.$i.'_dashboard',$tempo);
		$tempo = get_option('cforms'.($i+1).'_maxentries');
		update_option('cforms'.$i.'_maxentries',$tempo);
		
		//sorry, but WP2.2 doesn quickly enough flush the cache!
		if ( function_exists (wp_cache_init) ){
			wp_cache_init();
			wp_cache_flush();
		} 
	}

	$alloptions =  $wpdb->query("DELETE FROM `$wpdb->options` WHERE option_name LIKE 'cforms{$FORMCOUNT}_%'");

	$FORMCOUNT=$FORMCOUNT-1;
  
	if ( $FORMCOUNT>1 && ((int)$_REQUEST['no'])>1 ) {
		if( isset($_REQUEST['no']) && (int)$_REQUEST['no']<=$FORMCOUNT) // otherwise stick with the current form
			$noDISP = $no = $_REQUEST['no'];
		else
				$no = $noDISP = $FORMCOUNT;
	} else {
		$noDISP = '1';
		$no='';
	}
    
	update_option('cforms_formcount', (string)($FORMCOUNT));
	echo '<div id="message" class="updated fade"><p>'. __('Form deleted', 'cforms').'.</p></div>';
		
?>
