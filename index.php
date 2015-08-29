<?php
/*
Plugin Name: Test Plugin
Description: Test Plugin
Version: 1.0
Plugin URI: http://example.com
Author: Test
Author URI: http://example.com
*/
define( 'PLUG_DIR_NAME', 'test_plugin' );
define( 'PLUG_DIR', WP_PLUGIN_DIR.'/'.PLUG_DIR_NAME );
define( 'PLUG_URL', WP_PLUGIN_URL.'/'.PLUG_DIR_NAME );

define('PLUG_VERSION', '1.0');

global $ajaxurl;
$ajaxurl = admin_url('admin-ajax.php');

global $TestPlugin;
$TestPlugin = new TestPlugin();

class TestPlugin
{
	function TestPlugin()
	{
		global $wp, $wpdb, $TestPlugin;
		//register_activation_hook(__FILE__, array('TestPlugin', 'activation'));
		//register_deactivation_hook(__FILE__, array('TestPlugin', 'deactivation'));
		//register_uninstall_hook(__FILE__, array('TestPlugin', 'uninstall'));
		
		add_action('admin_menu', array(&$this, 'admin_menu'), 27);
		require_once(PLUG_DIR . "/front-page.php");
	}
	function get_free_menu_position($start, $increment = 0.1)
	{
		foreach ($GLOBALS['menu'] as $key => $menu) {
			$menus_positions[] = $key;
		}
		if (!in_array($start, $menus_positions)) {
			return $start;
		} else {
			$start += $increment;
		}
		/* the position is already reserved find the closet one */
		while (in_array($start, $menus_positions)) {
			$start += $increment;
		}
		return $start;
	}
	/* Setting Capabilities for user */
	function capabilities()
	{
		$cap = array(
			'manage_appointments' => __('Manage Dashboard', MEMBERSHIP_TXTDOMAIN),
		);
		return $cap;
	}
	function admin_menu()
	{
		global $wp, $wpdb, $current_user;
		if (current_user_can('administrator'))
		{
			$caps = $this->capabilities();
			foreach ($caps as $cap => $capdescription) {
				$current_user->add_cap($cap);
			}
			unset($caps);
			unset($cap);
			unset($capdescription);
		}
		$place = $this->get_free_menu_position(26.1, .1);
		$menu_hook = add_menu_page('Appointments', 'Appointments', 'manage_appointments', 'appointments', array(&$this, 'route'), '', (string) $place);
	}
	function route()
	{
		global $wp, $wpdb, $TestPlugin;
		if( isset($_REQUEST['page']) )
		{
			
		} else {
			//No Action
		}
	}
	/**
	 * Get Current IP Address of User/Guest
	 */
	function get_ip_address()
	{
		$ipaddress = '';
		if ($_SERVER['HTTP_CLIENT_IP']) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		} else if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if ($_SERVER['HTTP_X_FORWARDED']) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		} else if ($_SERVER['HTTP_FORWARDED_FOR']) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} else if ($_SERVER['HTTP_FORWARDED']) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		} else if ($_SERVER['REMOTE_ADDR']) {
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		} else {
			$ipaddress = 'UNKNOWN';
		}
		return $ipaddress;
	}
	function activation()
	{
		$plugin_ver = get_option('plug_version');
		if (!isset($plugin_ver) || $plugin_ver == '')
		{
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			global $wp, $wpdb, $TestPlugin;
			$charset_collate = '';
			if ($wpdb->has_cap('collation')) {
				if (!empty($wpdb->charset)) {
					$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
				}
				if (!empty($wpdb->collate)) {
					$charset_collate .= " COLLATE $wpdb->collate";
				}
			}
			update_option('plug_version', PLUG_VERSION);
			
			//Table structure for `slots`
			/*$tbl_slots = $wpdb->prefix.'slots';
			$sql_table = "DROP TABLE IF EXISTS `{$tbl_slots}`;
			CREATE TABLE IF NOT EXISTS `{$tbl_slots}` (
			  `slot_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` bigint(20) unsigned NOT NULL,
			  `slot_val` TEXT,
			  `slot_val` TEXT,
			  `status` INT(1) NOT NULL DEFAULT '1',
			  PRIMARY KEY (`slot_id`),
			) ENGINE=InnoDB {$charset_collate};";
			dbDelta($sql_table);
			//Table structure for `appointments`
			$tbl_appointments = $wpdb->prefix.'appointments';
			$sql_table = "DROP TABLE IF EXISTS `{$tbl_appointments}`;
			CREATE TABLE IF NOT EXISTS `{$tbl_appointments}` (
			  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `pt_id` bigint(20) unsigned NOT NULL,
			  `dr_id` bigint(20) unsigned NOT NULL,
			  `slot_id` bigint(20) unsigned NOT NULL,
			  PRIMARY KEY (`ID`),
			) ENGINE=InnoDB {$charset_collate};";
			dbDelta($sql_table);/**/
			
			//Add Plugin Role
			$TestPlugin->add_user_role_and_capabilities();
		}
	}
	function deactivation()
	{
		
	}
	function uninstall()
	{
		global $wpdb;
		if (is_multisite()) {
			$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
			if ($blogs) {
				foreach ($blogs as $blog) {
					switch_to_blog($blog['blog_id']);

					delete_option('plug_version');
					$blog_tables = array(
						$wpdb->prefix . 'slots',
					);
					foreach ($blog_tables as $table) {
						$wpdb->query("DROP TABLE IF EXISTS $table ");
					}
				}
				restore_current_blog();
			}
		} else {
			delete_option('plug_version');
			$blog_tables = array(
				$wpdb->prefix . 'slots',
			);
			foreach ($blog_tables as $table) {
				$wpdb->query("DROP TABLE IF EXISTS $table ");
			}
		}
	}
	//Find match string in array
	function strpos($haystack, $needle, $offset = 0)
	{
		if (!is_array($needle)) {
			$needle = array($needle);
		}
		foreach ($needle as $query) {
			if (strpos($haystack, $query, $offset) !== false) {
				return true; // stop on first true result
			}
		}
		return false;
	}
	//Trim Array Values.
	function array_trim( $array )
	{
		if (is_array($array))
		{
			foreach ($array as $key => $value)
			{
				if (is_array($value)) {
					$array[$key] = $this->array_trim($value);
				} else {
					$array[$key] = trim($value);
				}
				if (empty($array[$key]))
					unset($array[$key]);
			}
		} else {
			$array = trim($array);
		}
		return $array;
	}
	/**
	 * Removes duplicate values from multidimensional array 
	 */
	function array_unique($array)
	{
		$result = array_map("unserialize", array_unique(array_map("serialize", $array)));
		foreach ($result as $key => $value)
		{
			if (is_array($value)) {
				$result[$key] = $this->array_unique($value);
			}
		}
		return $result;
	}
	function add_user_role_and_capabilities()
	{
		global $wp, $wpdb, $wp_roles, $TestPlugin;
		$role1_name = "MainUser";
		$role1_slug = sanitize_title($role1_name);
		$role1_caps = array(
			$role1_slug		=> true,
			'read'			=> true,
			'edit_posts'	=> true,
			'manage_subusers'	=> true,
			'upload_files'	=> true,
			'level_0'		=> true,
			'level_1'		=> true,
		);
		//Create MainUser Role
		add_role($role1_slug, $role1_name, $role1_caps);
		
		$role2_name = "SubUser";
		$role2_slug = sanitize_title($role2_name);
		$role2_caps = array(
			$role2_slug		=> true,
			'read'			=> true,
			'edit_posts'	=> true,
			'upload_files'	=> true,
			'level_0'		=> true,
			'level_1'		=> true,
		);
		//Create SubUser Role
		add_role($role2_slug, $role2_name, $role2_caps);
	}
	function write_response( $response_data, $file_name='' )
	{
		global $wp, $wpdb, $wp_filesystem;
		if(!empty($file_name))
		{
			$file_path = PLUG_DIR . '/log/' . $file_name;
		} else {
			$file_path = PLUG_DIR . '/log/response.txt';
		}
		if (file_exists(ABSPATH . 'wp-admin/includes/file.php')) {
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			if (false === ($creds = request_filesystem_credentials($file_path, '', false, false) ))
			{
				// if we get here, then we don't have credentials yet,
				// but have just produced a form for the user to fill in, 
				// so stop processing for now
				return true; // stop the normal page form from displaying
			}
			// now we have some credentials, try to get the wp_filesystem running
			if (!WP_Filesystem($creds)) {
				// our credentials were no good, ask the user for them again
				request_filesystem_credentials($file_path, $method, true, false);
				return true;
			}
			$file_data = $wp_filesystem->get_contents($file_path);
			$file_data .= $response_data;
			$file_data .= "\r\n===========================================================================\r\n";
			$breaks = array("<br />","<br>","<br/>");  
			$file_data = str_ireplace($breaks, "\r\n", $file_data);
			$write_file = $wp_filesystem->put_contents( $file_path, $file_data, FS_CHMOD_FILE);
			if (!$write_file) {
				//_e('Error Saving Log!', MEMBERSHIP_TXTDOMAIN);
			}
		}
		return;
	}
}
