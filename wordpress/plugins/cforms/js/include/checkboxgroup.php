<?php require_once('../../../../../wp-config.php'); ?>

<form method="post"/>

	<label for="cf_edit_label_group"><?php _e('Field label', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label_group" name="cf_edit_label_group" value="">

	<div class="cf_edit_groups_header">
		<span><?php _e('Checkbox option', 'cforms'); ?></span>
		<span><?php _e('Optional value', 'cforms'); ?></span>
		<span class="cf_br"><?php _e('Line break', 'cforms'); ?></span>
	</div>
	
	<div id="cf_edit_groups">
	</div>
	<div class="add_group_item"><a href="#" id="add_group_button" class="cf_edit_plus"></a></div>
	
	<label style="clear:left; padding-top:5px;" for="cf_edit_title"><?php _e('Input field title', 'cforms'); ?></label>
	<input type="text" id="cf_edit_title" name="cf_edit_title" value="">

	<label for="cf_edit_customerr"><?php _e('Custom error message', 'cforms'); ?></label>
	<input type="text" id="cf_edit_customerr" name="cf_edit_customerr" value="">

</form>
