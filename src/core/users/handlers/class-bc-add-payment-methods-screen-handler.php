<?php
/**
 * BuddyCommerce Payment Methods screen handler.
 *
 * @package    BuddyCommerce
 * @subpackage Core\Users\Handlers
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyCommerce\Core\Users\Handlers;

// Do not allow direct access over web.
use BuddyCommerce\Core\Users\BC_Screens;

defined( 'ABSPATH' ) || exit;

/**
 * Add Payment screen handler.
 */
class BC_Add_Payment_Methods_Screen_Handler {

	/**
	 * Setup the bootstrapper.
	 *
	 * @return BC_Add_Payment_Methods_Screen_Handler
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
		add_action( 'wp', array( $this, 'maybe_update_query' ), 11 );
		//add_action( 'bp_actions', array( $this, 'maybe_handle' ) );
	}

	public function maybe_update_query() {
		// we may update it for admin in future to let them update others address.
		if ( ! function_exists( 'buddypress' ) || ! bp_is_my_profile() || ! bcommerce_is_user_payment_methods() ) {
			return;
		}

		$is_top = bcommerce_is_top_level_user_nav_item( 'payment_methods' );
		$val    = $is_top ? bp_action_variable( 0 ) : bp_action_variable( 1 );

		global $wp;
		if ( $this->is_payment_endpoint( bcommerce_get_tab_slug( 'add-payment-method', true ) ) ) {
			$wp->set_query_var( 'add-payment-method', 1 );
		} elseif ( $this->is_payment_endpoint( bcommerce_get_tab_slug( 'set-default-payment-method', true ) ) ) {
			$wp->set_query_var( 'set-default-payment-method', $val );
		} elseif ( $this->is_payment_endpoint( bcommerce_get_tab_slug( 'delete-payment-method', true ) ) ) {
			$wp->set_query_var( 'delete-payment-method', $val );

		}

	}

	private function is_payment_endpoint( $slug ) {

		if ( bcommerce_is_top_level_user_nav_item( 'payment_methods' ) ) {
			$is = bp_is_current_action( $slug );
		} else {
			$is = bp_is_action_variable( $slug, 0 );
		}
		return $is;
	}
	/**
	 * If this is address end point, handle it.
	 */
	public function maybe_handle() {
		// we may update it for admin in future to let them update others address.
		if ( ! bp_is_my_profile() || ! bcommerce_is_user_add_payment_methods() ) {
			return;
		}

		BC_Screens::get_instance()->add_payment_methods();
	}
}
