<?php
/*
Upgrade Name: WPSC Agent Booking
Upgrade URI: http://www.allenhan.com
Description: A agent booking module for wpec
Version: 0.1
Author: Allen Han
Author URI: http://www.allenhan.com

*/

//check to see whether the user is an admin or not.
if (is_admin()) {
	//Add admin Module page
	require_once("admin-pages/display-agent.page.php");

	/**
	 * Description Function to add admin Pages make sure not to use these generic funciton names, as it will cause a 		 * conflict, you should change this function name and the wpsc_display_admin_pages function name
	 * @access public - admin pages
	 *
	 * @param page hooks array
	 * @param base page
	 * @return new page hooks
 	 */
	function wpsc_add_modules_admin_pages($page_hooks, $base_page) {
		$page_hooks[] =  add_submenu_page($base_page, __('Agents','wpsc'),  __('Agents','wpsc'), 7, 'wpsc-module-admin', 'wpsc_display_agent_pages');
		return $page_hooks;
	}
	add_filter('wpsc_additional_pages', 'wpsc_add_modules_admin_pages',10, 2);
	add_action('admin_head', 'wpsc_agent_css');
}

?>
