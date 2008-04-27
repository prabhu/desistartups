<?php

require_once('../../../../../wp-config.php');

if ( !current_user_can('edit_users') )
	wp_die("access restricted.");

### mini firewall

global $wpdb;

$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';


### get form names
for ($i=1; $i <= get_option('cforms_formcount'); $i++){
	$n = ( $i==1 )?'':$i; 
	$fnames[$i]=stripslashes(get_option('cforms'.$n.'_fname'));
}


$format = $_GET['format'];
$sub_ids = $_GET['ids'];
$sortBy = $_GET['sortBy'];
$sortOrder = $_GET['sortOrder'];

$qtype = $_GET['qtype'];
$query = $_GET['query'];

if ( $query<>'' && $query<>'undefined' && $sub_ids=='all' )
	$where = "AND $qtype LIKE '%$query%'";
else
	$where = '';

if ( !$sortBy || $sortBy=='undefined' )
	$sortBy = 'id';
if ( !$sortOrder || $sortOrder=='undefined' )
	$sortOrder = 'desc';

if ($sub_ids<>'') {

	if ( $sub_ids<>'all' )
		$in_list = 'AND sub_id in ('.substr($sub_ids,0,-1).')';
	else
		$in_list = '';
	
	$sql = "SELECT *, form_id FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id=id $where $in_list ORDER BY $sortBy $sortOrder, f_id ASC";
	$entries = $wpdb->get_results($sql);
	
	if ( $format=='xml' )
		$buffer = getXML($entries);
	else if ( $format=='csv' )
		$buffer = getCSVTAB($entries);
	else if ( $format=='tab' )
		$buffer = getCSVTAB($entries,'tab');
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: text/download");
	header("Content-Type: text/$format");
	header("Content-Disposition: attachment; filename=\"formdata." . $format . "\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " .(string)(strlen($buffer)) );
	print $buffer;	
	exit();

}

function getCSVTAB($arr,$format='csv'){
	global $fnames;

	$br="\n";
	$buffer='';

	$sub_id='';
	$format = ($format=="csv")?",":"\t";

	foreach ($arr as $entry){
		if ( $entry->field_name=='page' || strpos($entry->field_name,'Fieldset')!==false )
			continue;

		$n = ( $entry->form_id=='' )?'1':$entry->form_id;	

		if( $sub_id<>$entry->sub_id ){
		
			if ( $sub_id<>'' ) 
				$buffer = substr($buffer,0,-1) . $br;
				
			$sub_id = $entry->sub_id;	
			$buffer .= __('Form','cforms').': "' . $fnames[$n]. '"'. $format .'"'. $entry->sub_date .'"' . $format;
		}

		$buffer .= '"' . str_replace('"','""', utf8_decode(stripslashes($entry->field_val))) . '"' . $format;
	}
	return $buffer;
}
	
function getXML($arr){
	global $fnames;

	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	
	$sub_id ='';
	foreach ($arr as $entry) {
		if ( $entry->field_name=='page' || strpos($entry->field_name,'Fieldset')!==false )
			continue;
			
		$n = ( $entry->form_id=='' )?'1':$entry->form_id;	
		if( $sub_id<>$entry->sub_id ){
		
			if ( $sub_id<>'' ) 
				$xml .= "</entry>\n";

			$xml .= '<entry form="'.utf8_encode( $fnames[$n]).'" date="'.utf8_encode( $entry->sub_date )."\">\n";				
			$sub_id = $entry->sub_id;	
		}		
		$xml .= "<data><![CDATA[".utf8_encode( stripslashes($entry->field_val) )."]]></data>\n";
	}
	if($sub_id<>'')
	 $xml .= "</entry>\n";
	return $xml;
}
?>
