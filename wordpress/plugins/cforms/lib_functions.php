<?php
function download_cforms() {

	global $wpdb;
	$br="\n";
	$buffer='';
	
	if( isset($_REQUEST['savecformsdata']) ) {		
		// current form
		$noDISP = '1'; $no='';
		if( $_REQUEST['noSub']<>'1' )
			$noDISP = $no = $_REQUEST['noSub'];
			
		$buffer .= 'cf:'.get_option('cforms'.$no.'_count_fields') . $br;
		$buffer .= 'ff:';
		for ( $i=1; $i<=get_option('cforms'.$no.'_count_fields'); $i++)  //now delete all fields from last form
		$buffer .= get_option('cforms'.$no.'_count_field_'.$i)."+++";
		
		$buffer .= $br;
		$buffer .= 'rq:'.get_option('cforms'.$no.'_required') . $br;
		$buffer .= 'er:'.get_option('cforms'.$no.'_emailrequired') . $br;
		
		$buffer .= 'ac:'.get_option('cforms'.$no.'_confirm') . $br;
		$buffer .= 'jx:'.get_option('cforms'.$no.'_ajax') . $br;
		$buffer .= 'fn:'.get_option('cforms'.$no.'_fname') . $br;
		$buffer .= 'cs:'.get_option('cforms'.$no.'_csubject') . $br;
		$buffer .= 'cm:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_cmsg')) . $br;
		$buffer .= 'em:'.get_option('cforms'.$no.'_email') . $br;
		
		$buffer .= 'sj:'.get_option('cforms'.$no.'_subject') . $br;
		$buffer .= 'su:'.get_option('cforms'.$no.'_submit_text') . $br;
		$buffer .= 'sc:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_success')) . $br;
		$buffer .= 'fl:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_failure')) . $br;
		$buffer .= 'wo:'.get_option('cforms'.$no.'_working') . $br;
		$buffer .= 'pp:'.get_option('cforms'.$no.'_popup') . $br;
		$buffer .= 'sp:'.get_option('cforms'.$no.'_showpos') . $br;
		$buffer .= 'rd:'.get_option('cforms'.$no.'_redirect') . $br;
		$buffer .= 'rp:'.get_option('cforms'.$no.'_redirect_page') . $br;
		$buffer .= 'hd:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_header')) . $br;
		$buffer .= 'pc:'.get_option('cforms'.$no.'_space') . $br;
		$buffer .= 'at:'.get_option('cforms'.$no.'_noattachments') . $br;
		$buffer .= 'ud:'.get_option('cforms'.$no.'_upload_dir') . $br;
		$buffer .= 'ue:'.get_option('cforms'.$no.'_upload_ext') . $br;
		$buffer .= 'us:'.get_option('cforms'.$no.'_upload_size') . $br;
		$buffer .= 'ar:'.get_option('cforms'.$no.'_action') . $br;
		$buffer .= 'ap:'.get_option('cforms'.$no.'_action_page') . $br;
		$buffer .= 'bc:'.get_option('cforms'.$no.'_bcc') . $br;
		$buffer .= 'ch:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_cmsg_html')) . $br;
		$buffer .= 'hh:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_header_html')) . $br;
		$buffer .= 'fd:'.get_option('cforms'.$no.'_formdata') . $br;
		$buffer .= 'tr:'.get_option('cforms'.$no.'_tracking') . $br;
		$buffer .= 'fm:'.get_option('cforms'.$no.'_fromemail') . $br;
		$buffer .= 'tf:'.get_option('cforms'.$no.'_tellafriend') . $br;
		$buffer .= 'db:'.get_option('cforms'.$no.'_dashboard') . $br;
		$buffer .= 'mx:'.get_option('cforms'.$no.'_maxentries') . $br;
		$buffer .= 'lt:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_limittxt')) . $br;
		$buffer .= 'cn:'.get_option('cforms'.$no.'_customnames') . $br;
			
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment; filename=\"formconfig.txt\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " .(string)(strlen($buffer)) );
		print $buffer;
		exit();

	} ### saving form settings
	
}

### Add cforms menu to admin
function cforms_menu() {
	global $plugindir, $wpdb, $submenu;

	$tablesup = ($wpdb->get_var("show tables like '$wpdb->cformssubmissions'") == $wpdb->cformssubmissions)?true:false;
	
	if (function_exists('add_menu_page')) {
		add_menu_page(__('cformsII', 'cforms'), __('cformsII', 'cforms'), 'manage_cforms', $plugindir.'/cforms-options.php');
	}
	elseif (function_exists('add_management_page')) {
		add_management_page(__('cformsII', 'cforms'), __('cformsII', 'cforms'), 'manage_cforms', $plugindir.'/cforms-options.php');
	}
	
	if (function_exists('add_submenu_page')) {
		add_submenu_page($plugindir.'/cforms-options.php', __('Global Settings', 'cforms'), __('Global Settings', 'cforms'), 'manage_cforms', $plugindir.'/cforms-global-settings.php');
		if ( ($tablesup || isset($_REQUEST['cforms_database'])) && !isset($_REQUEST['deletetables']) )
			add_submenu_page($plugindir.'/cforms-options.php', __('Tracking', 'cforms'), __('Tracking', 'cforms'), 'track_cforms', $plugindir.'/cforms-database.php');
		add_submenu_page($plugindir.'/cforms-options.php', __('Styling', 'cforms'), __('Styling', 'cforms'), 'manage_cforms', $plugindir.'/cforms-css.php');
		add_submenu_page($plugindir.'/cforms-options.php', __('Help!', 'cforms'), __('Help!', 'cforms'), 'manage_cforms', $plugindir.'/cforms-help.php');
	}
}

function cforms_init() {
	global $wpdb;

	$plugindir   = dirname(plugin_basename(__FILE__));
		
	$role = get_role('administrator');
	if(!$role->has_cap('manage_cforms')) {
		$role->add_cap('manage_cforms');
	}
	if(!$role->has_cap('track_cforms')) {
		$role->add_cap('track_cforms');
	}
	
	### alter tracking tables if needed
	$tables = $wpdb->get_col("SHOW TABLES FROM `" . DB_NAME . "` LIKE '$wpdb->cformssubmissions'",0);

	if( $tables[0]==$wpdb->cformssubmissions ) {
		$columns = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->cformssubmissions}");
		if ( $columns[2]->Field == 'date' ) 
			$result = $wpdb->query("ALTER TABLE `{$wpdb->cformssubmissions}` CHANGE `date` `sub_date` TIMESTAMP");	
	}

	### try to adjust cforms.js automatically
	$jsContent = $jsContentNew = '';
	if ( $fhandle = fopen(dirname(__FILE__).'/js/cforms.js', "r") ) {
		$jsContent = fread($fhandle, filesize(dirname(__FILE__).'/js/cforms.js'));
	    fclose($fhandle);

		$URIprefix='';
		if ( ($x=strpos($_SERVER['REQUEST_URI'],'/wp-admin/')) > 0 )
			$URIprefix = substr($_SERVER['REQUEST_URI'],0,$x);

		$pathToAjax = '\''.$URIprefix.'/wp-content/plugins/'.$plugindir. '/lib_ajax.php\'';
        $jsContentNew = str_replace('\'/wp-content/plugins/cforms/lib_ajax.php\'',$pathToAjax,$jsContent);
	}
	if ( $jsContentNew<>'' && $jsContentNew<>$jsContent && ($fhandle = fopen(dirname(__FILE__).'/js/cforms.js', "w")) ) {
	    fwrite($fhandle, $jsContentNew);
	    fclose($fhandle);
	}


}

### some css for arranging the table fields in wp-admin
function cforms_options_page_style() {  
	global $cforms_root;	
	echo	'<link rel="stylesheet" type="text/css" href="' . $cforms_root . '/cforms-admin.css" />' . "\n" .
			'<script type="text/javascript" src="' . $cforms_root . '/js/jquery.js"></script>' . "\n" .
			'<script type="text/javascript" src="' . $cforms_root . '/js/interface.js"></script>' . "\n".
			'<script type="text/javascript" src="' . $cforms_root . '/js/cformsadmin.js"></script>' . "\n";
}

### footer unbder all options pages
function cforms_footer() {
	global $localversion;
?>	<p style="padding-top:50px; font-size:11px; text-align:center;">
		<em>
			<?php echo sprintf(__('For more information and support, visit the %s support forum %s. ', 'cforms'),'<strong>cforms</strong> <a href="http://www.deliciousdays.com/cforms-forum/" title="cforms support forum">','</a>') ?>
			<?php _e('Translation provided by Oliver Seidel, for updates <a href="http://deliciousdays.com/cforms-plugin">check here.</a>', 'cforms') ?>
		</em>
	</p>

	<p align="center">Version v<?php echo $localversion; ?></p>
<?php 
}
?>
