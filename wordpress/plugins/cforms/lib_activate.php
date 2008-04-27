<?php
		/*file upload*/
		add_option('cforms_upload_dir', ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/attachments');
		add_option('cforms_upload_ext', 'txt,zip,doc,rtf,xls');
		add_option('cforms_upload_size', '1024');
		
		add_option('cforms_upload_err1', __('Generic file upload error. Please try again', 'cforms'));
		add_option('cforms_upload_err2', __('File is empty. Please upload something more substantial.', 'cforms'));
		add_option('cforms_upload_err3', __('Sorry, file is too large. You may try to zip your file.', 'cforms'));
		add_option('cforms_upload_err4', __('File upload failed. Please try again or contact the blog admin.', 'cforms'));
		add_option('cforms_upload_err5', __('File not accepted, file type not allowed.', 'cforms'));

		/*for default form*/
		add_option('cforms_count_field_1', __('My Fieldset', 'cforms').'$#$fieldsetstart$#$0$#$0$#$0$#$0$#$0');
		add_option('cforms_count_field_2', __('Your Name|Your Name', 'cforms').'$#$textfield$#$1$#$0$#$1$#$0$#$0');
		add_option('cforms_count_field_3', __('Email', 'cforms').'$#$textfield$#$1$#$1$#$0$#$0$#$0');
		add_option('cforms_count_field_4', __('Website', 'cforms').'|http://$#$textfield$#$0$#$0$#$0$#$0$#$0');
		add_option('cforms_count_field_5', __('Message', 'cforms').'$#$textarea$#$0$#$0$#$0$#$0$#$0');

		/*form verification questions*/
		add_option('cforms_sec_qa', __('What color is snow?=white', 'cforms'). "\r\n" . __('The color of grass is=green', 'cforms'). "\r\n" . __('Ten minus five equals=five', 'cforms'));
		add_option('cforms_formcount', '1');
		add_option('cforms_show_quicktag', '1');
		add_option('cforms_count_fields', '5');
		add_option('cforms_required', __('(required)', 'cforms'));
		add_option('cforms_emailrequired', __('(valid email required)', 'cforms'));

		add_option('cforms_confirm', '0');
		add_option('cforms_ajax', '1');
		add_option('cforms_fname', __('Your default form', 'cforms'));
		add_option('cforms_csubject', __('Re: Your note', 'cforms').'$#$'.__('Re: Submitted form (copy)', 'cforms'));
		add_option('cforms_cmsg', __('Dear {Your Name},', 'cforms') . "\n" . __('Thank you for your note!', 'cforms') . "\n". __('We will get back to you as soon as possible.', 'cforms') . "\n\n");

		add_option('cforms_cmsg_html', '<div style="font:normal 1em arial; margin-top:10px"><p><strong>' . __('Dear {Your Name},', 'cforms') . "</strong></p>\n<p>". __('Thank you for your note!', 'cforms') . "</p>\n<p>". __('We will get back to you as soon as possible.', 'cforms') . "\n" . '<div style="width:80%; background:#f4faff ; color:#aaa; font-size:11px; padding:10px; margin-top:20px"><strong>'.__('This is an automatic confirmation message.', 'cforms').' {Date}.'.'</strong></div></div>'. "\n\n");

		add_option('cforms_email', get_bloginfo('admin_email') );
		add_option('cforms_fromemail', get_bloginfo('admin_email') );
		add_option('cforms_bcc', '');

		add_option('cforms_commentsuccess', __('Thank you for leaving a comment.', 'cforms'));
		add_option('cforms_commentWait', '15');
		add_option('cforms_commentParent', 'commentlist');
		add_option('cforms_commentHTML', "<li id=\"comment-{id}\">{moderation}\n<p>{usercomment}</p>\n<p>\n<cite>Comment by <a href=\"{url}\" rel=\"external nofollow\">{author}</a> &mdash; {date} @ <a href=\"#comment-{id}\">{time}</a></cite>\n</p>\n</li>");
		add_option('cforms_commentInMod', '<em>'.__('Your comment is awaiting moderation.', 'cforms').'</em>');

		add_option('cforms_header', __('A new submission (form: "{Form Name}")', 'cforms') . "\r\n============================================\r\n" . __('Submitted on: {Date}', 'cforms') . "\r\n" . __('Via: {Page}', 'cforms') . "\r\n" . __('By {IP} (visitor IP)', 'cforms') . ".\r\n" . ".\r\n" );		
		add_option('cforms_header_html', '<p style="font:1em arial; font-weight:bold;">' . __('a form has been submitted on {Date}, via: {Page} [IP {IP}]', 'cforms') . '</p>' );		
		add_option('cforms_formdata', '1111');
		add_option('cforms_space', '30');
		add_option('cforms_noattachments', '0');

		add_option('cforms_subject', __('A comment from {Your Name}', 'cforms'));
		add_option('cforms_submit_text', __('Submit', 'cforms'));
		add_option('cforms_success', __('Thank you for your comment!', 'cforms'));
		add_option('cforms_failure', __('Please fill in all the required fields.', 'cforms'));
		add_option('cforms_limittxt', '<strong>'.__('No more submissions accepted at this time.', 'cforms').'</strong>');
		add_option('cforms_codeerr', __('Please double-check your verification code.', 'cforms'));
		add_option('cforms_working', __('One moment please...', 'cforms'));
		add_option('cforms_popup', 'nn');
		add_option('cforms_showpos', 'ynyyy');
		add_option('cforms_database', '0');

		add_option('cforms_css', 'cforms.css');
		add_option('cforms_labelID', '0');
		add_option('cforms_liID', '0');
		
		add_option('cforms_redirect', '0');
		add_option('cforms_redirect_page', __('http://redirect.to.this.page', 'cforms'));		
		add_option('cforms_action', '0');
		add_option('cforms_action_page', 'http://');		
		
		add_option('cforms_tracking', '');
		add_option('cforms_showdashboard', '1');
		add_option('cforms_maxentries', '');
		add_option('cforms_tellafriend', '0');
		add_option('cforms_dashboard', '0');
		add_option('cforms_datepicker', '0');
		add_option('cforms_dp_start', '0');
		add_option('cforms_dp_date', 'MM/dd/yyyy');
		add_option('cforms_dp_days', __('"S","M","T","W","T","F","S"', 'cforms'));
		add_option('cforms_dp_months', __('"January","February","March","April","May","June","July","August","September","October","November","December"', 'cforms'));

		$nav[0]=__('Previous Year', 'cforms');
		$nav[1]=__('Previous Month', 'cforms');
		$nav[2]=__('Next Year', 'cforms');
		$nav[3]=__('Next Month', 'cforms');
		$nav[4]=__('Close', 'cforms');
		$nav[5]=__('Choose Date', 'cforms');	
		add_option('cforms_dp_nav', $nav);
		
		// updates existing tracking db
		if ( $wpdb->get_var("show tables like '$wpdb->cformsdata'") == $wpdb->cformsdata ) {
			// fetch table column structure from the database
			$tablefields = $wpdb->get_results("DESCRIBE {$wpdb->cformsdata};");

            $afield = array();
			foreach($tablefields as $field)
                array_push ($afield,$field->Field); 
            
            if ( !in_array('f_id', $afield) ) {
    			$sql = "ALTER TABLE " . $wpdb->cformsdata . " 
    					  ADD f_id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    					  CHANGE field_name field_name varchar(100) NOT NULL default '';";
    			$wpdb->query($sql);
              	echo '<div id="message" class="updated fade"><p><strong>' . __('Existing cforms tracking tables updated.', 'cforms') . '</strong></p></div>';
            }            
        }
?>
