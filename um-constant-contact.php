<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              NA
 * @since             1.0.0
 * @package           Um_Constant_Contact
 *
 * @wordpress-plugin
 * Plugin Name:       Ultimate Member - Constant Contact Add on
 * Plugin URI:        NA
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Junie Lorenzo
 * Author URI:        NA
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       um-constant-contact
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-um-constant-contact-activator.php
 */
function activate_um_constant_contact() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-um-constant-contact-activator.php';
	Um_Constant_Contact_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-um-constant-contact-deactivator.php
 */
function deactivate_um_constant_contact() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-um-constant-contact-deactivator.php';
	Um_Constant_Contact_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_um_constant_contact' );
register_deactivation_hook( __FILE__, 'deactivate_um_constant_contact' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-um-constant-contact.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_um_constant_contact() {

	$plugin = new Um_Constant_Contact();
	$plugin->run();

}
run_um_constant_contact();
