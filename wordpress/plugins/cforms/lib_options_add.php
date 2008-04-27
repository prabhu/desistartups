<?php

	$FORMCOUNT=$FORMCOUNT+1;
	$no = $noDISP = $FORMCOUNT;
	
	//sorry, but WP2.2 doesn quickly enough flush the cache!
	if ( function_exists (wp_cache_close) ) {
		wp_cache_flush();
		wp_cache_close();
	}

	update_option('cforms_formcount', (string)($FORMCOUNT));

	add_option('cforms'.$no.'_count_fields', '5');
	add_option('cforms'.$no.'_count_field_1', __('My Fieldset', 'cforms').'$#$fieldsetstart$#$0$#$0$#$0$#$0$#$0');
	add_option('cforms'.$no.'_count_field_2', __('Your Name|Your Name', 'cforms').'$#$textfield$#$1$#$0$#$1$#$0$#$0');
	add_option('cforms'.$no.'_count_field_3', __('Email', 'cforms').'$#$textfield$#$1$#$1$#$0$#$0$#$0');
	add_option('cforms'.$no.'_count_field_4', __('Website|http://', 'cforms').'$#$textfield$#$0$#$0$#$0$#$0$#$0');
	add_option('cforms'.$no.'_count_field_5', __('Message', 'cforms').'$#$textarea$#$0$#$0$#$0$#$0$#$0');
	
	add_option('cforms'.$no.'_required', __('(required)', 'cforms'));
	add_option('cforms'.$no.'_emailrequired', __('(valid email required)', 'cforms'));
	
	add_option('cforms'.$no.'_ajax', '1');
	add_option('cforms'.$no.'_confirm', '0');
	add_option('cforms'.$no.'_fname', __('A new form', 'cforms')); 
	add_option('cforms'.$no.'_csubject', __('Re: Your note', 'cforms').'$#$'.__('Re: Submitted form (copy)', 'cforms'));
	add_option('cforms'.$no.'_cmsg', __('Dear {Your Name},', 'cforms') . "\n". __('Thank you for your note!', 'cforms') . "\n". __('We will get back to you as soon as possible.', 'cforms') . "\n\n");
	
	add_option('cforms'.$no.'_cmsg_html', '<div style="font:normal 1em arial; margin-top:10px"><p><strong>' . __('Dear {Your Name},', 'cforms') . "</strong></p>\n<p>". __('Thank you for your note!', 'cforms') . "</p>\n<p>". __('We will get back to you as soon as possible.', 'cforms') . "\n" . '<div style="width:80%; background:#f4faff ; color:#aaa; font-size:11px; padding:10px; margin-top:20px"><strong>'.__('This is an automatic confirmation message.', 'cforms').' {Date}.'.'</strong></div></div>'. "\n\n");

	add_option('cforms'.$no.'_email', get_bloginfo('admin_email') );
	add_option('cforms'.$no.'_fromemail', get_bloginfo('admin_email') );
	add_option('cforms'.$no.'_bcc', '');
	add_option('cforms'.$no.'_header', __('A new submission (form: "{Form Name}")', 'cforms') . "\r\n============================================\r\n" . __('Submitted on: {Date}', 'cforms') . "\r\n" . __('Via: {Page}', 'cforms') . "\r\n" . __('By {IP} (visitor IP)', 'cforms') . ".\r\n" . ".\r\n" );		
	add_option('cforms'.$no.'_header_html', '<p style="font:1em arial; font-weight:bold;">' . __('a form has been submitted on {Date}, via: {Page} [IP {IP}]', 'cforms') . '</p>' );		
	add_option('cforms'.$no.'_formdata', '1111');
	add_option('cforms'.$no.'_space', '30');
	add_option('cforms'.$no.'_noattachments', '0');
	
	add_option('cforms'.$no.'_subject', __('A comment from {Your Name}', 'cforms'));
	add_option('cforms'.$no.'_submit_text', __('Submit', 'cforms'));
	add_option('cforms'.$no.'_success', __('Thank you for your comment!', 'cforms'));
	add_option('cforms'.$no.'_failure', __('Please fill in all the required fields.', 'cforms'));
	add_option('cforms'.$no.'_limittxt', '<strong>'.__('No more submissions accepted at this time.', 'cforms').'</strong>');
	add_option('cforms'.$no.'_working', __('One moment please...', 'cforms'));
	add_option('cforms'.$no.'_popup', 'nn');
	add_option('cforms'.$no.'_showpos', 'ynyyy');
	
	add_option('cforms'.$no.'_redirect', '0');
	add_option('cforms'.$no.'_redirect_page', __('http://redirect.to.this.page', 'cforms'));		
	add_option('cforms'.$no.'_action', '0');
	add_option('cforms'.$no.'_action_page', 'http://');		

	/*file upload*/
	add_option('cforms'.$no.'_upload_dir', ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/attachments');
	add_option('cforms'.$no.'_upload_ext', 'txt,zip,doc,rtf,xls');
	add_option('cforms'.$no.'_upload_size', '1024');

	add_option('cforms'.$no.'_tracking', '');
	add_option('cforms'.$no.'_tellafriend', '01');
	add_option('cforms'.$no.'_dashboard', '0');
	add_option('cforms'.$no.'_maxentries', '');
	
	echo '<div id="message" class="updated fade"><p>'.__('A new form with default fields has been added.', 'cforms').'</p></div>';
	
	//sorry, but WP2.2 doesn quickly enough flush the cache!
	if ( function_exists (wp_cache_init) ){
		wp_cache_init();
		wp_cache_flush();
	} 
		
?>
