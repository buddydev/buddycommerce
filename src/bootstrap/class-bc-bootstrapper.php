<?php
/**
 * Bootstrapper. Initializes the plugin.
 *
 * @package    BuddyCommerce
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyCommerce\Bootstrap;

use BuddyCommerce\Core\Users\Handlers\BC_Add_Payment_Methods_Screen_Handler;
use BuddyCommerce\Core\Users\Handlers\BC_Address_Screen_Handler;
use BuddyCommerce\Core\Users\Filters\BC_URL_Filters;
use BuddyCommerce\Core\Users\Filters\BC_Condition_Filters;
use BuddyCommerce\Core\Users\Handlers\BC_Checkout_Endpoint_Screen_Handler;
use BuddyCommerce\Core\Users\Handlers\BC_View_Subscription_Screen_Handler;
use BuddyCommerce\Core\Users\Redirects\BC_Account_Redirects;
use BuddyCommerce\Core\Users\Handlers\BC_Tabs_Helper;
use BuddyCommerce\Core\Users\Handlers\BC_View_Order_Screen_Handler;
use BuddyCommerce\Admin\Admin_Settings_Helper;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Bootstrapper.
 */
class BC_Bootstrapper {

	/**
	 * Plugin Dir path.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Plugin url.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * BC_Bootstrapper constructor.
	 */
	private function __construct() {
		$bc         = buddycommerce();
		$this->path = $bc->path;
		$this->url  = $bc->url;
	}

	/**
	 * Setup the bootstrapper.
	 *
	 * @return BC_Bootstrapper
	 */
	public static function boot() {
		static $self;

		if ( ! is_null( $self ) ) {
			return $self; // already booted.
		}

		$self = new self();
		$self->register();
		$self->setup();
		return $self;
	}

	/**
	 * Register handlers.
	 */
	private function register() {
		// load core.
		add_action( 'plugins_loaded', array( $this, 'load' ) );
		add_action( 'plugins_loaded', array( $this, 'load_admin' ), 9996 ); // pt settings 1.0.4.

		add_action( 'init', array( $this, 'load_translations' ) );

		// Enqueue assets.
		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
	}

	/**
	 * Setup actions.
	 */
	private function setup() {
	}

	/**
	 * Load core functions/template tags.
	 * These are non auto loadable constructs.
	 */
	public function load() {

		if ( ! $this->is_enabled() ) {
			return;
		}

		$this->load_core();
		$this->load_front();

		BC_Account_Redirects::boot();
		BC_Address_Screen_Handler::boot();
		BC_View_Order_Screen_Handler::boot();
		BC_Add_Payment_Methods_Screen_Handler::boot();
		BC_View_Subscription_Screen_Handler::boot();
		BC_Checkout_Endpoint_Screen_Handler::boot();

		BC_URL_Filters::boot();
		BC_Condition_Filters::boot();
		BC_Tabs_Helper::boot();

	}

	/**
	 * Load admin
	 */
	public function load_admin() {

		if ( ! $this->is_enabled() ) {
			return;
		}

		if ( ! is_admin() || wp_doing_ajax() ) {
			return;
		}

		require_once $this->path . 'src/admin/pt-settings/pt-settings-loader.php';
		Admin_Settings_Helper::boot();
	}

	/**
	 * Load translations.
	 */
	public function load_translations() {
		load_plugin_textdomain( 'buddycommerce', false, basename( buddycommerce()->path ) . '/languages' );
	}

	/**
	 * Load assets.
	 */
	public function load_assets() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		BC_Assets_Loader::boot();
	}

	/**
	 * Load core files which are essential for BuddyCommerce but can not be auto loaded.
	 */
	private function load_core() {
		$path = $this->path;
		// Load main files.
		// common functions/libs/3rd party loaders if any.
		require $path . 'src/core/bc-general-functions.php';
		require $path . 'src/core/bc-link-functions.php';
		require $path . 'src/core/bc-template.php';
		require $path . 'src/core/bc-conditional-functions.php';
		require $path . 'src/core/bc-wc-override.php';
	}

	/**
	 * Load front only code here.
	 */
	private function load_front() {
		// load front only scripts.
	}

	/**
	 * Check if all the dependencies are enabled.
	 *
	 * @return bool
	 */
	private function is_enabled() {
		return function_exists( 'buddypress' ) && class_exists( '\WooCommerce' );
	}
}
