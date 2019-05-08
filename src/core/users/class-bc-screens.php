<?php
/**
 * User Screens Handler.
 *
 * @package    BuddyCommerce
 * @subpackage Core
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyCommerce\Core\Users;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Handles loading of templates on various screens.
 */
class BC_Screens {

	/**
	 * Singleton.
	 *
	 * @var BC_Screens
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return BC_Screens
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Load My Orders content.
	 */
	public function orders() {

		if ( bcommerce_is_user_view_order() ) {
			return;
		}

		add_action( 'bp_template_content', array( $this, 'content_orders' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );

	}

	/**
	 * Load View Order content.
	 */
	public function view_order() {
		add_action( 'bp_template_content', array( $this, 'content_view_order' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Load Track Orders content.
	 */
	public function track_orders() {
		add_action( 'bp_template_content', array( $this, 'content_track_orders' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Load My Downloads screen.
	 */
	public function downloads() {
		add_action( 'bp_template_content', array( $this, 'content_downloads' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Load Cart screen.
	 */
	public function cart() {

		if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}

		add_action( 'bp_template_content', array( $this, 'content_cart' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Load Checkout screen.
	 */
	public function checkout() {

		if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			define( 'WOOCOMMERCE_CHECKOUT', true );
		}

		add_action( 'bp_template_content', array( $this, 'content_checkout' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Load address screen content.
	 */
	public function addresses() {
		add_action( 'bp_template_content', array( $this, 'content_addresses' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Load Payment screen content.
	 */
	public function payment_methods() {
		add_action( 'bp_template_content', array( $this, 'content_payment_methods' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Load Payment screen content.
	 */
	public function add_payment_methods() {
		add_action( 'bp_template_content', array( $this, 'content_add_payment_methods' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Load My Subscriptions content.
	 *
	 * Plugin: WooCommerce subscriptions.
	 */
	public function subscriptions() {

		if ( bcommerce_is_user_view_subscription() ) {
			return;
		}

		add_action( 'bp_template_content', array( $this, 'content_subscriptions' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );

	}

	/**
	 * Load View Order content.
	 */
	public function view_subscription() {
		add_action( 'bp_template_content', array( $this, 'content_view_subscription' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Load content for Orders
	 */
	public function content_orders() {
		bcommerce_get_template_part( 'members/orders' );
	}

	/**
	 * Load content for View Order page
	 */
	public function content_view_order() {
		bcommerce_get_template_part( 'members/view-order' );
	}

	/**
	 * Load content for Track Orders
	 */
	public function content_track_orders() {
		bcommerce_get_template_part( 'members/track-orders' );
	}

	/**
	 * Load content for My Downloads
	 */
	public function content_downloads() {
		bcommerce_get_template_part( 'members/downloads' );
	}

	/**
	 * Load content for Addresses
	 */
	public function content_addresses() {
		bcommerce_get_template_part( 'members/addresses' );
	}

	/**
	 * Load content for My Cart
	 */
	public function content_cart() {
		bcommerce_get_template_part( 'members/cart' );
	}

	/**
	 * Load content for Checkout
	 */
	public function content_checkout() {
		bcommerce_get_template_part( 'members/checkout' );
	}
	/**
	 * Load content for Payment Methods
	 */
	public function content_payment_methods() {
		bcommerce_get_template_part( 'members/payment-methods' );
	}

	/**
	 * Load content for Add Payment Methods
	 */
	public function content_add_payment_methods() {
		bcommerce_get_template_part( 'members/add-payment-method' );
	}

	/**
	 * Load content for Orders
	 * Plugin: WooCommerce subscriptions.
	 */
	public function content_subscriptions() {
		bcommerce_get_template_part( 'members/subscriptions' );
	}

	/**
	 * Load content for View subscription page
	 * Plugin: WooCommerce subscriptions.
	 */
	public function content_view_subscription() {
		bcommerce_get_template_part( 'members/view-subscription' );
	}

}
