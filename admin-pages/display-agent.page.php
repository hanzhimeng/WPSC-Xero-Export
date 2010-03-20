<?php
/**
 * This is the admin page display
 *not how your supposed to add meta boxes,,, 
 * @package moduleSkeleton
 */
function wpsc_display_agent_pages(){
	global $wpdb, $wpsc_query;
	$products = $wpdb->get_results("SELECT * FROM ".WPSC_TABLE_PRODUCT_LIST." ORDER BY id ASC", ARRAY_A);
?>
<h2><?php _e('Agent Booking Page','wpsc'); ?></h2>
	<div class="metabox-holder" style="width: 95%;">
		<div class='postbox'>
			<div class='agent-booking-holder'>
				<select name='agency_name'>
					<option>Red Seven</option>
					<option>Chillisauce</option>
					<option>Hen Heaven</option>
					<option>Eclipse</option>
					<option>The Stag Company</option>
					<option>Freedom</option>
				</select>
				<table>
				<tr>
					<th>Title</th><th>Price</th><th>Quantity</th>
				</tr>
				<?php
					foreach ($products as $product){
						echo "<tr>";
						echo "<td>".$product['name']."</td>";
						echo "<td>".nzshpcrt_currency_display($product['price'],1)."</td>";
						echo "<td><input type='text' size='2'><input type='hidden' name='pid' value='".$product['id']."'</td>";
						echo "</tr>";
					}
					//echo "<pre>".print_r($products,1)."</pre>";
				?>
				</table>

				<input type='button' class='button-primary' value='Book'>
			</div>
		</div>
		
	</div>
<?php

}

function wpsc_agent_css(){
?>
<style>
div.agent-booking-holder {
	padding:10px 20px;
}
</style>
<?php
}

?>
