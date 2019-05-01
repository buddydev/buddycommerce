<?php
/**
 * BuddyCommerce View order screen handler.
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
 * View order screen handler.
 */
class BC_View_Order_Screen_Handler {

	/**
	 * Setup the bootstrapper.
	 *
	 * @return BC_View_Order_Screen_Handler
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
	 * If this is address end point, handle it.
	 */
	public function maybe_handle() {
		// we may update it for admin in future to let them update others address.
		if ( ! bp_is_my_profile() || ! bcommerce_is_user_view_order() ) {
			return;
		}


		$order_id = bcommerce_is_top_level_user_nav_item( 'orders' ) ? bp_action_variable( 0 ) : bp_action_variable( 1 );

		set_query_var( 'view-order', $order_id );

		BC_Screens::get_instance()->view_order();
	}
}
