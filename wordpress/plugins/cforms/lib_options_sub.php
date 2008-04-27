<?php

	$verification=false;
	$captcha=false;
	$ccbox=false;
	$emailtobox=false;

	for($i = 1; $i <= $field_count; $i++) {

	  if ($_REQUEST['field_' . $i . '_name']<>''){    // Newly "AddField" does not exist yet!

				$allgood=true;
				$name = str_replace('$#$', '$', $_REQUEST['field_' . $i . '_name']);
				$type = $_REQUEST['field_' . $i . '_type'];
				$required = 0;
				$emailcheck = 0;
				$clear = 0;
				$disabled = 0;
				$readonly = 0;

				if( $type=='verification' ){
					$allgood = $verification?false:true;
					$usermsg .= $verification?__('Only one <em>Visitor verification</em> field is permitted!', 'cforms').'<br />':'';
					$verification=true;
				}
				if( $type=='captcha' ){
					$allgood = $captcha?false:true;
					$usermsg .= $captcha?__('Only one <em>captcha</em> field is permitted!', 'cforms').'<br />':'';
					$captcha=true;
				}
				if( $type=='ccbox' ){
					$allgood = $ccbox?false:true;
					$usermsg .= $ccbox?__('Only one <em>CC:</em> field is permitted!', 'cforms').'<br />':'';
					$ccbox=true;
				}
				if( $type=='emailtobox' ){
					$allgood = $emailtobox?false:true;
					$usermsg .= $emailtobox?__('Only one <em>Multiple Recipients</em> field is permitted!'.'<br />', 'cforms'):'';
					$emailtobox=true;
				}
						
				if(isset($_REQUEST['field_' . $i . '_required']) && in_array($type,array('pwfield','textfield','datepicker','textarea','checkbox','multiselectbox','selectbox','emailtobox','upload','yourname','youremail','friendsname','friendsemail','email','cauthor','url','comment')) ) {
					$required = 1;
				}
				
				if(isset($_REQUEST['field_' . $i . '_emailcheck']) && in_array($type,array('textfield','datepicker','youremail','friendsemail','email')) ){
					$emailcheck = 1;
				}

				if(isset($_REQUEST['field_' . $i . '_clear']) && in_array($type,array('pwfield','textfield','datepicker','textarea','yourname','youremail','friendsname','friendsemail','email','cauthor','url','comment')) ) {
					$clear = 1;
				}

				if(isset($_REQUEST['field_' . $i . '_disabled']) && in_array($type,array('pwfield','textarea','datepicker','textfield','checkbox','checkboxgroup','multiselectbox','selectbox','radiobuttons','upload')) ) {
					$disabled = 1;
				}

				if(isset($_REQUEST['field_' . $i . '_readonly']) && in_array($type,array('pwfield','textarea','datepicker','textfield','checkbox','checkboxgroup','multiselectbox','selectbox','radiobuttons','upload')) ) {
					$readonly = 1;
				}
				
				if ($allgood)
						update_option('cforms'.$no.'_count_field_' . $i, $name . '$#$' . $type . '$#$' . $required. '$#$'. $emailcheck . '$#$'. $clear . '$#$'. $disabled . '$#$'. $readonly);

				$all_fields[$i-1]=$name . '$#$' . $type . '$#$' . $required. '$#$' . $emailcheck . '$#$'. $clear . '$#$' . $disabled . '$#$' . $readonly;
				
		}
	}

	update_option('cforms'.$no.'_upload_dir',  $_REQUEST['cforms_upload_dir'].'$#$'.$_REQUEST['cforms_upload_dir_url']);
	update_option('cforms'.$no.'_upload_ext',  $_REQUEST['cforms_upload_ext']);
	update_option('cforms'.$no.'_upload_size', $_REQUEST['cforms_upload_size']);
	
	update_option('cforms'.$no.'_confirm',    $_REQUEST['cforms_confirm']?'1':'0');
	update_option('cforms'.$no.'_ajax',       $_REQUEST['cforms_ajax']?'1':'0');
	update_option('cforms'.$no.'_popup',     ($_REQUEST['cforms_popup1']?'y':'n').($_REQUEST['cforms_popup2']?'y':'n') );

/*	if ( $_REQUEST['cforms_redirect']=='2' ){
		$tmp = substr(get_option('cforms'.$no.'_showpos'),2);
		update_option('cforms'.$no.'_showpos', 'yn'.$tmp );
	}
	else
*/
	update_option('cforms'.$no.'_showpos',   ($_REQUEST['cforms_showposa']?'y':'n').($_REQUEST['cforms_showposb']?'y':'n').($_REQUEST['cforms_errorLI']?'y':'n').($_REQUEST['cforms_errorINS']?'y':'n').($_REQUEST['cforms_jump']?'y':'n') );

	update_option('cforms'.$no.'_fname',    preg_replace("/\\\+/", "\\",$_REQUEST['cforms_fname']));	
	update_option('cforms'.$no.'_csubject', preg_replace("/\\\+/", "\\",$_REQUEST['cforms_csubject']).'$#$'.preg_replace("/\\\+/", "\\",$_REQUEST['cforms_ccsubject']) );
	update_option('cforms'.$no.'_cmsg',     preg_replace("/\\\+/", "\\",$_REQUEST['cforms_cmsg']));
	update_option('cforms'.$no.'_cmsg_html',preg_replace("/\\\+/", "\\",$_REQUEST['cforms_cmsg_html']));

  	update_option('cforms'.$no.'_required',      $_REQUEST['cforms_required']);
  	update_option('cforms'.$no.'_emailrequired', $_REQUEST['cforms_emailrequired']);
	update_option('cforms'.$no.'_success',       $_REQUEST['cforms_success']);
	update_option('cforms'.$no.'_failure',       $_REQUEST['cforms_failure']);
	if( $_REQUEST['cforms_maxentries']<>'' && isset($_REQUEST['cforms_limittxt']) )
		update_option('cforms'.$no.'_limittxt',      $_REQUEST['cforms_limittxt']);
	update_option('cforms'.$no.'_working',       $_REQUEST['cforms_working']);

	update_option('cforms'.$no.'_submit_text',   $_REQUEST['cforms_submit_text']);
	update_option('cforms'.$no.'_email',         $_REQUEST['cforms_email']);
	update_option('cforms'.$no.'_fromemail',     $_REQUEST['cforms_fromemail']);
	
	update_option('cforms'.$no.'_bcc',           $_REQUEST['cforms_bcc']);
	update_option('cforms'.$no.'_subject',       $_REQUEST['cforms_subject']);
	update_option('cforms'.$no.'_header',      preg_replace("/\\\+/", "\\",$_REQUEST['cforms_header']));
	update_option('cforms'.$no.'_header_html', preg_replace("/\\\+/", "\\",$_REQUEST['cforms_header_html'])); 

	update_option('cforms'.$no.'_formdata',     ($_REQUEST['cforms_formdata_txt']?'1':'0').($_REQUEST['cforms_formdata_html']?'1':'0').($_REQUEST['cforms_admin_html']?'1':'0').($_REQUEST['cforms_user_html']?'1':'0') );
	
	update_option('cforms'.$no.'_space',         $_REQUEST['cforms_space']);
	update_option('cforms'.$no.'_noattachments', $_REQUEST['cforms_noattachments']?'1':'0');

	update_option('cforms'.$no.'_redirect',      $_REQUEST['cforms_redirect']);
	update_option('cforms'.$no.'_redirect_page', preg_replace("/\\\+/", "\\",$_REQUEST['cforms_redirect_page']));
	update_option('cforms'.$no.'_action',        $_REQUEST['cforms_action']?'1':'0');
	update_option('cforms'.$no.'_action_page',   preg_replace("/\\\+/", "\\",$_REQUEST['cforms_action_page']));
	update_option('cforms'.$no.'_tracking',      preg_replace("/\\\+/", "\\",$_REQUEST['cforms_tracking']));

	if ( isset($_REQUEST['cforms_taftrick']) && (get_option('cforms'.$no.'_tellafriend')==0 || get_option('cforms'.$no.'_tellafriend')==3) )
		update_option('cforms'.$no.'_tellafriend', '3');		
	else
		update_option('cforms'.$no.'_tellafriend',	($_REQUEST['cforms_tellafriend']?'1':'0').($_REQUEST['cforms_tafdefault']?'1':'0') );
	
	if ( isset($_REQUEST['cforms_commentrep']) )
		update_option('cforms'.$no.'_tellafriend',	($_REQUEST['cforms_commentrep']?'2':'0') );
	
	update_option('cforms'.$no.'_dashboard',	($_REQUEST['cforms_dashboard']?'1':'0') );

	update_option('cforms'.$no.'_maxentries',	($_REQUEST['cforms_maxentries']==''?'':(int)$_REQUEST['cforms_maxentries']) );
	update_option('cforms'.$no.'_customnames',	($_REQUEST['cforms_customnames']?'1':'0') );
	
	// did the order of fields change ?
	if(isset($_REQUEST['field_order']) && $_REQUEST['field_order']<>'') {
		$j=0;

		$result = preg_match_all('/allfields\[\]=f([^&]+)&?/',$_REQUEST['field_order'],$order);
		$order  = $order[1];
//		print_r($order);
		
		//echo $temp[1]."<br>";  // debug
		$tempcount = isset($_REQUEST['AddField'])?($field_count-$_POST['AddFieldNo']):($field_count);
		while($j < $tempcount)
		{
				$new_f = $order[$j]-1;
				if ( $j <> $new_f )
						update_option('cforms'.$no.'_count_field_'.($j+1),$all_fields[$new_f]);
					  //echo "$j=$new_f :: ".$all_fields[$j]." == ".$all_fields[$new_f]."<br />";  //debug
		$j++;
		}

	} //if order changed

	echo '<div id="message" class="updated fade"><p>'.__('Form settings updated.', 'cforms').'</p></div>';
		
?>
