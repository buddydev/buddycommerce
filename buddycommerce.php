<?php
/**
 * Main Plugin file.
 *
 * @package           BuddyCommerce
 *
 * @wordpress-plugin
 * Plugin Name:       BuddyCommerce
 * Plugin URI:        https://buddydev.com/plugins/buddycommerce/
 * Description:       BuddyPress and WooCommerce Integration.
 * Version:           1.0.8
 * Author:            BuddyDev
 * Author URI:        https://buddydev.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddycommerce
 * Domain Path:       /languages
 * Requires PHP:      5.4
 */

use BuddyCommerce\Bootstrap\BC_Autoloader;
use BuddyCommerce\Bootstrap\BC_Bootstrapper;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Copyright (C) 2019, Brajesh Singh
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License,
 * or any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, see <http://www.gnu.org/licenses>.
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

// Load autoloader.
require_once 'src/bootstrap/class-bc-autoloader.php';
require_once 'class-buddycommerce.php';

// Register autoloader.
try {
	spl_autoload_register( new BC_Autoloader( 'BuddyCommerce\\', __DIR__ . '/src/' ) );
	// Boot the main class.
	BuddyCommerce::boot();
	// setup bootstrapper.
	BC_Bootstrapper::boot();

} catch ( Exception $e ) {
	error_log( 'BuddyCommerce: ' . __( 'Unable to register autoloader. The plugin will not work.', 'buddycommerce' ) );
}

/**
 * Save default settings on activate.
 */
function bcommerce_on_activate() {
	if ( ! get_option( 'buddycommerce_settings' ) ) {
		require_once plugin_dir_path( __FILE__ ) . 'src/core/bc-general-functions.php';
		update_option( 'buddycommerce_settings', bcommerce_get_default_options() );
	}
	update_site_option( 'buddycommerce_plugin_version', buddycommerce()->version );
}

register_activation_hook( __FILE__, 'bcommerce_on_activate' );

/**
 * Helper method to access BuddyCommerce instance.
 *
 * @return BuddyCommerce
 */
function buddycommerce() {
	return BuddyCommerce::get_instance();
}
