<?php

require_once('../../../../../wp-config.php');

if ( !current_user_can('edit_users') )
	wp_die("access restricted.");

global $wpdb;

$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

$sub_ids = $_POST['ids'];
$qtype = $_POST['qtype'];
$query = $_POST['query'];

if ( $query<>'' && $query<>'undefined' && $sub_ids=='all' )
	$doquery = "AND $qtype LIKE '%$query%'";
else
	$doquery = '';

if ( $sub_ids<>'' ){

	if ( $sub_ids=='all' )
		$all_entries[0] = 'all';
	else
		$all_entries = explode(',',substr($sub_ids,0,-1));

	foreach ($all_entries as $entry) :
		$entry = (int) $entry;

		if ($entry <> 'all')
			$sub_id = "sub_id = '$entry'";
		else
			$sub_id = '1';

		$sql = "SELECT field_val,form_id,sub_id FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE $sub_id $doquery AND id=sub_id AND field_name LIKE '%[*]%'";
		$filevalues = $wpdb->get_results($sql);

		$del='';
		$found = 0;
	
		foreach( $filevalues as $fileval ) {
	
			$temp = explode( '$#$',stripslashes(htmlspecialchars(get_option('cforms'.$fileval->form_id.'_upload_dir'))) );
			$fileuploaddir = $temp[0];
		
			$file = $fileuploaddir.'/'.$fileval->sub_id.'-'.$fileval->field_val;

			if ( $fileval->field_val <> '' ){
				if ( file_exists( $file ) ){
					unlink ( $file );
					$found = $found | 1;
				}
				else{
					$found = $found | 2;
				}
			}
			
		}

		if ($entry<>'all'){
			$whereD = "sub_id = '$entry'";
			$whereS = "id = '$entry'";
		}
		else{
			$whereD = '1';
			$whereS = '1';
		}
		
		if ( $query<>'' && $query<>'undefined' && $sub_ids=='all' )
			$dospecialquery = "AND sub_id IN ( SELECT id FROM {$wpdb->cformssubmissions} WHERE $qtype LIKE '%$query%') ";
		else
			$dospecialquery = '';

		$nuked = $wpdb->query("DELETE FROM {$wpdb->cformsdata} WHERE $whereD $dospecialquery");
		$nuked = $wpdb->query("DELETE FROM {$wpdb->cformssubmissions} WHERE $whereS $doquery");
	endforeach;
	
	 _e('Entries successfully removed from the tracking tables!', 'cforms');
}
?>
