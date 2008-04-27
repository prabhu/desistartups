<?php

require_once('../../../../../wp-config.php');

if ( !current_user_can('edit_users') )
	wp_die("access restricted.");

global $wpdb;

$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

### get form names
$sql="SELECT * FROM {$wpdb->cformssubmissions} $WHERE $sort $limit";
$result = $wpdb->get_results($sql);
for ($i=1; $i <= get_option('cforms_formcount'); $i++){
	$n = ( $i==1 )?'':$i; 
	$fnames[$i]=stripslashes(get_option('cforms'.$n.'_fname'));
}


$showIDs = $_POST['showids'];
$sortBy = ($_POST['sortby']<>'')?$_POST['sortby']:'sub_id';
$sortOrder = ($_POST['sortorder']<>'')?substr($_POST['sortorder'],1):'desc';

$qtype = $_POST['qtype'];
$query = $_POST['query'];

if ( $query<>'' && $query<>'undefined' && $showIDs=='all' )
	$doquery = "AND $qtype LIKE '%$query%'";
else
	$doquery = '';

if ($showIDs<>'') {

	if ( $showIDs<>'all' )
		$in_list = 'AND sub_id in ('.substr($showIDs,0,-1).')';
	else
		$in_list = '';
	
	$sql="SELECT *, form_id, ip FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id=id $in_list $doquery ORDER BY $sortBy $sortOrder, f_id";
	$entries = $wpdb->get_results($sql);
	?>

	<div id="top">
	<?php if ($entries) :

		$sub_id='';
		foreach ($entries as $entry){

			if( $sub_id<>$entry->sub_id ){

				if( $sub_id<>'' )
					echo '</div>';

				$sub_id = $entry->sub_id;
				echo '<div class="showform" id="entry'.$entry->sub_id.'">'.
					 '<div class="dataheader"><span>'.__('Form:','cforms').' </span><span class="b">'. stripslashes(get_option('cforms'.$entry->form_id.'_fname')) . '</span><span class="e">(ID:' . $entry->sub_id . ')</span>' .
					 '<a class="xdatabutton" type="submit" id="xbutton'.$entry->sub_id.'" title="'.__('delete this entry', 'cforms').'" value=""></a></div>' . "\n";
			}

			$name = $entry->field_name==''?'':stripslashes($entry->field_name);
			$val  = $entry->field_val ==''?'':stripslashes($entry->field_val);

			if (strpos($name,'[*]')!==false) {  // attachments?

					$no   = $entry->form_id; 

					$temp = explode( '$#$',stripslashes(htmlspecialchars(get_option('cforms'.$no.'_upload_dir'))) );
					$fileuploaddir = $temp[0];
					$fileuploaddirurl = $temp[1];
										
					if ( $fileuploaddirurl=='' ){
	                    $fileurl = $fileuploaddir.'/'.$entry->sub_id.'-'.strip_tags($val);
	                    $fileurl = get_settings('siteurl') . substr( $fileurl, strpos($fileurl, '/wp-content/') );
					}
					else
	                    $fileurl = $fileuploaddirurl.'/'.$entry->sub_id.'-'.strip_tags($val);

					echo '<div class="showformfield" style="margin:4px 0;color:#3C575B;"><div class="L">';
					_e('Attached file:', 'cforms');
					if ( $entry->field_val == '' )
						echo 	'</div><div class="R">' . __('-','cforms') . '</div></div>' . "\n";					
					else
						echo 	'</div><div class="R">' . '<a href="' . $fileurl . '">' . str_replace("\n","<br />", strip_tags($val) ) . '</a>' . '</div></div>' . "\n";

			}
			elseif ($name=='page') {  // special field: page 
			
					echo '<div class="showformfield" style="color:#3C575B;"><div class="L">';
					_e('Submitted via page', 'cforms');
					echo 	'</div><div class="R">' . str_replace("\n","<br />", strip_tags($val) ) . '</div></div>' . "\n";

					echo '<div class="showformfield" style="margin-bottom:10px;color:#3C575B;"><div class="L">';
					_e('IP address', 'cforms');
					echo 	'</div><div class="R"><a href="http://geomaplookup.cinnamonthoughts.org/?ip='.$entry->ip.'" title="'.__('IP Lookup', 'cforms').'">'.$entry->ip.'</a></div></div>' . "\n";					


			} elseif ( strpos($name,'Fieldset')!==false ) {
			
					echo '<div class="showformfield tfieldset"><div class="L">&nbsp;</div><div class="R">' . strip_tags($val)  . '</div></div>' . "\n";
			
			} else {
						
					echo '<div class="showformfield"><div class="L">' . $name . '</div>' .
							'<div id="'.$entry->f_id.'" class="R editable">' . str_replace("\n","<br />", strip_tags($val) ) . '</div></div>' . "\n";

			}

		}
		echo '</div>';

	else : ?>
	
		<p align="center"><?php _e('Sorry, data not found. Please refresh your data table.', 'cforms') ?></p>
		</div>

	<?php endif;
	
}
?>
