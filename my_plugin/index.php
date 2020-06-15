<?php
/*
Plugin Name: My Plugin
Plugin URI: https://www.website.com/my_plugin
Description: My Plugin description
Version: 1.0.0
Author: My Name
Author URI: https://www.website.com/
Short Name: my-plugin
Plugin update URI: https://www.website.com/my_plugin/update
*/

	// Paths
	define('MY_PLUGIN_FOLDER', 'my_plugin/');
	define('MY_PLUGIN_PATH', osc_plugins_path() . MY_PLUGIN_FOLDER);


	// Prepare model, controllers and helpers
	require_once MY_PLUGIN_PATH . "oc-load.php";


	// URL routes
	osc_add_route('my-plugin-admin-crud', MY_PLUGIN_FOLDER.'views/admin/crud', MY_PLUGIN_FOLDER.'views/admin/crud', MY_PLUGIN_FOLDER.'views/admin/crud.php');
	osc_add_route('my-plugin-admin-settings', MY_PLUGIN_FOLDER.'views/admin/settings', MY_PLUGIN_FOLDER.'views/admin/settings', MY_PLUGIN_FOLDER.'views/admin/settings.php');
	

	// Headers in the admin panel
	osc_add_hook('admin_menu_init', function() {
	    osc_add_admin_submenu_divider(
	        "plugins", __("My Plugin", 'my_plugin'), "my_plugin", "administrator"
	    );

	    osc_add_admin_submenu_page(
	        "plugins", __("CRUD", 'my_plugin'), osc_route_admin_url("my-plugin-admin-crud"), "my-plugin-admin-crud", "administrator"
	    );

	    osc_add_admin_submenu_page(
	        "plugins", __("Settings", 'my_plugin'), osc_route_admin_url("my-plugin-admin-settings"), "my-plugin-admin-settings", "administrator"
	    );
	});


	// Load the controllers, depend of url route
	function my_plugin_admin_controllers() {
		switch (Params::getParam("route")) {
			case 'my-plugin-admin-crud':
				$filter = function($string) {
	                return __("CRUD - My Plugin", 'my_plugin');
	            };

	            // Page title (in <head />)
	            osc_add_filter("admin_title", $filter, 10);

	            // Page title (in <h1 />)
	            osc_add_filter("custom_plugin_title", $filter);

	            $do = new CAdminMyPluginCRUD();
	            $do->doModel();
				break;

			case 'my-plugin-admin-settings':
				$filter = function($string) {
	                return __("Settings - My Plugin", 'my_plugin');
	            };

	            // Page title (in <head />)
	            osc_add_filter("admin_title", $filter, 10);

	            // Page title (in <h1 />)
	            osc_add_filter("custom_plugin_title", $filter);

	            $do = new CAdminMyPluginSettings();
	            $do->doModel();
				break;
		}
	}
	osc_add_hook("renderplugin_controller", "my_plugin_admin_controllers");


	function my_plugin_content() {
		include MY_PLUGIN_PATH . 'parts/public/my_plugin_content.php';
	}
	// Add the function in a hook, you run on the template with: osc_run_hook('my_plugin');
	osc_add_hook('my_plugin', 'my_plugin_content');


	// 'Configure' link
	function my_plugin_configure_admin_link() {
		osc_redirect_to(osc_route_admin_url('my-plugin-admin-settings'));
	}
	// Show 'Configure' link at plugins table
	osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'my_plugin_configure_admin_link');


	// Call uninstallation method from model (model/MyPlugin.php)
	function my_plugin_uninstall() {
		MyPlugin::newInstance()->uninstall();
	}
	// Show an Uninstall link at plugins table
	osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'my_plugin_uninstall');


	// Call the process of installation method
	function my_plugin_install() {
		MyPlugin::newInstance()->install();
	}
	// Register plugin's installation
	osc_register_plugin(osc_plugin_path(__FILE__), 'my_plugin_install');