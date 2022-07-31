<?php
/**
 * Main Class
 *
 * @package    BuddyCommerce
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * BuddyCommerce main class, acts as a facade to other things.
 *
 * @property-read string              $path absolute path to the plugin directory.
 * @property-read string              $url absolute url to the plugin directory.
 * @property-read string              $basename plugin base name.
 * @property-read string              $version plugin version.
 */
class BuddyCommerce {

	/**
	 * Current plugin version .
	 *
	 * @var string
	 */
	private $version = '1.0.5';

	/**
	 * Singleton instance
	 *
	 * @var BuddyCommerce
	 */
	private static $instance = null;

	/**
	 * Absolute path to this plugin directory.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Absolute url to this plugin directory.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Plugin basename.
	 *
	 * @var string
	 */
	private $basename;

	/**
	 * Minimum required php version.
	 *
	 * @var float
	 */
	private $min_php_version = 5.4;

	/**
	 * Protected properties. These properties are inaccessible via magic method.
	 *
	 * @var array
	 */
	private $guarded = array( 'instance' );

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->bootstrap();
	}

	/**
	 * Get singleton instance
	 *
	 * @return BuddyCommerce
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Boot the class.
	 *
	 * It's here for the sake of convention.
	 *
	 * @return BuddyCommerce
	 */
	public static function boot() {
		return self::get_instance();
	}

	/**
	 * Bootstrap the core.
	 */
	private function bootstrap() {

		// Setup general properties.
		$this->path     = plugin_dir_path( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->basename = plugin_basename( path_join( dirname( __FILE__ ), 'buddycommerce.php' ) );
	}

	/**
	 * Get the main plugin file.
	 *
	 * @return string
	 */
	public function get_file() {
		return path_join( dirname( __FILE__ ), 'buddycommerce.php' );
	}

	/**
	 * Check if BuddyCommerce is network active.
	 *
	 * @return bool
	 */
	public function is_network_active() {

		if ( ! is_multisite() ) {
			return false;
		}

		// Check the sitewide plugins array.
		$base    = $this->basename;
		$plugins = get_site_option( 'active_sitewide_plugins' );

		if ( ! is_array( $plugins ) || ! isset( $plugins[ $base ] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Is the PHP version good enough for us?
	 * Checks if php version matches our min requirement.
	 *
	 * @return bool
	 */
	public function has_php_version() {
		return version_compare( PHP_VERSION, $this->min_php_version, '>=' );
	}

	/**
	 * Magic method for accessing property as readonly(It's a lie, references can be updated).
	 *
	 * @param string $name property name.
	 *
	 * @return mixed|null
	 */
	public function __get( $name ) {

		if ( ! in_array( $name, $this->guarded, true ) && property_exists( $this, $name ) ) {
			return $this->{$name};
		}

		return null;
	}

	/**
	 * Check if the property exists.
	 *
	 * @param string $name property name.
	 *
	 * @return bool
	 */
	public function __isset( $name ) {
		return ! in_array( $name, $this->guarded, true ) && property_exists( $this, $name );
	}
}
