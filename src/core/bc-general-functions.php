<?php
/**
 * Common functions.
 *
 * @package    BuddyCommerce
 * @subpackage Core
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
use BuddyCommerce\Core\Users\BC_Screens;

defined( 'ABSPATH' ) || exit;

/**
 * Get the option value.
 *
 * @param string $option_name option name.
 * @param mixed  $default default value for the option.
 *
 * @return mixed
 */
function bcommerce_get_option( $option_name, $default = null ) {
	$options = get_option( 'buddycommerce_settings', array() );
	$value   = $default;

	if ( isset( $options[ $option_name ] ) ) {
		$value = $options[ $option_name ];
	}

	return $value;
}

/**
 * Get default settings.
 *
 * @return array
 */
function bcommerce_get_default_options() {

	$defaults = array(
		'admin_bar_priority_id' => 99,
	);
	$tabs     = bcommerce_get_wc_tabs_details();
	$position = 85;
	foreach ( $tabs as $tab => $args ) {

		$defaults = array_merge(
			$defaults,
			array(
				"is_user_{$tab}_enabled"              => isset( $args['enabled'] ) ? $args['enabled'] : 1,
				"user_nav_{$tab}_label"               => isset( $args['label'] ) ? $args['label'] : '',
				"user_nav_{$tab}_slug"                => isset( $args['slug'] ) ? $args['slug'] : '',
				"user_nav_{$tab}_position"            => isset( $args['position'] ) ? $args['position'] : $position,
				"is_user_{$tab}_top_level_item"       => isset( $args['is_top_level'] ) ? $args['is_top_level'] : 0,
				"user_nav_{$tab}_parent_slug"         => isset( $args['parent_slug'] ) ? $args['parent_slug'] : '',
				"user_nav_{$tab}_parent_admin_bar_id" => isset( $args['parent_admin_bar_id'] ) ? $args['parent_admin_bar_id'] : '',
				"user_nav_{$tab}_redirect"            => isset( $args['redirect'] ) ? $args['redirect'] : 0,
			)
		);
		$position = $position + 10;
	}

	return $defaults;
}

/**
 * Get all woo tabs details.
 *
 * @return array
 */
function bcommerce_get_wc_tabs_details() {

	$tabs = array(

		'shop'            => array(
			'enabled'              => 1,
			'endpoint'             => false,
			'label'                => __( 'Shop', 'buddycommerce' ),
			'desc'                 => __( 'Shop tab settings for profile.', 'buddycommerce' ),
			'slug'                 => 'shop',
			'is_top_level'         => 1,
			'redirect'             => 1,
			'redirect_description' => __( 'Redirect WooCommerce my account page to BuddyPress profile page', 'buddycommerce' ),
			'toplevel_only'        => true,
		),
		'orders'          => array(
			'enabled'              => 1,
			'endpoint'             => true,
			'label'                => __( 'Order', 'buddycommerce' ),
			'desc'                 => __( 'Order tab settings.', 'buddycommerce' ),
			'is_top_level'         => 0,
			'redirect'             => 1,
			'redirect_description' => __( 'Redirect WooCommerce orders page to BuddyPress profile page', 'buddycommerce' ),
		),
		'track_orders'    => array(
			'enabled'      => 1,
			'endpoint'     => false,
			'label'        => __( 'Track Order', 'buddycommerce' ),
			'desc'         => __( 'Order tracking tab settings.', 'buddycommerce' ),
			'slug'         => 'track-orders',
			'is_top_level' => 0,
		),
		'downloads'       => array(
			'enabled'              => 1,
			'endpoint'             => true,
			'label'                => __( 'Downloads', 'buddycommerce' ),
			'desc'                 => __( 'Downloads tab settings.', 'buddycommerce' ),
			'is_top_level'         => 0,
			'redirect'             => 1,
			'redirect_description' => __( 'Redirect WooCommerce my downloads page to BuddyPress profile page', 'buddycommerce' ),
		),
		'addresses'       => array(
			'enabled'              => 1,
			'endpoint'             => false,
			'label'                => __( 'Addresses', 'buddycommerce' ),
			'slug'                 => 'addresses',
			'desc'                 => __( 'Addresses tab settings.', 'buddycommerce' ),
			'is_top_level'         => 0,
			'redirect'             => 1,
			'redirect_description' => __( 'Redirect WooCommerce addresses page to BuddyPress profile page', 'buddycommerce' ),
		),
		'payment_methods' => array(
			'enabled'              => 1,
			'endpoint'             => true,
			'label'                => __( 'Payment Methods', 'buddycommerce' ),
			'slug'                 => 'payment-methods',
			'desc'                 => __( 'Payment method tab settings.', 'buddycommerce' ),
			'is_top_level'         => 0,
			'redirect'             => 1,
			'redirect_description' => __( 'Redirect WooCommerce payment methods page to BuddyPress profile page', 'buddycommerce' ),
		),
		'cart'            => array(
			'enabled'              => 0,
			'endpoint'             => false,
			'label'                => __( 'Cart', 'buddycommerce' ),
			'slug'                 => 'cart',
			'desc'                 => __( 'Add WooCommerce Cart page as profile tab.', 'buddycommerce' ),
			'is_top_level'         => 0,
			'redirect'             => 1,
			'redirect_description' => __( 'Redirect site cart page to BuddyPress profile cart page', 'buddycommerce' ),
		),
		'checkout'        => array(
			'enabled'              => 0,
			'endpoint'             => false,
			'label'                => __( 'Checkout', 'buddycommerce' ),
			'slug'                 => 'checkout',
			'desc'                 => __( 'Add WooCommerce checkout page as user profile tab. We recommend not enabling it.', 'buddycommerce' ),
			'is_top_level'         => 0,
			'redirect'             => 1,
			'redirect_description' => __( 'Redirect site checkout page to BuddyPress profile checkout page', 'buddycommerce' ),
		),
	);

	if ( class_exists( 'WC_Subscriptions' ) ) {
		$tabs['subscriptions'] = array(
			'enabled'              => 0,
			'endpoint'             => true,
			'label'                => __( 'Subscriptions', 'buddycommerce' ),
			// 'slug'                 => 'subscriptions',
			'desc'                 => __( 'Subscription tab settings.', 'buddycommerce' ),
			'is_top_level'         => 0,
			'redirect'             => 1,
			'redirect_description' => __( 'Redirect WooCommerce subscription page to BuddyPress profile page', 'buddycommerce' ),
		);
	}

	if ( class_exists( 'WC_Memberships_Loader' ) ) {
		$tabs['members_area'] = array(
			'enabled'              => 0,
			'endpoint'             => true,
			'label'                => __( 'Memberships', 'buddycommerce' ),
			// 'slug'                 => 'subscriptions',
			'desc'                 => __( 'Membership tab settings.', 'buddycommerce' ),
			'is_top_level'         => 0,
			'redirect'             => 1,
			'redirect_description' => __( 'Redirect WooCommerce My membership page to BuddyPress profile page', 'buddycommerce' ),
		);
	}

	return apply_filters( 'bcommerce_wc_tabs', $tabs );
}

/**
 * Get the tab slug.
 *
 * @param string $tab tab/end point name which are internally identifiable(orders|downloads etc).
 * @param bool   $is_endpoint is it an end point.
 *
 * @return string
 */
function bcommerce_get_tab_slug( $tab, $is_endpoint = null ) {

	$slug = bcommerce_get_user_nav_item_slug_setting( $tab );

	if ( is_null( $is_endpoint ) ) {
		$wootabs     = bcommerce_get_wc_tabs_details();
		$is_endpoint = isset( $wootabs[ $tab ] ) && ! empty( $wootabs[ $tab ]['endpoint'] );
	}

	if ( ! $slug && $is_endpoint ) {
		// most probably a user set woo end point.
		// Bad way to do it(replacing underscore with dash)
		// we should add this details to our woo tabs and get from there.
		$slug = bcommerce_get_endpoint_slug( str_replace( '_', '-', $tab ) );
	}

	if ( ! $slug ) {
		$slug = $tab;
	}

	return $slug;
}

/**
 * Get fallback parent tab slug.
 *
 * @return string
 */
function bcommerce_get_default_parent_tab_slug() {
	return apply_filters( 'bcommerce_default_account_tab_slug', bcommerce_get_tab_slug( 'shop', false ) );
}

/**
 * Get fallback parent tab admin bar id
 *
 * @return string
 */
function bcommerce_get_default_parent_tab_admin_bar_id() {
	return apply_filters( 'bcommerce_default_account_tab_adminbar_id', 'bcommerce-shop' );
}

/**
 * Is the nav item enabled?
 *
 * @param string $tab tab name.
 *
 * @return bool
 */
function bcommerce_is_user_nav_item_enabled( $tab ) {
	return bcommerce_get_option( "is_user_{$tab}_enabled", false );
}

/**
 * Is the nav tab redirect enabled.
 *
 * @param string $tab tab id.
 *
 * @return bool
 */
function bcommerce_is_user_nav_item_redirect_enabled( $tab ) {
	return bcommerce_get_option( "user_nav_{$tab}_redirect", false );
}

/**
 * Is it a top level user nav item?
 *
 * @param string $tab Tab/Component name eg.('downloads', 'orders', 'cart', 'checkout' etc).
 *
 * @return bool
 */
function bcommerce_is_top_level_user_nav_item( $tab ) {

	return bcommerce_get_option( "is_user_{$tab}_top_level_item", null );
}

/**
 * Get User nav item slug.
 *
 * @param string $tab Tab id(shop|downloads etc).
 *
 * @return string
 */
function bcommerce_get_user_nav_item_slug_setting( $tab ) {
	return bcommerce_get_option( "user_nav_{$tab}_slug", null );
}

/**
 * Get User nav item slug.
 *
 * @param string $tab Tab id(shop|downloads etc).
 *
 * @return string
 */
function bcommerce_get_user_nav_item_label_setting( $tab ) {
	return bcommerce_get_option( "user_nav_{$tab}_label", null );
}
/**
 * Get parent nav item slug.
 *
 * @param string $tab component.
 *
 * @return string
 */
function bcommerce_get_user_nav_item_parent_slug_setting( $tab ) {
	return bcommerce_get_option( "user_nav_{$tab}_parent_slug", 'shop' );
}

/**
 * Get User nav item position.
 *
 * @param string $tab Tab id(shop|downloads etc).
 *
 * @return int
 */
function bcommerce_get_user_nav_item_position_setting( $tab ) {
	return bcommerce_get_option( "user_nav_{$tab}_position", null );
}

/**
 * Get User nav item visibility.
 *
 * @param string $tab Tab id(shop|downloads etc).
 *
 * @return int
 */
function bcommerce_get_user_nav_item_visibility_setting( $tab ) {
	return bcommerce_get_option( "user_nav_{$tab}_visibility", null );
}


/**
 * Get parent nav admin bar id.
 *
 * @param string $tab Tab id(shop|downloads etc).
 *
 * @return string
 */
function bcommerce_get_user_nav_item_parent_admin_bar_id_setting( $tab ) {
	return bcommerce_get_option( "user_nav_{$tab}_parent_admin_bar_id", 'shop' );
}

/**
 * Get screen callback.
 *
 * @param string $tab tab id.
 *
 * @return callable
 */
function bcommerce_get_view_callback( $tab ) {
	$screen = BC_Screens::get_instance();

	$callback = null;
	switch ( $tab ) {
		case 'shop':
			break;

		case 'orders':
			$callback = array( $screen, 'orders' );
			break;
		case 'view_order':
			$callback = array( $screen, 'view_order' );
			break;

		case 'track_orders':
			$callback = array( $screen, 'track_orders' );
			break;

		case 'downloads':
			$callback = array( $screen, 'downloads' );
			break;

		case 'addresses':
			$callback = array( $screen, 'addresses' );
			break;
		case 'payment_methods':
			$callback = array( $screen, 'payment_methods' );
			break;
		case 'cart':
			$callback = array( $screen, 'cart' );
			break;

		case 'checkout':
			$callback = array( $screen, 'checkout' );
			break;
		case 'subscriptions':
			$callback = array( $screen, 'subscriptions' );
			break;
		case 'members_area':
			$callback = array( $screen, 'memberships' );
			break;
	}

	$callback = apply_filters( 'bcommerce_tab_view_callback', $callback, $tab );

	if ( ! $callback || ! is_callable( $callback ) ) {
		$callback = '__return_false';
	}

	return $callback;
}
/**
 * Get endpoint slug.
 *
 * @param string $endpoint endpoint name.
 *
 * @return string
 */
function bcommerce_get_endpoint_slug( $endpoint ) {
	$query_vars = WC()->query->get_query_vars();

	if ( 'members-area' === $endpoint && function_exists( 'wc_memberships_get_members_area_endpoint' ) ) {
		$endpoint = wc_memberships_get_members_area_endpoint();
	}

	return ! empty( $query_vars[ $endpoint ] ) ? $query_vars[ $endpoint ] : $endpoint;
}

/**
 * Get endpoint from slug.
 *
 * @param string $slug current slug.
 *
 * @return string|null
 */
function bcommerce_get_endpoint_from_slug( $slug ) {
	$query_vars = WC()->query->get_query_vars();
	foreach ( $query_vars as $endpoint => $ep_slug ) {
		if ( $ep_slug === $slug ) {
			return $endpoint;
		}
	}

	return null;
}

/**
 * Get all wooCommerce end points.
 *
 * @return array
 */
function bcommerce_get_endpoints() {
	return WC()->query->get_query_vars();
}

/**
 * Get current page number for paginated pages.
 *
 * @param string $tab tab id.
 *
 * @return int
 */
function bcommerce_get_current_page_number( $tab ) {

	if ( bcommerce_is_top_level_user_nav_item( $tab ) ) {
		$current_page = absint( bp_current_action() );
	} else {
		$current_page = absint( bp_action_variable( 0 ) );
	}

	if ( ! $current_page ) {
		$current_page = 1;
	}

	return $current_page;
}

/**
 * No-op format notifications slug.
 */
function bcommerce_format_notifications() {
	// do nothing.
}

/**
 * Verify nonce.
 *
 * @param string $action action name.
 * @param string $name nonce form field name.
 * @param string $method post method(POST|GET).
 *
 * @return bool|int
 */
function bcommerce_verify_nonce( $action, $name = '_wpnonce', $method = 'POST' ) {

	if ( empty( $method ) || empty( $name ) ) {
		return false;
	}

	$method = strtoupper( $method );

	if ( 'POST' === $method ) {
		$data = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : '';
	} else {
		$data = isset( $_GET[ $name ] ) ? wp_unslash( $_GET[ $name ] ) : '';
	}

	return wp_verify_nonce( $data, $action );
}

/**
 * Sanitize input value. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $input_var Data to sanitize.
 *
 * @return string|array
 */
function bcommerce_sanitize_input( $input_var ) {
	if ( is_array( $input_var ) ) {
		return array_map( 'bcommerce_sanitize_input', $input_var );
	} else {
		return is_scalar( $input_var ) ? sanitize_text_field( $input_var ) : $input_var;
	}
}
