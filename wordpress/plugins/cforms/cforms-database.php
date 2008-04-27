<?php

/*
please see cforms.php for more information
*/

load_plugin_textdomain('cforms');

$plugindir   = dirname(plugin_basename(__FILE__));
$cforms_root = get_settings('siteurl') . '/wp-content/plugins/'.$plugindir;

### db settings
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';
		
### Check Whether User Can Manage Database
if(!current_user_can('manage_cforms') && !current_user_can('track_cforms')) {
	die(__('Access Denied','cforms'));
}


// if all data has been erased quit
if ( get_option('cforms_formcount') == '' ){
	?>
	<div class="wrap">
	<h2><?php _e('All cforms data has been erased!', 'cforms') ?></h2>
	<p><?php _e('Please go to your <strong>Plugins</strong> tab and either disable the plugin, or toggle its status (disable/enable) to revive cforms!', 'cforms') ?></p>
	</div>
	<?php
	return;
}


?>
<div class="wrap"><a id="top"></a>
<img src="<?php echo $cforms_root; ?>/images/cfii.gif" alt="" align="right"/><img src="<?php echo $cforms_root; ?>/images/p3-title.jpg" alt=""/>

	<p><?php _e('All your recorded form submissions are listed below. View individual entries or a whole bunch and download as XML, TAB or CSV formatted file. Attachments can be accessed in the details section (<strong>View records</strong>). When deleting entries, associated attachments will be removed, too! ', 'cforms') ?></p>

	<p class="ex" style="margin-bottom:30px;"><?php _e('Details section: Fields with a grey background can be clicked on and edited!', 'cforms') ?></p>

	<div id="ctrlmessage"></div>
	<div class="bborderx"><table id="flex1" style="display:none"></table></div>
	<div id="entries"></div>
	<div id="geturl" title="<?php echo $cforms_root; ?>/js/include/"></div>

<script type="text/javascript">
cforms("#flex1").flexigrid ( {
	url: '<?php echo $cforms_root.'/js/include/lib_database_getdata.php'; ?>',
	dataType: 'xml',
	colModel : [
		{display: '#', name : 'id', width : 40, sortable : true, align: 'center'},
		{display: '<?php _e('Form Name','cforms'); ?>', name : 'form_id', width : 240, sortable : true, align: 'center'},
		{display: '<?php _e('e-mail Address','cforms'); ?>', name : 'email', width : 200, sortable : true, align: 'center'},
		{display: '<?php _e('Date','cforms'); ?>', name : 'sub_date', width : 160, sortable : true, align: 'center'},
		{display: '<?php _e('IP','cforms'); ?>', name : 'ip', width : 100, sortable : true, align: 'center'}
		],
	buttons : [
		{name: '<?php _e('View records','cforms'); ?>', bclass: 'add', onpress : cf_tracking_view},
		{name: '<?php _e('Delete records','cforms'); ?>', bclass: 'delete', onpress : function (){cforms('#cf_delete_dialog').jqmShow();} },
		{name: '<?php _e('Download records','cforms'); ?>', bclass: 'dl', onpress : function (){cforms('#cf_dl_dialog').jqmShow();}},
		{separator: true}
		],
	searchitems : [
		{display: '<?php _e('Form Name','cforms'); ?>', name : 'form_id'},
		{display: '<?php _e('e-mail Address','cforms'); ?>', name : 'email', isdefault: true},
		{display: '<?php _e('Date','cforms'); ?>', name : 'sub_date'},
		{display: '<?php _e('IP','cforms'); ?>', name : 'ip'}
		],
	sortname: "id",
	sortorder: "desc",
	usepager: true,
	title: '<?php _e('Form Submissions','cforms'); ?>',
	errormsg: '<?php _e('Connection Error','cforms'); ?>',
	pagestat: '<?php _e('Displaying {from} to {to} of {total} items','cforms'); ?>',
	procmsg: '<?php _e('Processing, please wait ...','cforms'); ?>',
	nomsg: '<?php _e('No items','cforms'); ?>',
	pageof: '<?php _e('Page {%1} of','cforms'); ?>',
	useRp: true,
	blockOpacity: 0.9,
	rp: 30,
	rpOptions: [10,30,50,100,200],
	showTableToggleBtn: true,
	width: 820,
	height: 250 });
</script>
<?php
cforms_footer();
?>
</div> <!-- wrap -->

<?php
add_action('admin_footer', 'insert_cfmodal_tracking');
function insert_cfmodal_tracking(){
	global $cforms_root,$noDISP;
?>
	<div class="jqmWindow" id="cf_delete_dialog">
		<div class="cf_ed_header jqDrag"><?php _e('Please Confirm','cforms'); ?></div>
		<div class="cf_ed_main">
			<form name="installpreset" method="POST">		
				<div id="cf_target" style="text-align:center;font-weight:bold;margin:10px 0 0 0;"><?php _e('Are you sure you want to delete the record(s)?','cforms'); ?></div>
				<div class="controls"><a href="#" id="okDelete" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_ok.gif" alt="<?php _e('Install', 'cforms') ?>" title="<?php _e('OK', 'cforms') ?>"/></a><a href="#" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_cancel.gif" alt="<?php _e('Cancel', 'cforms') ?>" title="<?php _e('Cancel', 'cforms') ?>"/></a></div>
			</form>
		</div>
	</div>
	<div class="jqmWindow" id="cf_dl_dialog">
		<div class="cf_ed_header jqDrag"><?php _e('Please Confirm','cforms'); ?></div>
		<div class="cf_ed_main">
			<form name="installpreset" method="POST">		
				<div id="cf_target" style="text-align:center;font-weight:bold;margin:10px 0 0 0;">
					<select id="pickDLformat" name="format">
					<option value="xml">&nbsp;&nbsp;&nbsp;XML&nbsp;&nbsp;&nbsp;</option>
					<option value="csv">&nbsp;&nbsp;&nbsp;CSV&nbsp;&nbsp;&nbsp;</option>
					<option value="tab">&nbsp;&nbsp;&nbsp;TAB&nbsp;&nbsp;&nbsp;</option>
					</select>
					<br />
					<?php echo sprintf(__('Please pick a format!','cforms')); ?>
				</div>
				<div class="controls"><a href="#" id="okDL" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_ok.gif" alt="<?php _e('Install', 'cforms') ?>" title="<?php _e('OK', 'cforms') ?>"/></a><a href="#" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_cancel.gif" alt="<?php _e('Cancel', 'cforms') ?>" title="<?php _e('Cancel', 'cforms') ?>"/></a></div>
			</form>
		</div>
	</div>
<?php
}
?>

