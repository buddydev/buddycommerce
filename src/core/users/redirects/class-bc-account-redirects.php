<?php
/**
 * BuddyCommerce Account Redirection
 *
 * @package    BuddyCommerce
 * @subpackage Core\Users\Handlers
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyCommerce\Core\Users\Redirects;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Account url redirects
 */
class BC_Account_Redirects {

	/**
	 * Setup the bootstrapper.
	 *
	 * @return BC_Account_Redirects
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
		// Yes, there is a reason for attaching individually. Ask me for clarification.
		add_filter( 'bp_template_redirect', array( $this, 'maybe_redirect_account' ) );
		add_filter( 'bp_template_redirect', array( $this, 'maybe_redirect_user_downloads' ) );
		add_filter( 'bp_template_redirect', array( $this, 'maybe_redirect_user_orders' ) );
		add_filter( 'bp_template_redirect', array( $this, 'maybe_redirect_cart' ) );
		add_filter( 'bp_template_redirect', array( $this, 'maybe_redirect_checkout' ) );
		add_filter( 'bp_template_redirect', array( $this, 'maybe_redirect_addresses' ) );
		add_filter( 'bp_template_redirect', array( $this, 'maybe_redirect_payment_methods' ) );
	}

	/**
	 * May be redirect my account page.
	 */
	public function maybe_redirect_account() {

		// shop tab should be enabled to allow us redirect.
		if ( ! bcommerce_is_user_nav_item_enabled( 'shop' ) || ! bcommerce_is_user_nav_item_redirect_enabled( 'shop' ) ) {
			return;
		}

		if ( ! $this->needs_redirection() || ! is_page( wc_get_page_id( 'myaccount' ) ) || is_wc_endpoint_url() ) {
			return;
		}

		$user_id = bp_loggedin_user_id();
		$url     = bp_loggedin_user_domain();

		$link = bcommerce_get_user_my_account_permalink( $user_id, $url );

		if ( $link ) {
			bp_core_redirect( $link );
		}
	}

	/**
	 * May be redirect cart page.
	 */
	public function maybe_redirect_cart() {

		// shop tab should be enabled to allow us redirect.
		if ( ! bcommerce_is_user_nav_item_enabled( 'cart' ) || ! bcommerce_is_user_nav_item_redirect_enabled( 'cart' ) ) {
			return;
		}

		if ( ! $this->needs_redirection() || ! is_page( wc_get_page_id( 'cart' ) ) ) {
			return;
		}

		$user_id = bp_loggedin_user_id();
		$url     = bp_loggedin_user_domain();

		$link = bcommerce_get_user_cart_permalink( $user_id, $url );

		if ( $link ) {
			bp_core_redirect( $link );
		}
	}

	/**
	 * May be redirect my account page.
	 */
	public function maybe_redirect_checkout() {

		// shop tab should be enabled to allow us redirect.
		if ( ! bcommerce_is_user_nav_item_enabled( 'checkout' ) || ! bcommerce_is_user_nav_item_redirect_enabled( 'checkout' ) ) {
			return;
		}

		if ( ! $this->needs_redirection() || is_wc_endpoint_url() || ! is_page( wc_get_page_id( 'checkout' ) ) ) {
			return;
		}

		$user_id = bp_loggedin_user_id();
		$url     = bp_loggedin_user_domain();

		$link = bcommerce_get_user_checkout_permalink( $user_id, $url );

		if ( $link ) {
			bp_core_redirect( $link );
		}
	}

	/**
	 * Redirect downloads.
	 */
	public function maybe_redirect_user_downloads() {

		// shop tab should be enabled to allow us redirect.
		if ( ! bcommerce_is_user_nav_item_enabled( 'downloads' ) || ! bcommerce_is_user_nav_item_redirect_enabled( 'downloads' ) ) {
			return;
		}


		if ( ! $this->needs_redirection() || ! is_wc_endpoint_url( 'downloads' ) ) {
			return;
		}

		$user_id = bp_loggedin_user_id();
		$url     = bp_loggedin_user_domain();

		$link = bcommerce_get_user_downloads_permalink( $user_id, $url );

		if ( $link ) {
			bp_core_redirect( $link );
		}
	}

	/**
	 * Redirect orders.
	 */
	public function maybe_redirect_user_orders() {

		// shop tab should be enabled to allow us redirect.
		if ( ! bcommerce_is_user_nav_item_enabled( 'orders' ) || ! bcommerce_is_user_nav_item_redirect_enabled( 'orders' ) ) {
			return;
		}


		if ( ! $this->needs_redirection() || ! is_wc_endpoint_url( 'orders' ) ) {
			return;
		}

		$user_id = bp_loggedin_user_id();
		$url     = bp_loggedin_user_domain();

		$link = bcommerce_get_user_orders_permalink( $user_id, $url );

		if ( $link ) {
			bp_core_redirect( $link );
		}
	}
	/**
	 * Redirect orders.
	 */
	public function maybe_redirect_addresses() {

		// shop tab should be enabled to allow us redirect.
		if ( ! bcommerce_is_user_nav_item_enabled( 'addresses' ) || ! bcommerce_is_user_nav_item_redirect_enabled( 'addresses' ) ) {
			return;
		}


		if ( ! $this->needs_redirection() || ! is_wc_endpoint_url( 'edit-address' ) ) {
			return;
		}

		$link = '';

		$user_id = bp_loggedin_user_id();
		$url     = bp_loggedin_user_domain();

		$link = bcommerce_get_user_address_permalink( $user_id, $url );

		if ( $link ) {
			bp_core_redirect( $link );
		}
	}

	/**
	 * Redirect orders.
	 */
	public function maybe_redirect_payment_methods() {

		// shop tab should be enabled to allow us redirect.
		if ( ! bcommerce_is_user_nav_item_enabled( 'payment_methods' ) || ! bcommerce_is_user_nav_item_redirect_enabled( 'payment_methods' ) ) {
			return;
		}


		if ( ! $this->needs_redirection() || ! is_wc_endpoint_url( 'payment-methods' ) ) {
			return;
		}

		$user_id = bp_loggedin_user_id();
		$url     = bp_loggedin_user_domain();

		$link = bcommerce_get_user_address_permalink( $user_id, $url );

		if ( $link ) {
			bp_core_redirect( $link );
		}
	}

	/**
	 * Does it match basic redirect condition.
	 *
	 * @return bool
	 */
	public function needs_redirection() {
		static $needs_redirection = null;

		if ( did_action( 'init' ) && isset( $needs_redirection ) ) {
			return $needs_redirection;
		}

		if ( ! is_user_logged_in() || ! is_page() || bp_is_user() ) {
			$needs_redirection = false;
		} else {
			$needs_redirection = true;
		}

		return $needs_redirection;
	}
}
