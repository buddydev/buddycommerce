<?php
/**
 * BuddyCommerce Checkout endpoint screen handler.
 *
 * @package    BuddyCommerce
 * @subpackage Core\Users\Handlers
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyCommerce\Core\Users\Handlers;

use BuddyCommerce\Core\Users\BC_Screens;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Checkout endpoint screen handler.
 */
class BC_Checkout_Endpoint_Screen_Handler {

	/**
	 * Setup the bootstrapper.
	 *
	 * @return BC_Checkout_Endpoint_Screen_Handler
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
		add_action( 'bp_actions', array( $this, 'maybe_handle' ) );
	}

	/**
	 * If this is checkout end point, handle it.
	 */
	public function maybe_handle() {
		// we may update it for admin in future to let them update others address.
		if ( ! bp_is_my_profile() || ! bcommerce_is_user_checkout() ) {
			return;
		}

		if ( bcommerce_is_top_level_user_nav_item( 'checkout' ) ) {
			$slug  = bp_current_action();
			$value = bp_action_variable( 0 );
		} else {
			$slug  = bp_action_variable( 0 );
			$value = bp_action_variable( 1 );
		}

		$endpoint = bcommerce_get_endpoint_from_slug( $slug );

		if ( ! $endpoint ) {
			return;
		}

		// if not a valid end point for us.
		if ( ! in_array( $endpoint, array(
			'order-pay',
			'order-received',
			'add-payment-method',
			'delete-payment-method',
			'set-default-payment-method',
		) ) ) {
			return;
		}

		set_query_var( $endpoint, $value );

		BC_Screens::get_instance()->view_order();
	}
}
