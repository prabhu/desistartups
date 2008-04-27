<?php require_once('../../../../../wp-config.php'); ?>

<form method="post"/>

	<label for="cf_edit_label"><?php _e('Text', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label" name="cf_edit_label" value="">

	<label for="cf_edit_css"><?php _e('CSS class', 'cforms'); ?></label>
	<input type="text" id="cf_edit_css" name="cf_edit_css" value="">

	<label for="cf_edit_style"><?php echo sprintf(__('Inline style (e.g. %s)', 'cforms'),'color:red; font-size:11px;'); ?></label>
	<input type="text" id="cf_edit_style" name="cf_edit_style" value="">

</form>
