<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Visitor_Tracer
 * @subpackage Visitor_Tracer/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Visitor_Tracer
 * @subpackage Visitor_Tracer/includes
 * @author     Developer Junayed <admin@easeare.com>
 */
class Visitor_Tracer_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
		$visitor_tracer = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}visitor_tracer` (
			`ID` INT NOT NULL AUTO_INCREMENT,
			`visitor_id` INT NOT NULL,
			`entryPage` VARCHAR(555) NOT NULL,
			`exitPage` VARCHAR(555) NOT NULL,
			`referer` VARCHAR(555) NOT NULL,
			`local_ip` VARCHAR(555) NOT NULL,
			`user_agent` VARCHAR(555) NOT NULL,
			`first_visit` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`last_visit` DATETIME NOT NULL,
			PRIMARY KEY (`ID`)) ENGINE = InnoDB";
			dbDelta($visitor_tracer);
	}
	
}
