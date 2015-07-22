<?php
/*
Plugin Name: Event Espresso Reports
Plugin URI: http://patternsinthecloud.com
Description: Adds new reports for Event Espresso
Version: 1.0.0
Author: Patterns in the Cloud
Author URI: http://patternsinthecloud.com
License: Single-site
*/

define( 'EE_REPORTS_VERSION', '1.0.0' );

function ee_reports_install() {

}
register_activation_hook( __FILE__, 'ee_reports_install' );

function ee_reports_deactivate() {

}
register_deactivation_hook( __FILE__, 'ee_reports_deactivate' );

function ee_reports_uninstall() {

}
register_uninstall_hook( __FILE__, 'ee_reports_uninstall' );

function ee_reports_scripts() {
	$url_path = plugin_dir_url( __FILE__ );
	wp_enqueue_style( 'ee-reports', $url_path . 'css/admin-style.css', array(), EE_REPORTS_VERSION );
}
add_action( 'admin_enqueue_scripts', 'ee_reports_scripts' );

function ee_reports_admin_menu() {
	add_menu_page( 'Event Espresso Reports', 'EE Reports', 'manage_options', 'ee_reports', null, 'dashicons-media-spreadsheet' );
	add_submenu_page( 'ee_reports', 'Event Espresso Registrations', 'Registrations', 'manage_options', 'ee_reports', 'ee_reports_admin_render_registrations_page' );
}
add_action( 'admin_menu', 'ee_reports_admin_menu' );

function ee_reports_admin_render_registrations_page() {
	echo '<div class="wrap">';

	require_once( 'classes/admin/ee-reports-registrations-list-table.php' );
	$list_table = new EE_Reports_Registrations_List_Table();
	$list_table->prepare_items();
	include( 'templates/admin/ee-reports-registrations.php' );
		
	echo '</div>';
}