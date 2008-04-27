<?php

require_once('../../../../../wp-config.php');

if ( !current_user_can('edit_users') )
	wp_die("access restricted.");

global $wpdb;

$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

function countRec() {
	global $wpdb;
	$sql = "SELECT count(id) FROM {$wpdb->cformssubmissions}";
	return $wpdb->get_var($sql);
}

$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];

$qtype = $_POST['qtype'];
$query = $_POST['query'];

if ( $query<>'' )
	$where = "WHERE $qtype LIKE '%$query%'";
else
	$where = '';

if (!$sortname)
	$sortname = 'id';
if (!$sortorder) $sortorder = 'desc';
	$sort = "ORDER BY $sortname $sortorder";
if (!$page)
	$page = 1;
if (!$rp)
	$rp = 10;

$start = (($page-1) * $rp);
$limit = "LIMIT $start, $rp";

$total = countRec();

for ($i=1; $i <= get_option('cforms_formcount'); $i++){
	$n = ( $i==1 )?'':$i; 
	$fnames[$i]=stripslashes(get_option('cforms'.$n.'_fname'));
}

$sql="SELECT * FROM {$wpdb->cformssubmissions} $where $sort $limit";
$result = $wpdb->get_results($sql);


header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/xml");

$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$xml .= "<rows>";
$xml .= "<page>$page</page>";
$xml .= "<total>$total</total>";

foreach ($result as $entry) {
	$n = ( $entry->form_id=='' )?'1':$entry->form_id;	
	$xml .= "<row id='".$entry->id."'>";
	$xml .= "<cell><![CDATA[".$entry->id."]]></cell>";
	$xml .= "<cell><![CDATA[".utf8_encode( $fnames[$n] )."]]></cell>";
	$xml .= "<cell><![CDATA[".utf8_encode( $entry->email )."]]></cell>";
	$xml .= "<cell><![CDATA[".utf8_encode( $entry->sub_date )."]]></cell>";
	$xml .= "<cell><![CDATA[".utf8_encode( $entry->ip )."]]></cell>";
	$xml .= "</row>";
}

$xml .= "</rows>";
echo $xml;
?>
