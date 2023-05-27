<?php
/**
 * Assets Loader
 *
 * @package    BuddyCommerce
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyCommerce\Bootstrap;

// Do not allow direct access over web.
use BuddyCommerce\Traits\BC_Bootable;

defined( 'ABSPATH' ) || exit;

/**
 * Assets Loader.
 */
class BC_Assets_Loader {

	use BC_Bootable;

	/**
	 * Data to be set in js.
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Setup for boot.
	 */
	private function setup() {
		$this->register();
		$this->enqueue();
	}

	/**
	 * Register assets.
	 */
	private function register() {
		$this->register_vendors();
		$this->register_core();
	}

	/**
	 * Register core assets.
	 */
	private function register_core() {
		$version = buddycommerce()->version;
		// We will move this part to template pack in the future.
		wp_register_style( 'buddycommerce-core', bcommerce_locate_asset( 'assets/buddycommerce-core.css' ), false, $version );
	}

	/**
	 * Load assets.
	 */
	private function enqueue() {
		wp_enqueue_style( 'buddycommerce-core' );
	}

	/**
	 * Register vendor scripts.
	 */
	private function register_vendors() {
	}
}
