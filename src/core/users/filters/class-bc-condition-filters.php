<?php
/**
 * BuddyCommerce URL Filter
 *
 * @package    BuddyCommerce
 * @subpackage Handlers
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyCommerce\Core\Users\Filters;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Assets Loader.
 */
class BC_Condition_Filters {

	/**
	 * Setup the bootstrapper.
	 *
	 * @return BC_Condition_Filters
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
		// WOOCOMMERCE_CART - see screen function.
		//  WOOCOMMERCE_CHECKOUT - see screen functions.
		add_filter( 'woocommerce_is_account_page', array( $this, 'is_my_account' ) );

		add_filter( 'woocommerce_is_order_received_page', array( $this, 'is_order_received_page'));

		// is_checkout_pay_page - no need to override.
		// woocommerce_is_order_received_page
	}

	/**
	 * Is it order recieved page?
	 *
	 * @param bool $is is.
	 *
	 * @return bool
	 */
	public function is_order_received_page( $is ) {
		global $wp;
		if ( ! $is && is_checkout() && isset( $wp->query_vars['order-received'] ) ) {
			$is = true;
		}

		return $is;
	}

	/**
	 * Filter is_account_page
	 *
	 * @param bool $is is it account page.
	 *
	 * @return bool
	 */
	public function is_my_account( $is ) {

		if ( bcommerce_is_woo_page() ) {
			$is = true;
		}

		return $is;
	}
}
