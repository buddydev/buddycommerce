<?php
/**
 * Conditional functions
 *
 * @package    BuddyCommerce
 * @subpackage templates\defaults\members
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Check if the given tab is currently being viewed.
 *
 * @param string $tab tab name.
 * @param bool   $endpoint is it end point.
 *
 * @return bool
 */
function bcommerce_is_current_tab( $tab, $endpoint = true ) {

	$slug = bcommerce_get_tab_slug( $tab, $endpoint );

	if ( bcommerce_is_top_level_user_nav_item( $tab ) ) {
		$is = bp_is_current_component( $slug );
	} else {
		$parent = bcommerce_get_user_nav_item_parent_slug_setting( $tab );
		if ( ! $parent ) {
			$parent = bcommerce_get_tab_slug( 'shop', false );
		}

		$is = bp_is_current_component( $parent ) && bp_is_current_action( $slug );
	}

	return $is;
}

/**
 * Is it a Woo page?
 *
 * @return bool
 */
function bcommerce_is_woo_page() {

	if ( ! bp_is_my_profile() && ! ( bp_is_user() && is_super_admin() ) ) {
		return false;
	}

	$component = bp_current_component();
	$action    = bp_current_action();

	$tab = bcommerce_get_woo_item_from_slug( $component );

	if ( $tab && bcommerce_is_top_level_user_nav_item( $tab ) && bcommerce_is_user_nav_item_enabled( $tab ) ) {
		return true;
	}

	if ( ! $action ) {
		return false;
	}

	$tab = bcommerce_get_woo_item_from_slug( $action );

	if ( $tab && ! bcommerce_is_top_level_user_nav_item( $tab ) && bcommerce_is_user_nav_item_enabled( $tab ) ) {
		return true;
	}

	return false;
}

/**
 * Given a slug, finds out the internal id for woo tab.
 *
 * @param string $slug slug.
 *
 * @return string
 */
function bcommerce_get_woo_item_from_slug( $slug ) {
	$tabs = bcommerce_get_wc_tabs_details();

	foreach ( $tabs as $tab => $tab_settings ) {
		$tab_slug = bcommerce_get_tab_slug( $tab, ! empty( $tab_settings['endpoint'] ) );

		if ( (string) $slug === $tab_slug ) {
			return $tab;
		}
	}

	return '';
}

/**
 * Is it the profile shop page.
 *
 * @return bool
 */
function bcommerce_is_user_shop() {
	if ( ! bcommerce_is_user_nav_item_enabled( 'shop' ) ) {
		return false;
	}

	return bcommerce_is_current_tab( 'shop', false );
}

/**
 * Is it profile order page?
 *
 * @return bool
 */
function bcommerce_is_user_orders() {
	if ( ! bcommerce_is_user_nav_item_enabled( 'orders' ) ) {
		return false;
	}

	return bcommerce_is_current_tab( 'orders', true );
}


/**
 * Is it profile order page?
 *
 * @return bool
 */
function bcommerce_is_user_view_order() {

	if ( ! bcommerce_is_user_orders() ) {
		return false;
	}

	$slug = bcommerce_get_tab_slug( 'view-order', true );
	if ( bcommerce_is_top_level_user_nav_item( 'orders' ) ) {
		$is = bp_is_current_action( $slug );
	} else {
		$is = bp_is_action_variable( $slug, 0 );
	}

	return $is;
}

/**
 * Is it user downloads page.
 *
 * @return bool
 */
function bcommerce_is_user_downloads() {
	if ( ! bcommerce_is_user_nav_item_enabled( 'downloads' ) ) {
		return false;
	}

	return bcommerce_is_current_tab( 'downloads', true );
}

/**
 * Is user cart page on profile?
 *
 * @return bool
 */
function bcommerce_is_user_cart() {
	if ( ! bcommerce_is_user_nav_item_enabled( 'cart' ) ) {
		return false;
	}

	return bcommerce_is_current_tab( 'cart', false );
}

/**
 * Is it user checkout page?
 *
 * @return bool
 */
function bcommerce_is_user_checkout() {
	if ( ! bcommerce_is_user_nav_item_enabled( 'checkout' ) ) {
		return false;
	}

	return bcommerce_is_current_tab( 'checkout', false );
}

/**
 * Is user payment methods page on profile?
 *
 * @return bool
 */
function bcommerce_is_user_payment_methods() {
	if ( ! bcommerce_is_user_nav_item_enabled( 'payment_methods' ) ) {
		return false;
	}

	return bcommerce_is_current_tab( 'payment_methods', true );
}

/**
 * Is user add payment methods page on profile?
 *
 * @return bool
 */
function bcommerce_is_user_add_payment_methods() {
	if ( ! bcommerce_is_user_payment_methods() ) {
		return false;
	}

	$slug = bcommerce_get_tab_slug( 'add-payment-method', true );
	if ( bcommerce_is_top_level_user_nav_item( 'payment_methods' ) ) {
		$is = bp_is_current_action( $slug );
	} else {
		$is = bp_is_action_variable( $slug, 0 );
	}

	return $is;
}

/**
 * Is it profile subscriptions page?
 *
 * @return bool
 */
function bcommerce_is_user_subscriptions() {
	if ( ! bcommerce_is_user_nav_item_enabled( 'subscriptions' ) ) {
		return false;
	}

	return bcommerce_is_current_tab( 'subscriptions', true );
}

/**
 * Is it profile view subscription page?
 *
 * @return bool
 */
function bcommerce_is_user_view_subscription() {

	if ( ! bcommerce_is_user_subscriptions() ) {
		return false;
	}

	$slug = bcommerce_get_tab_slug( 'view-subscription', true );
	if ( bcommerce_is_top_level_user_nav_item( 'subscriptions' ) ) {
		$is = bp_is_current_action( $slug );
	} else {
		$is = bp_is_action_variable( $slug, 0 );
	}

	return $is;
}