<?php
/**
 * This is the admin page display
 *not how your supposed to add meta boxes,,, 
 * @package moduleSkeleton
 */
function wpsc_display_xero_export_pages(){
?>
<h2><?php _e('WPSC Xero Export','wpsc'); ?></h2>
	<div>
		<div>
			<form method='get' action=''>
				<input type='hidden' name='xero_submit' value='true'>
				<table class='form-table'>
				<tr><th><label for='inclusive_tax'>Price include Tax?</label></th><td><input id='inclusive_tax' type='radio' name='tax' value='yes'> Yes <input type='radio' name='tax' value='No'> No</td></tr>
				<tr><th>Date Range</th><td>From <input type='text' id='startdate' name='start_date'> to <input type='text' id='enddate' name='end_date'></td></tr>
				<tr><th><label for='xero_code'>Xero Account Code</label></th><td><input type='text' id='xero_code' class='small-text' name='xero_code' size='3'></td></tr>
				<tr><td><input type='submit' value='Generate' class='button-primary'></td></tr>
				</table>
			</form>
		</div>
	</div>
<?php

}

?>