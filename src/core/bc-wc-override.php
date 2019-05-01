<?php
/**
 * BuddyCommerce Function overrides for WooCommerce.
 *
 * For more overrides, Please see bc-condition-filters.php
 *
 * @package    BuddyCommerce
 * @subpackage Core
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;


// Overrides as there are no other ways.
if ( ! function_exists( 'is_edit_account_page' ) ) {

	/**
	 * Check for edit account page.
	 * Returns true when viewing the edit account page.
	 */
	function is_edit_account_page() {
		global $wp;
		return ( is_account_page() && isset( $wp->query_vars['edit-account'] ) );
	}
}

if ( ! function_exists( 'is_add_payment_method_page' ) ) {

	/**
	 * Is it Payment method page.
	 *
	 * @return bool
	 */
	function is_add_payment_method_page() {

		global $wp;

		return is_account_page() && ( isset( $wp->query_vars['payment-methods'] ) || isset( $wp->query_vars['add-payment-method'] ) );
	}
}

if ( ! function_exists( 'is_view_order_page' ) ) {
	/**
	 * Is it view order page?
	 *
	 * @return bool
	 */
	function is_view_order_page() {
		global $wp;
		return ( is_account_page() && isset( $wp->query_vars['view-order'] ) );
	}
}
