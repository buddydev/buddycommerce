<?php
/**
 * BuddyCommerce paginated views handler
 *
 * Loads views for paginated screens.
 *
 * @package    BuddyCommerce
 * @subpackage Core\Users\Handlers
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyCommerce\Core\Users\Handlers;

use BuddyCommerce\Core\Users\BC_Screens;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Paginated views handler.
 */
class BC_Paginated_Views_Handler {

	/**
	 * Setup the bootstrapper.
	 *
	 * @return BC_Paginated_Views_Handler
	 */
	public static function boot() {
		static $self;

		if ( ! is_null( $self ) ) {
			return $self; // already booted.
		}

		$self = new self();
		$self->register();
		return $self;
	}

	/**
	 * Register filters.
	 */
	private function register() {
		add_action( 'bp_actions', array( $this, 'handle_orders_pagination' ) );
	}


	/**
	 * If this is orders end point and paginated handle it.
	 */
	public function handle_orders_pagination() {
		// we may update it for admin in future to let them update others address.
		if ( ! bp_is_my_profile() || ! bcommerce_is_user_orders() || bcommerce_is_user_view_order() ) {
			return;
		}

		if ( ! is_numeric( bp_current_action() ) ) {
			return;
		}

		BC_Screens::get_instance()->orders();
	}
}
