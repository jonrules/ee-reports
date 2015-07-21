<?php
/*
Plugin Name: Event Espresso Reports
Plugin URI: http://patternsinthecloud.com
Description: Adds new reports for Event Espresso
Version: 1.0
Author: Patterns in the Cloud
Author URI: http://patternsinthecloud.com
License: Single-site
*/

function ee_reports_install() {

}
register_activation_hook( __FILE__, 'ee_reports_install' );

function ee_reports_deactivate() {

}
register_deactivation_hook( __FILE__, 'ee_reports_deactivate' );

function ee_reports_uninstall() {

}
register_uninstall_hook( __FILE__, 'ee_reports_uninstall' );