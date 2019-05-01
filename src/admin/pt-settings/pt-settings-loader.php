<?php
/**
 * PressThemes.io Settings Loader
 *
 * @package Press_Themes\PT_Settings
 *
 * @version 1.0.4
 */

use Press_Themes\PT_Settings\Ajax_Handler;

/**
 * Simply include this file in your code to use the Press_Themes\PT_Settings package
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// The autoloader is not defined. In other words, disables loading the libraries twice.
if ( ! function_exists( 'pt_settings_class_loader' ) ) :
	/**
	 * Class Loader for PressThemes Settings package.
	 *
	 * After registering this autoload function with SPL, the following line
	 * would cause the function to attempt to load the \Press_Themes\PT_Settings\Bar\Baz\Qux class
	 * from /path/to/project/pt-settings/src/bar/baz/class-qux.php:
	 *
	 *      new \Press_Themes\PT_Settings\Baz\Qux;
	 *  maps to /path/to/pt-settings/src/baz/class-qux.php
	 * The path/directory name should be all lowercase and the the class file is named as 'class-$classname.php'
	 *
	 * @param string $class The fully-qualified class name.
	 *
	 * @return void
	 */
	function pt_settings_class_loader( $class ) {

		// Project-specific namespace prefix.
		$prefix = 'Press_Themes\\PT_Settings\\';

		// Base directory for the namespace prefix.
		$base_dir = __DIR__ . '/src/';

		// does the class use the namespace prefix?
		$len = strlen( $prefix );

		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			// no, move to the next registered auto loader.
			return;
		}

		// get the relative class name.
		// also make it lower case as we will use it as file name with wp standards.
		$relative_class = strtolower( substr( $class, $len ) );

		// replace the namespace prefix with the base directory, replace namespace
		// separators with directory separators in the relative class name, append
		// with .php. Also, replace the underscore(_) with hyphen(-).
		$file = $base_dir . str_replace( array( '_', '\\' ), array( '-', '/' ), $relative_class );

		$file                       = explode( '/', $file );
		$file[ count( $file ) - 1 ] = 'class-' . $file[ count( $file ) - 1 ] . '.php';

		$file = join( '/', $file );
		// Replace the last component
		//$file = strtolower( $file ); // only lowercase file names.

		// If the file exists, require it.
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}

	// Register the auto loader.
	spl_autoload_register( 'pt_settings_class_loader' );
endif;

if ( ! function_exists( 'pt_settings_load_assets' ) ) :
	/**
	 * Register the required assets
	 */
	function pt_settings_load_assets() {
		$url = pt_settings_guess_url();
		wp_register_script( 'pt-settings-media-uploader', $url . 'src/assets/pt-settings-upload-helper.js', array( 'jquery' ) );
		wp_register_script( 'pt-settings-page-create', $url . 'src/assets/pt-settings-page-create.js', array( 'jquery' ) );
		wp_localize_script( 'pt-settings-page-create', 'PT_Settings', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

	add_action( 'admin_enqueue_scripts', 'pt_settings_load_assets' );
endif;

if ( ! function_exists( 'pt_settings_guess_url' ) ) :
	/**
	 * Guess the current directory url.
	 *
	 * @return string
	 */
	function pt_settings_guess_url() {
		// We need to find the directory of the pt-settings
		// It could be inside a theme or a plugin we  don't know.
		$path = dirname( __FILE__ );
		// For windows, cleanup.
		$path = str_replace( '\\', '/', $path );

		$abspath = str_replace( '\\', '/', ABSPATH );

		// Find relative path.
		$rel_path = str_replace( $abspath, '', $path );

		return trailingslashit( site_url( '/' ) . $rel_path );
	}
endif;

// ajax handler(added in 1.0.2).
if ( ! function_exists( 'pt_settings_ajax_handler' ) ) :
	/**
	 * Create ajax handler.
	 *
	 * @return Ajax_Handler|null
	 */
	function pt_settings_ajax_handler() {
		static $handler;
		if ( ! $handler && class_exists( 'Ajax_Handler' ) ) {
			$handler = new Ajax_Handler();
		}

		return $handler;
	}

	// init.
	pt_settings_ajax_handler();
endif;
