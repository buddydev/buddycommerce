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
 * Membership endpoint screen handler.
 */
class BC_Membership_Endpoint_Screen_Handler {

	/**
	 * Setup the bootstrapper.
	 *
	 * @return BC_Membership_Endpoint_Screen_Handler
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
		if ( ! bp_is_my_profile() || ! function_exists( 'wc_memberships_get_members_area_endpoint' ) || ! bcommerce_is_current_tab( 'members_area' ) ) {
			return;
		}


		if ( bcommerce_is_top_level_user_nav_item( 'members_area' ) ) {
			$value = bp_current_action();
		} else {
			$value = bp_action_variable( 0 );
		}


		set_query_var( wc_memberships_get_members_area_endpoint(), $value );

		BC_Screens::get_instance()->memberships();
	}
}
