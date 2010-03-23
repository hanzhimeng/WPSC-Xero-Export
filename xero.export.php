<?php
/*
Upgrade Name:WPSC to Xero
Upgrade URI: http://www.allenhan.com
Description: Export your WPSC sales log to Xero
Version: 0.1
Author: Allen Han
Author URI: http://www.allenhan.com
*/

/*
	This file is part of WPSC to Xero Exporter.

    WPSC to Xero Exporter is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WPSC to Xero Exporter is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with WPSC to Xero Exporter.  If not, see <http://www.gnu.org/licenses/>.
	
*/

//check to see whether the user is an admin or not.
if (is_admin()) {
	//Add admin Module page
	require_once("admin-pages/xero.export.page.php");
	
	/**
	 * Description Function to add admin Pages make sure not to use these generic funciton names, as it will cause a 		 * conflict, you should change this function name and the wpsc_display_admin_pages function name
	 * @access public - admin pages
	 *
	 * @param page hooks array
	 * @param base page
	 * @return new page hooks
 	 */
	function wpsc_add_modules_xero_export_pages($page_hooks, $base_page) {
		$page_hooks[] =  add_submenu_page($base_page, __('Xero Export','wpsc'),  __('Xero Export','wpsc'), 7, 'wpsc-module-xero-export', 'wpsc_display_xero_export_pages');
		return $page_hooks;
	}
	wp_enqueue_script('datepicker',WPSC_URL.'/js/ui.datepicker.js', array('jquery'), '1.0.0');
	function wpsc_xero_export_head(){
	?>
	<script type="text/javascript">
		jQuery(document).ready(
			function(){
				jQuery('#startdate').datepicker({dateFormat: "dd/mm/yy"});
				jQuery("#enddate").datepicker({dateFormat: "dd/mm/yy"});
			}
		);
		
	</script>
	<link rel="stylesheet" href = '<?php echo WPSC_URL; ?>/admin_2.7.css' type="text/css" media="all" />
	<?php
	}
	
	add_action('admin_head', 'wpsc_xero_export_head');
	add_filter('wpsc_additional_pages', 'wpsc_add_modules_xero_export_pages',10, 2);

	if ($_GET['xero_submit'] == 'true'){	

		global $wpdb;
		$accountCode = $_GET['xero_code'];
		$inclusive_tax = $_GET['tax'];
		
		$startdate = $_GET['start_date'];
		$startdate = explode("/", $startdate);
		$start_timestamp = mktime(0, 0, 0, $startdate[1], $startdate[0], $startdate[2]);
		
		$enddate = $_GET['end_date'];
		$enddate = explode("/", $enddate);
		$end_timestamp = mktime(0, 0, 0, $enddate[1], $enddate[0], $enddate[2]);
		
		$form_sql = "SELECT * FROM `".WPSC_TABLE_CHECKOUT_FORMS."` WHERE `active` = '1';";
		$form_data = $wpdb->get_results($form_sql,ARRAY_A);
	
		$data = $wpdb->get_results("SELECT * FROM `".WPSC_TABLE_PURCHASE_LOGS."` ORDER BY `date` ASC",ARRAY_A);
		//exit('---><pre>'.print_r($data, true).'</pre>');  
		header('Content-Type: text/csv');
		header('Content-Disposition: inline; filename="Purchase Log '.date("M-d-Y", $start_timestamp).' to '.date("M-d-Y", $end_timestamp).'.csv"');	  
		$output .= "ContactName,EmailAddress,POAddressLine1,POAddressLine2,POAddressLine3,POAddressLine4,POCity,PORegion,POPostalCode,POCountry,InvoiceNumber,Reference,InvoiceDate,DueDate,SubTotal,TotalTax,Total,Description,Quantity,UnitAmount,AccountCode,TaxType,TaxAmount,TrackingName1,TrackingOption1,TrackingName2,TrackingOption2\n";
		foreach((array)$data as $purchase) {
		    $country_sql = "SELECT * FROM `".WPSC_TABLE_SUBMITED_FORM_DATA."` WHERE `log_id` = '".$purchase['id']."' AND `form_id` = '".get_option('country_form_field')."' LIMIT 1";
		    $country_data = $wpdb->get_results($country_sql,ARRAY_A);
		    $country = $country_data[0]['value'];
	   
		    $quantity = $wpdb->get_var("SELECT SUM(quantity) FROM `".WPSC_TABLE_CART_CONTENTS."` WHERE `purchaseid`=".$purchase['id']);
		    //echo "SELECT SUM(quantity) FROM `".WPSC_TABLE_CART_CONTENTS."` WHERE `purchaseid`=".$purchase['id'];
	
	/* 	    echo "<pre>".print_r($purchase,1)."</pre>"; */
		    foreach($form_data as $form_field) {
		    	$collected_data_sql = "SELECT * FROM `".WPSC_TABLE_SUBMITED_FORM_DATA."` WHERE `log_id` = '".$purchase['id']."' AND `form_id` = '".$form_field['id']."' LIMIT 1";
		    	$collected_data = $wpdb->get_results($collected_data_sql,ARRAY_A);
		    	$collected_data = $collected_data[0];
		    	switch ($form_field['name']){
		    		case "First Name":
		    		if ($form_field['id'] == $collected_data['form_id'])
		    			$firstname = ucfirst($collected_data['value']);
		    		break;
		    		
		    		case "Last Name":
		    		if ($form_field['id'] == $collected_data['form_id'])
		    			$lastname = ucfirst($collected_data['value']);
		    		break;
		    		
		    		case "Email":
		    		if ($form_field['id'] == $collected_data['form_id'])
		    			$email = $collected_data['value'];
		    		break;
		    		
		    		case "Address 1":
		    		if ($form_field['id'] == $collected_data['form_id'])
		    			$address1 = ucfirst(trim($collected_data['value']));
		    		break;
		    		
		    		case "City":
		    		if ($form_field['id'] == $collected_data['form_id'])
		    			$city = ucfirst($collected_data['value']);
		    		break;
		    		
		    		case "Postal Code":
		    		if ($form_field['id'] == $collected_data['form_id'])
		    			$postcode = ucfirst($collected_data['value']);
		    		break;
		    	}
		    }
		    $cartsql = "SELECT * FROM `".WPSC_TABLE_CART_CONTENTS."` WHERE `purchaseid`=".$purchase['id']."";
		    $cart = $wpdb->get_results($cartsql,ARRAY_A) ; 
		    //exit(nl2br(print_r($cart,true)));
		    foreach ($cart as $item) {
	/* 	    	echo "<pre>".print_r($item,1)."</pre>"; */
		    	$product = $wpdb->get_row("SELECT * FROM `".WPSC_TABLE_PRODUCT_LIST."` WHERE `id`=".$item['prodid']." LIMIT 1",ARRAY_A);
		    	$output .= "\"".$firstname." ".$lastname."\",";
		    	$output .= "\"".$email."\",";
		    	$output .= "\"".$address1."\",";
		    	$output .= "\"".$address2."\",";
		    	$output .= "\"".$address3."\",";
		    	$output .= "\"".$address4."\",";
		    	$output .= "\"".$city."\",";
		    	$output .= "\"".$region."\","; //Region still needs to be addressed
		    	$output .= "\"".$postcode."\",";
		    	$output .= "\"".wpsc_get_country($purchase['shipping_country'])."\",";
		    	$output .= "\"".$purchase['id']."\",";
		    	$output .= "\"".$reference."\",";
		    	$output .= "\"".date("d/m/Y",$purchase['date'])."\",";
		    	$output .= "\"".date("d/m/Y",$purchase['date'])."\",";
		    	$total = $item["price"] * $item['quantity'];
			    $subtotal = $total;
			    $tax = $subtotal*get_option("country_tax");
			    $unitamount = $item["price"];
			    if ($inclusive_tax == true) {
			    	$subtotal = round($total / 1.175, 2);
			    	$tax = $total - $subtotal;
			    	$unitamount = round ($subtotal/$item['quantity'], 6);
			    }
		    	$output .= "\""."\",";
		    	$output .= "\""."\",";
		    	$output .= "\""."\",";
		    	$output .= "\"".$product['name']."\",";
		    	$output .= "\"".$item['quantity'] ."\",";
		    	$output .= "\"".$unitamount."\",";
		    	$output .= "\"".$accountCode."\",";
		    	$output .= "\"\",";
		    	$output .= "\"".$tax."\",";
		    	$output .= "\"".$trackingname1."\",";
		    	$output .= "\"".$trackingoption1."\",";
		    	$output .= "\"".$trackingname2."\",";
		    	$output .= "\"".$trackingoption2."\"";
		    	$output .= "\n";
		    }
		}
		echo $output;
		//echo nl2br($output);
		exit();
	}
}

?>
