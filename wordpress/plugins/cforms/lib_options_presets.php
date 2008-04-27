<?php

	$fullplugindir	= ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__));
	$file			= $fullplugindir.'/formpresets/'.$_REQUEST['formpresets'];
	
		if( is_file($file) && filesize($file) > 0)
			$fields = file($file);
		else {
		    echo '<div id="message" class="updated fade"><p><strong>'.__('Sorry, this form preset can\'t be loaded. I Can\'t find file ', 'cforms').'<br />'.$file.'</strong></p></div>';
		    return;
		}

	$i = 1;
	$taf = false;
	foreach( $fields as $field ){
		if ( strpos($field,'~~~')===false ) continue;
		
		$data = explode('~~~',$field);
		if( $data[0]=='ff' ){
			update_option("cforms{$no}_count_field_{$i}",$data[1]);
			$i++;
		}
		else if( $data[0]=='mx' ){
			update_option("cforms{$no}_maxentries",$data[1]);
		}
		else if( $data[0]=='su' ){
			update_option("cforms{$no}_submit_text",$data[1]);
		}
		else if( $data[0]=='lt' ){
			update_option("cforms{$no}_limittxt",$data[1]);
		}
		else if( $data[0]=='rd' ){
			update_option("cforms{$no}_redirect",$data[1]);
		}
		else if( $data[0]=='ri' ){
			update_option("cforms{$no}_required",$data[1]);
		}
		else if( $data[0]=='re' ){
			update_option("cforms{$no}_emailrequired",$data[1]);
		}
		else if( $data[0]=='tf' ){
			update_option("cforms{$no}_tellafriend", $data[1]);
		}
		else if( $data[0]=='tt' ){
			update_option("cforms{$no}_cmsg", str_replace('|nl|',"\n",$data[1]) );
			update_option("cforms{$no}_cmsg_html", str_replace('|nl|',"\n",$data[1]) );
			update_option("cforms{$no}_confirm", '1');
			$taf = $data[1];
		}
		else if( $data[0]=='ts' ){
			update_option("cforms{$no}_csubject",$data[1]);
		}
		else if( $data[0]=='cs' ){
			update_option("cforms_css",$data[1]);
		}
		else if( $data[0]=='dp' ){
			update_option("cforms{$no}_datepicker",$data[1]);
		}
	}
	
	$max=get_option("cforms{$no}_count_fields");
	for ( $j=$i; $j<=$max; $j++)
		update_option("cforms{$no}_count_field_{$j}",'');
		
	update_option("cforms{$no}_count_fields",($i-1)); 	
		
	?>
	<div id="message" class="updated fade"><p><strong>
		<?php
		_e('Your form has been populated with the preset input fields.', 'cforms');
		if( $taf==2 ){	
			echo '<br />'.sprintf(__('Please note, that in order to make this form work, the <strong>%s</strong> has been turned on, too!','cforms'),__('WP comment feature','cforms'));
			echo '<br />'.__('Check with the HELP page on how to <u>properly</u> use this cforms feature and check all your settings below!','cforms');
		} else if( $taf==11 ){
			echo '<br />'.sprintf(__('Please note, that in order to make this form work, the <strong>%s</strong> has been turned on, too!','cforms'),__('TAF feature','cforms'));
			echo '<br />'.__('Check with the HELP page on how to <u>properly</u> use this cforms feature and check all your settings below!','cforms');
		}
		?>
	</strong></p></div>
	<?php
			
?>
