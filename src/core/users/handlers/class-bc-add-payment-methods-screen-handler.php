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
		add_action( 'bp_actions', array( $this, 'maybe_handle' ) );
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
