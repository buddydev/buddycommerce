<?php
/**
 * Link functions
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

/**
 * Get nav item permalink.
 *
 * @param int    $user_id user id.
 * @param string $user_url url.
 * @param string $tab tab id( orders|shop|downloads etc).
 * @param bool   $endpoint is end point.
 *
 * @return string
 */
function bcommerce_get_user_nav_item_permalink( $user_id, $user_url = '', $tab = '', $endpoint = false ) {

	if ( empty( $tab ) ) {
		return '';
	}

	$slug = bcommerce_get_tab_slug( $tab, $endpoint );
	if ( ! $user_url ) {
		$user_url = function_exists( 'bp_members_get_user_url' ) ? bp_members_get_user_url( $user_id ) : bp_core_get_user_domain( $user_id );
	}

	if ( bcommerce_is_top_level_user_nav_item( $tab ) ) {
		$url = trailingslashit( $user_url ) . trailingslashit( $slug );
	} else {
		$parent = bcommerce_get_user_nav_item_parent_slug_setting( $tab );
		if ( ! $parent ) {
			$parent = bcommerce_get_default_parent_tab_slug();
		}
		$url = trailingslashit( $user_url ) . $parent . '/' . $slug . '/';
	}

	return $url;
}

/**
 * Get mapped myaccount permalink.
 *
 * @param int    $user_id user id.
 * @param string $user_url user profile url.
 *
 * @return string
 */
function bcommerce_get_user_my_account_permalink( $user_id, $user_url = '' ) {
	if ( ! $user_url ) {
		$user_url = function_exists( 'bp_members_get_user_url' ) ? bp_members_get_user_url( $user_id ) : bp_core_get_user_domain( $user_id );
	}

	return trailingslashit( $user_url . bcommerce_get_default_parent_tab_slug() );
}

/**
 * Get mapped cart permalink.
 *
 * @param int    $user_id user id.
 * @param string $user_url user profile url.
 *
 * @return string
 */
function bcommerce_get_user_cart_permalink( $user_id, $user_url = '' ) {
	return bcommerce_get_user_nav_item_permalink( $user_id, $user_url, 'cart', false );
}

/**
 * Get mapped checkout permalink.
 *
 * @param int    $user_id user id.
 * @param string $user_url user profile url.
 *
 * @return string
 */
function bcommerce_get_user_checkout_permalink( $user_id, $user_url = '' ) {
	return bcommerce_get_user_nav_item_permalink( $user_id, $user_url, 'checkout', false );
}

/**
 * Get mapped Orders.
 *
 * @param int    $user_id user id.
 * @param string $user_url user profile url.
 *
 * @return string
 */
function bcommerce_get_user_orders_permalink( $user_id, $user_url = '' ) {
	return bcommerce_get_user_nav_item_permalink( $user_id, $user_url, 'orders', true );
}

/**
 * Get View Order url.
 *
 * @param int    $user_id user id.
 * @param string $user_url user profile url.
 *
 * @return string
 */
function bcommerce_get_user_view_order_permalink( $user_id, $user_url = '', $order_id = 0 ) {

	$endpoint = bcommerce_get_endpoint_slug( 'view-order' );

	return bcommerce_get_user_orders_permalink( bp_loggedin_user_id() ) . trailingslashit( $endpoint ) . $order_id . '/';
}

/**
 * Get mapped Track Orders url.
 *
 * @param int    $user_id user id.
 * @param string $user_url user profile url.
 *
 * @return string
 */
function bcommerce_get_user_track_orders_permalink( $user_id, $user_url = '' ) {
	return bcommerce_get_user_nav_item_permalink( $user_id, $user_url, 'track_orders', false );
}

/**
 * Get mapped Downloads.
 *
 * @param int    $user_id user id.
 * @param string $user_url user profile url.
 *
 * @return string
 */
function bcommerce_get_user_downloads_permalink( $user_id, $user_url = '' ) {
	return bcommerce_get_user_nav_item_permalink( $user_id, $user_url, 'downloads', true );
}

/**
 * Get mapped Address url.
 *
 * @param int    $user_id user id.
 * @param string $user_url user profile url.
 * @param string $address_type Address type.
 *
 * @return string
 */
function bcommerce_get_user_address_permalink( $user_id, $user_url = '', $address_type = '' ) {
	$url = bcommerce_get_user_nav_item_permalink( $user_id, $user_url, 'addresses', false );

	if ( $address_type ) {
		$url .= trailingslashit( $address_type );
	}

	return $url;
}

/**
 * Get mapped Downloads.
 *
 * @param int    $user_id user id.
 * @param string $user_url user profile url.
 *
 * @return string
 */
function bcommerce_get_user_payment_methods_permalink( $user_id, $user_url = '' ) {
	return bcommerce_get_user_nav_item_permalink( $user_id, $user_url, 'payment_methods', true );
}



/**
 * Get mapped subscriptions url.
 *
 * @param int    $user_id user id.
 * @param string $user_url user profile url.
 *
 * @return string
 */
function bcommerce_get_user_subscriptions_permalink( $user_id, $user_url = '' ) {
	return bcommerce_get_user_nav_item_permalink( $user_id, $user_url, 'subscriptions', true );
}

/**
 * Get View subscription url.
 *
 * @param int    $user_id user id.
 * @param string $user_url user profile url.
 *
 * @return string
 */
function bcommerce_get_user_view_subscription_permalink( $user_id, $user_url = '', $order_id = 0 ) {

	$endpoint   = bcommerce_get_endpoint_slug( 'view-subscription' );

	return bcommerce_get_user_subscriptions_permalink( bp_loggedin_user_id() ) . trailingslashit( $endpoint ) . $order_id . '/';
}
