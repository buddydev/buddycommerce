<?php
/**
 * BuddyCommerce URL Filter
 *
 * @package    BuddyCommerce
 * @subpackage Bootstrap
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
class BC_URL_Filters {

	/**
	 * Setup the bootstrapper.
	 *
	 * @return BC_URL_Filters
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
		// filter ulr for special pages(myaccount,cart,checkout).
		add_filter( 'page_link', array( $this, 'filter_page_link' ), 10, 2 );

		// filter My account page permalink.
		add_filter( 'woocommerce_get_myaccount_page_permalink', array( $this, 'filter_myaccount_url' ) );
		// Cart.
		add_filter( 'woocommerce_get_cart_page_permalink', array( $this, 'filter_cart_url' ) );
		// checkout.
		add_filter( 'woocommerce_get_checkout_page_permalink', array( $this, 'filter_checkout_url' ) );
		/**
		 * We do not need to filter on
		 * 'woocommerce_get_cart_url'
		 * 'woocommerce_get_checkout_url'
		 * As we already filter on
		 * @see wc_get_page_permalink().
		 */
		add_filter( 'woocommerce_get_endpoint_url', array( $this, 'filter_endpoints' ), 10, 4 );
		// filter view order.
		add_filter( 'woocommerce_get_view_order_url', array( $this, 'filter_view_order_url' ), 10, 2 );
		// checkout payment url
		// woocommerce_get_checkout_payment_url
		// checkout->order received
		// woocommerce_get_checkout_order_received_url
		//Cancel Order
		// woocommerce_get_cancel_order_url
		//woocommerce_get_cancel_order_url_raw
		// woocommerce_get_edit_order_url
	}

	/**
	 * Filter page urls(myaccount/cart/checkout).
	 *
	 * Applies for nav menu and other places.
	 *
	 * @param string $link link.
	 * @param int    $page_id page id.
	 *
	 * @return string
	 */
	public function filter_page_link( $link, $page_id ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) || ! is_user_logged_in() ) {
			return $link;
		}

		$user_id = bp_loggedin_user_id();
		$url     = bp_loggedin_user_domain();

		if ( bcommerce_is_user_nav_item_enabled( 'shop' ) && wc_get_page_id( 'myaccount' ) == $page_id ) {
			$link = bcommerce_get_user_my_account_permalink( $user_id, $url );
		} elseif ( bcommerce_is_user_nav_item_enabled( 'cart' ) && wc_get_page_id( 'cart' ) == $page_id ) {
			$link = bcommerce_get_user_cart_permalink( $user_id, $url );
		} elseif ( bcommerce_is_user_nav_item_enabled( 'checkout' ) && wc_get_page_id( 'checkout' ) == $page_id ) {
			$link = bcommerce_get_user_checkout_permalink( $user_id, $url );
		}

		return $link;
	}

	/**
	 * Filter My Account page url.
	 *
	 * @param string $url url.
	 *
	 * @return string
	 */
	public function filter_myaccount_url( $url ) {

		if ( ! is_user_logged_in() || ! bcommerce_is_user_nav_item_enabled( 'shop' ) ) {
			return $url;
		}

		return bcommerce_get_user_my_account_permalink( bp_loggedin_user_id(), bp_loggedin_user_domain() );
	}

	/**
	 * Filter Cart url.
	 *
	 * @param string $url cart url.
	 *
	 * @return string
	 */
	public function filter_cart_url( $url ) {

		if ( ! is_user_logged_in() || ! bcommerce_is_user_nav_item_enabled( 'cart' ) ) {
			return $url;
		}

		return bcommerce_get_user_cart_permalink( bp_loggedin_user_id(), bp_loggedin_user_domain() );
	}

	/**
	 * Filter Checkout page url.
	 *
	 * @param string $url cart url.
	 *
	 * @return string
	 */
	public function filter_checkout_url( $url ) {

		if ( ! is_user_logged_in() || ! bcommerce_is_user_nav_item_enabled( 'checkout' ) ) {
			return $url;
		}

		return bcommerce_get_user_checkout_permalink( bp_loggedin_user_id(), bp_loggedin_user_domain() );
	}

	/**
	 * Filter end point for our edit address.
	 *
	 * @param string $url url.
	 * @param string $endpoint endpoint.
	 * @param string $value value.
	 * @param string $permalink page permalink.
	 *
	 * @return string
	 */
	public function filter_endpoints( $url, $endpoint, $value, $permalink ) {

		if ( bcommerce_is_user_nav_item_enabled( 'addresses' ) && bcommerce_get_endpoint_slug( 'edit-address' ) === $endpoint ) {
			$url = bcommerce_get_user_address_permalink( bp_loggedin_user_id(), bp_loggedin_user_domain(), $endpoint );
			if ( $value ) {
				$url .= trailingslashit( $value );
			}
		} elseif ( bcommerce_is_user_nav_item_enabled( 'payment_methods' ) && bcommerce_get_endpoint_slug( 'payment-methods' ) === $endpoint ) {
			$url = bcommerce_get_user_payment_methods_permalink( bp_loggedin_user_id(), bp_loggedin_user_domain() ) . trailingslashit( $endpoint );
			if ( $value ) {
				$url .= trailingslashit( $value );
			}
		} elseif ( bcommerce_is_user_nav_item_enabled( 'orders' ) && bcommerce_get_endpoint_slug( 'orders' ) === $endpoint ) {
			$url = bcommerce_get_user_orders_permalink( bp_loggedin_user_id(), bp_loggedin_user_domain() );
		} elseif ( bcommerce_is_user_nav_item_enabled( 'downloads' ) && bcommerce_get_endpoint_slug( 'downloads' ) == $endpoint ) {
			$url = bcommerce_get_user_downloads_permalink( bp_loggedin_user_id(), bp_loggedin_user_domain() );
		}

		// downloads
		// payment-methods
		// More endpoints.
		// edit-address
		// edit-account
		// payment-methods
		// order-received
		// order-pay
		// add-payment-method
		// delete-payment-method
		// set-default-payment-method.
		return $url;
	}

	/**
	 * Filter View Order url.
	 *
	 * @param string    $url url.
	 * @param \WC_Order $order order object.
	 *
	 * @return string
	 */
	public function filter_view_order_url( $url, $order ) {

		if ( ! bcommerce_is_user_nav_item_enabled( 'orders' ) ) {
			return $url;
		}

		return bcommerce_get_user_view_order_permalink( bp_loggedin_user_id(), bp_loggedin_user_domain(), $order->get_id() );
	}
}