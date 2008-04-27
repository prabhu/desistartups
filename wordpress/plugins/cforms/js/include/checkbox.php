<?php require_once('../../../../../wp-config.php'); ?>

<form method="post"/>

	<label for="cf_edit_label_left"><?php _e('Field label left of the checkbox...', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label_left" name="cf_edit_label_left" value="">

	<label for="cf_edit_label_right"><?php _e('...or define a field label right of the checkbox', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label_right" name="cf_edit_label_right" value="">

	<label for="cf_edit_title"><?php _e('Input field title', 'cforms'); ?></label>
	<input type="text" id="cf_edit_title" name="cf_edit_title" value="">

	<label for="cf_edit_customerr"><?php _e('Custom error message', 'cforms'); ?></label>
	<input type="text" id="cf_edit_customerr" name="cf_edit_customerr" value="">

</form>
