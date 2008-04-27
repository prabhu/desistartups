<?php

  $file = $_FILES['import'];
	$err = '';
			
	// A successful upload will pass this test. It makes no sense to override this one.
	if ( $file['error'] > 0 )
			$err = $file['error'];

	// A non-empty file will pass this test.
	if ( !( $file['size'] > 0 ) )
			$err = __('File is empty. Please upload something more substantial.', 'cforms');

	// A properly uploaded file will pass this test. There should be no reason to override this one.
	if (! @ is_uploaded_file( $file['tmp_name'] ) )
			$err = __('Specified file failed upload test.', 'cforms');

	if ( $err <> '' ){

	  echo '<div id="message" class="updated fade"><p>'.__('Error:', 'cforms').' '.$err.'</p></div>';

	} else {

	  // current form
	  $noDISP = '1'; $no='';
		if( $_REQUEST['noSub']<>'1' )
			$noDISP = $no = $_REQUEST['noSub'];

		$importdata = file($file['tmp_name']);
		$cf=0;
		if ( !(strpos($importdata[0], 'cf:')===false) ) {
					update_option('cforms'.$no.'_count_fields',substr( trim($importdata[0]), 3) );
					$cf = (int) substr( trim($importdata[0]), 3);
		}
		
		if ( !(strpos($importdata[1], 'ff:')===false) ) {
					$fields = explode( '+++', substr( trim($importdata[1]), 3) );
				  for ( $i=1; $i<=$cf; $i++)  //now delete all fields from last form
							update_option('cforms'.$no.'_count_field_'.$i, $fields[$i-1] );

					//delete the rest until all gone
					while ( get_option( 'cforms'.$no.'_count_field_'.$i++ ) <> "" && $i<100 ) // 100: just to be safe
 							delete_option( 'cforms'.$no.'_count_field_'.($i-1) );
		}

		if ( !(strpos($importdata[2], 'rq:')===false) )
					update_option('cforms'.$no.'_required',substr( trim($importdata[2]), 3) );

		if ( !(strpos($importdata[3], 'er:')===false) )
					update_option('cforms'.$no.'_emailrequired',substr( trim($importdata[3]), 3) );

		if ( !(strpos($importdata[4], 'ac:')===false) )
					update_option('cforms'.$no.'_confirm',substr( trim($importdata[4]), 3) );

		if ( !(strpos($importdata[5], 'jx:')===false) )
					update_option('cforms'.$no.'_ajax',substr( trim($importdata[5]), 3) );

		if ( !(strpos($importdata[6], 'fn:')===false) )
					update_option('cforms'.$no.'_fname',substr( trim($importdata[6]), 3) );

		if ( !(strpos($importdata[7], 'cs:')===false) )
					update_option('cforms'.$no.'_csubject',substr( trim($importdata[7]), 3) );

		if ( !(strpos($importdata[8], 'cm:')===false) )
					update_option('cforms'.$no.'_cmsg', str_replace ('$n$', "\r\n",substr( trim($importdata[8]), 3) ));

		if ( !(strpos($importdata[9], 'em:')===false) )
					update_option('cforms'.$no.'_email',substr( trim($importdata[9]), 3) );

		if ( !(strpos($importdata[10], 'sj:')===false) )
					update_option('cforms'.$no.'_subject',substr( trim($importdata[10]), 3) );

		if ( !(strpos($importdata[11], 'su:')===false) )
					update_option('cforms'.$no.'_submit_text',substr( trim($importdata[11]), 3) );

		if ( !(strpos($importdata[12], 'sc:')===false) )
					update_option('cforms'.$no.'_success',str_replace ('$n$', "\r\n",substr( trim($importdata[12]), 3) ));

		if ( !(strpos($importdata[13], 'fl:')===false) )
					update_option('cforms'.$no.'_failure',str_replace ('$n$', "\r\n",substr( trim($importdata[13]), 3) ));

		if ( !(strpos($importdata[14], 'wo:')===false) )
					update_option('cforms'.$no.'_working',substr( trim($importdata[14]), 3) );

		if ( !(strpos($importdata[15], 'pp:')===false) )
					update_option('cforms'.$no.'_popup',substr( trim($importdata[15]), 3) );

		if ( !(strpos($importdata[16], 'sp:')===false) )
					update_option('cforms'.$no.'_showpos',substr( trim($importdata[16]), 3) );

		if ( !(strpos($importdata[17], 'rd:')===false) )
					update_option('cforms'.$no.'_redirect',substr( trim($importdata[17]), 3) );

		if ( !(strpos($importdata[18], 'rp:')===false) )
					update_option('cforms'.$no.'_redirect_page',substr( trim($importdata[18]), 3) );

		if ( !(strpos($importdata[19], 'hd:')===false) )
					update_option('cforms'.$no.'_header',str_replace ('$n$', "\r\n",substr( trim($importdata[19]), 3) ));

		if ( !(strpos($importdata[20], 'pc:')===false) )
					update_option('cforms'.$no.'_space',substr( trim($importdata[20]), 3) );
					
		if ( !(strpos($importdata[21], 'at:')===false) )
					update_option('cforms'.$no.'_noattachments',substr( trim($importdata[21]), 3) );

		if ( !(strpos($importdata[22], 'ud:')===false) )
					update_option('cforms'.$no.'_upload_dir',substr( trim($importdata[22]), 3) );

		if ( !(strpos($importdata[23], 'ue:')===false) )
					update_option('cforms'.$no.'_upload_ext',substr( trim($importdata[23]), 3) );

		if ( !(strpos($importdata[24], 'us:')===false) )
					update_option('cforms'.$no.'_upload_size',substr( trim($importdata[24]), 3) );

		if ( !(strpos($importdata[25], 'ar:')===false) )
					update_option('cforms'.$no.'_action',substr( trim($importdata[25]), 3) );

		if ( !(strpos($importdata[26], 'ap:')===false) )
					update_option('cforms'.$no.'_action_page',substr( trim($importdata[26]), 3) );
					
		if ( !(strpos($importdata[27], 'bc:')===false) )
					update_option('cforms'.$no.'_bcc',substr( trim($importdata[27]), 3) );

		if ( !(strpos($importdata[28], 'ch:')===false) )
					update_option('cforms'.$no.'_cmsg_html', str_replace ('$n$', "\r\n",substr( trim($importdata[28]), 3) ));

		if ( !(strpos($importdata[29], 'hh:')===false) )
					update_option('cforms'.$no.'_header_html',str_replace ('$n$', "\r\n",substr( trim($importdata[29]), 3) ));

		if ( !(strpos($importdata[30], 'fd:')===false) )
					update_option('cforms'.$no.'_formdata',substr( trim($importdata[30]), 3) );

		if ( !(strpos($importdata[31], 'tr:')===false) )
					update_option('cforms'.$no.'_tracking',substr( trim($importdata[31]), 3) );

		if ( !(strpos($importdata[32], 'fm:')===false) )
					update_option('cforms'.$no.'_fromemail',substr( trim($importdata[32]), 3) );

		if ( !(strpos($importdata[33], 'tf:')===false) )
					update_option('cforms'.$no.'_tellafriend',substr( trim($importdata[33]), 3) );

		if ( !(strpos($importdata[34], 'db:')===false) )
					update_option('cforms'.$no.'_dashboard',substr( trim($importdata[34]), 3) );

		if ( !(strpos($importdata[35], 'mx:')===false) )
					update_option('cforms'.$no.'_maxentries',substr( trim($importdata[35]), 3) );

		if ( !(strpos($importdata[36], 'lt:')===false) )
					update_option('cforms'.$no.'_limittxt',str_replace ('$n$', "\r\n",substr( trim($importdata[36]), 3) ));

		if ( !(strpos($importdata[37], 'cn:')===false) )
					update_option('cforms'.$no.'_customnames', substr( trim($importdata[37]), 3) );

	echo '<div id="message" class="updated fade"><p>'.__('All form specific settings have been restored from the backup file.', 'cforms').'</p></div>';
	}
		
?>
