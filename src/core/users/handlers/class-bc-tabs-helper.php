<?php
/**
 * BuddyCommerce Edit Address Screen end point handler.
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
defined( 'ABSPATH' ) || exit;

/**
 * Helps manage tabs.
 */
class BC_Tabs_Helper {

	/**
	 * BC_Bootstrapper constructor.
	 */
	private function __construct() {
	}

	/**
	 * Setup the bootstrapper.
	 *
	 * @return BC_Tabs_Helper
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
	 * Register hooks.
	 */
	private function register() {
		add_action( 'bp_setup_nav', array( $this, 'setup_nav' ), 200 );

		$admin_bar_action_priority = bcommerce_get_option( 'admin_bar_priority_id', 99 );
		if ( ! $admin_bar_action_priority ) {
			$admin_bar_action_priority = 99;
		}
		add_action( 'bp_setup_admin_bar', array( $this, 'setup_admin_bar' ), $admin_bar_action_priority );
	}


	/**
	 * Setup admin bar menu items.
	 */
	public function setup_admin_bar() {

		if ( ! is_admin_bar_showing() || ! is_user_logged_in() ) {
			return;
		}

		// Bail if this is an ajax request.
		if ( defined( 'DOING_AJAX' ) ) {
			return;
		}

		// Do not proceed if BP_USE_WP_ADMIN_BAR constant is not set or is false.
		if ( ! bp_use_wp_admin_bar() ) {
			return;
		}

		global $wp_admin_bar;
		$bp_my_account_id = buddypress()->my_account_menu_id;
		$user_id          = bp_loggedin_user_id();
		$user_domain      = bp_loggedin_user_domain();

		$tabs = bcommerce_get_wc_tabs_details();

		uksort( $tabs, array( $this, 'sort' ) );

		foreach ( $tabs as $tab => $args ) {
			if ( ! bcommerce_is_user_nav_item_enabled( $tab ) ) {
				continue;
			}
			// It is a top level item.
			if ( bcommerce_is_top_level_user_nav_item( $tab ) ) {
				$wp_admin_bar->add_menu(
					array(
						'parent' => $bp_my_account_id,
						'id'     => sanitize_title_with_dashes( 'bcommerce-' . $tab ),
						'title'  => $this->get_label( $tab, $args['label'] ),
						'href'   => bcommerce_get_user_nav_item_permalink( $user_id, $user_domain, $tab, ! empty( $args['endpoint'] ) ),
					)
				);
			} else {
				// it is a child tab of some tab.
				$parent_admin_bar_id = bcommerce_get_user_nav_item_parent_slug_setting( $tab );
				if ( ! $parent_admin_bar_id ) {
					$parent_admin_bar_id = bcommerce_get_default_parent_tab_admin_bar_id();
				}
				$wp_admin_bar->add_menu(
					array(
						'parent' => $parent_admin_bar_id,
						'id'     => sanitize_title_with_dashes( 'bcommerec-' . $tab ),
						'title'  => $this->get_label( $tab, $args['label'] ),
						'href'   => bcommerce_get_user_nav_item_permalink( $user_id, $user_domain, $tab, ! empty( $args['endpoint'] ) ),
					)
				);
			}
		}
	}

	/**
	 * Sort by position.
	 *
	 * @param string $tab1 tab id.
	 * @param string $tab2 tab id.
	 *
	 * @return int
	 */
	public function sort( $tab1, $tab2 ) {
		$p1 = bcommerce_get_user_nav_item_position_setting( $tab1 );
		$p2 = bcommerce_get_user_nav_item_position_setting( $tab2 );

		if ( $p1 < $p2 ) {
			return - 1;
		} elseif ( $p1 == $p2 ) {
			return 0;
		} else {
			return 1;
		}
	}


	/**
	 * Add tabs.
	 */
	public function setup_nav() {
		// Determine user to use.
		if ( bp_displayed_user_domain() ) {
			$user_id     = bp_displayed_user_id();
			$user_domain = bp_displayed_user_domain();
		} elseif ( bp_loggedin_user_domain() ) {
			$user_domain = bp_loggedin_user_domain();
			$user_id     = bp_loggedin_user_id();
		} else {
			return;
		}

		$access = bp_core_can_edit_settings();
		$args   = array(
			'access'      => $access,
			'user_domain' => $user_domain,
		);

		$this->add_shop_tab( $user_id, $args );
		$this->add_cart_tab( $user_id, $args );
		$this->add_checkout_tab( $user_id, $args );
		$this->add_orders_tab( $user_id, $args );
		$this->add_track_orders_tab( $user_id, $args );
		$this->add_downloads_tab( $user_id, $args );
		$this->add_addresses_tab( $user_id, $args );
		$this->add_payment_methods_tab( $user_id, $args );

		if ( class_exists( 'WC_Subscriptions' ) ) {
			$this->add_subscriptions_tab( $user_id, $args );
		}

		if ( class_exists( 'WC_Memberships_Loader' ) ) {
			$this->add_membership_tab( $user_id, $args );
		}
	}

	/**
	 * Add shop tab for user.
	 *
	 * @param int   $user_id user id.
	 * @param array $args args.
	 */
	private function add_orders_tab( $user_id, $args ) {
		$tab = 'orders';
		if ( ! bcommerce_is_user_nav_item_enabled( $tab ) ) {
			return;
		}

		$tab_slug       = bcommerce_get_tab_slug( $tab, true );

		if ( bcommerce_is_top_level_user_nav_item( $tab ) ) {
			bp_core_new_nav_item(
				array(
					'name'                    => $this->get_label( 'orders', __( 'Orders', 'buddycommerce' ), wc_get_customer_order_count( $user_id ) ),
					'slug'                    => $tab_slug,
					'position'                => bcommerce_get_user_nav_item_position_setting( $tab ),
					'screen_function'         => bcommerce_get_view_callback( $tab ),
					'default_subnav_slug'     => $tab,
					'show_for_displayed_user' => $args['access'],
				)
			);

			return;
		}

		$parent_slug = bcommerce_get_user_nav_item_parent_slug_setting( $tab );
		if ( ! $parent_slug ) {
			$parent_slug = bcommerce_get_tab_slug( 'shop', false );
		}

		bp_core_new_subnav_item(
			array(
				'name'            => $this->get_label( 'orders', __( 'Orders', 'buddycommerce' ) ),
				'slug'            => $tab_slug,
				'parent_url'      => trailingslashit( $args['user_domain'] . $parent_slug ),
				'parent_slug'     => $parent_slug,
				'screen_function' => bcommerce_get_view_callback( $tab ),
				'position'        => bcommerce_get_user_nav_item_position_setting( $tab ),
				'user_has_access' => $args['access'],
			)
		);
	}
	/**
	 * Add shop tab for user.
	 *
	 * @param int   $user_id user id.
	 * @param array $args args.
	 */
	private function add_track_orders_tab( $user_id, $args ) {
		$tab = 'track_orders';
		if ( ! bcommerce_is_user_nav_item_enabled( $tab ) ) {
			return;
		}

		$tab_slug       = bcommerce_get_tab_slug( $tab, false );

		if ( bcommerce_is_top_level_user_nav_item( $tab ) ) {
			bp_core_new_nav_item(
				array(
					'name'                    => $this->get_label( 'track_orders', __( 'Track Orders', 'buddycommerce' ) ),
					'slug'                    => $tab_slug,
					'position'                => bcommerce_get_user_nav_item_position_setting( $tab ),
					'screen_function'         => bcommerce_get_view_callback( $tab ),
					'default_subnav_slug'     => $tab,
					'show_for_displayed_user' => $args['access'],
				)
			);

			return;
		}

		$parent_slug = bcommerce_get_user_nav_item_parent_slug_setting( $tab );
		if ( ! $parent_slug ) {
			$parent_slug = bcommerce_get_tab_slug( 'shop', false );
		}

		bp_core_new_subnav_item(
			array(
				'name'            => $this->get_label( 'track_orders', __( 'Track Orders', 'buddycommerce' ) ),
				'slug'            => $tab_slug,
				'parent_url'      => trailingslashit( $args['user_domain'] . $parent_slug ),
				'parent_slug'     => $parent_slug,
				'screen_function' => bcommerce_get_view_callback( $tab ),
				'position'        => bcommerce_get_user_nav_item_position_setting( $tab ),
				'user_has_access' => $args['access'],
			)
		);
	}

	/**
	 * Add shop tab for user.
	 *
	 * @param int   $user_id user id.
	 * @param array $args args.
	 */
	private function add_downloads_tab( $user_id, $args ) {
		$tab = 'downloads';
		if ( ! bcommerce_is_user_nav_item_enabled( $tab ) ) {
			return;
		}

		$tab_slug       = bcommerce_get_tab_slug( $tab, true );

		if ( bcommerce_is_top_level_user_nav_item( $tab ) ) {
			bp_core_new_nav_item(
				array(
					'name'                    => $this->get_label( 'downloads', __( 'Downloads', 'buddycommerce' ) ),
					'slug'                    => $tab_slug,
					'position'                => bcommerce_get_user_nav_item_position_setting( $tab ),
					'screen_function'         => bcommerce_get_view_callback( $tab ),
					'default_subnav_slug'     => $tab,
					'show_for_displayed_user' => $args['access'],
				)
			);

			return;
		}

		$parent_slug = bcommerce_get_user_nav_item_parent_slug_setting( $tab );
		if ( ! $parent_slug ) {
			$parent_slug = bcommerce_get_tab_slug( 'shop', false );
		}

		bp_core_new_subnav_item(
			array(
				'name'            => $this->get_label( 'downloads', __( 'Downloads', 'buddycommerce' ) ),
				'slug'            => $tab_slug,
				'parent_url'      => trailingslashit( $args['user_domain'] . $parent_slug ),
				'parent_slug'     => $parent_slug,
				'screen_function' => bcommerce_get_view_callback( $tab ),
				'position'        => bcommerce_get_user_nav_item_position_setting( $tab ),
				'user_has_access' => $args['access'],
			)
		);
	}

	/**
	 * Add shop tab for user.
	 *
	 * @param int   $user_id user id.
	 * @param array $args args.
	 */
	private function add_addresses_tab( $user_id, $args ) {
		$tab = 'addresses';
		if ( ! bcommerce_is_user_nav_item_enabled( $tab ) ) {
			return;
		}

		$tab_slug       = bcommerce_get_tab_slug( $tab, false );

		if ( bcommerce_is_top_level_user_nav_item( $tab ) ) {
			bp_core_new_nav_item(
				array(
					'name'                    => $this->get_label( 'addresses', __( 'Addresses', 'buddycommerce' ) ),
					'slug'                    => $tab_slug,
					'position'                => bcommerce_get_user_nav_item_position_setting( $tab ),
					'screen_function'         => bcommerce_get_view_callback( $tab ),
					'default_subnav_slug'     => $tab,
					'show_for_displayed_user' => $args['access'],
				)
			);

			return;
		}

		$parent_slug = bcommerce_get_user_nav_item_parent_slug_setting( $tab );
		if ( ! $parent_slug ) {
			$parent_slug = bcommerce_get_tab_slug( 'shop', false );
		}

		bp_core_new_subnav_item(
			array(
				'name'            => $this->get_label( 'addresses', __( 'Addresses', 'buddycommerce' ) ),
				'slug'            => $tab_slug,
				'parent_url'      => trailingslashit( $args['user_domain'] . $parent_slug ),
				'parent_slug'     => $parent_slug,
				'screen_function' => bcommerce_get_view_callback( $tab ),
				'position'        => bcommerce_get_user_nav_item_position_setting( $tab ),
				'user_has_access' => $args['access'],
			)
		);
	}

	/**
	 * Add shop tab for user.
	 *
	 * @param int   $user_id user id.
	 * @param array $args args.
	 */
	private function add_payment_methods_tab( $user_id, $args ) {
		$tab = 'payment_methods';
		if ( ! bcommerce_is_user_nav_item_enabled( $tab ) ) {
			return;
		}

		$tab_slug       = bcommerce_get_tab_slug( $tab, true );

		if ( bcommerce_is_top_level_user_nav_item( $tab ) ) {
			bp_core_new_nav_item(
				array(
					'name'                    => $this->get_label( 'payment_methods', __( 'Payment Methods', 'buddycommerce' ) ),
					'slug'                    => $tab_slug,
					'position'                => bcommerce_get_user_nav_item_position_setting( $tab ),
					'screen_function'         => bcommerce_get_view_callback( $tab ),
					'default_subnav_slug'     => $tab_slug,
					'show_for_displayed_user' => $args['access'],
				)
			);

			return;
		}

		$parent_slug = bcommerce_get_user_nav_item_parent_slug_setting( $tab );
		if ( ! $parent_slug ) {
			$parent_slug = bcommerce_get_tab_slug( 'shop', false );
		}

		bp_core_new_subnav_item(
			array(
				'name'            => $this->get_label( 'payment_methods', __( 'Payment Methods', 'buddycommerce' ) ),
				'slug'            => $tab_slug,
				'parent_url'      => trailingslashit( $args['user_domain'] . $parent_slug ),
				'parent_slug'     => $parent_slug,
				'screen_function' => bcommerce_get_view_callback( $tab ),
				'position'        => bcommerce_get_user_nav_item_position_setting( $tab ),
				'user_has_access' => $args['access'],
			)
		);
	}

	/**
	 * Add shop tab for user.
	 *
	 * @param int   $user_id user id.
	 * @param array $args args.
	 */
	private function add_cart_tab( $user_id, $args ) {
		$tab = 'cart';
		if ( ! bcommerce_is_user_nav_item_enabled( $tab ) ) {
			return;
		}

		$tab_slug       = bcommerce_get_tab_slug( $tab, false );

		if ( bcommerce_is_top_level_user_nav_item( $tab ) ) {
			bp_core_new_nav_item(
				array(
					'name'                    => $this->get_label( $tab, __( 'Cart', 'buddycommerce' ) ),
					'slug'                    => $tab_slug,
					'position'                => bcommerce_get_user_nav_item_position_setting( $tab ),
					'screen_function'         => bcommerce_get_view_callback( $tab ),
					'default_subnav_slug'     => $tab,
					'show_for_displayed_user' => $args['access'],
				)
			);

			return;
		}

		$parent_slug = bcommerce_get_user_nav_item_parent_slug_setting( $tab );
		if ( ! $parent_slug ) {
			$parent_slug = bcommerce_get_tab_slug( 'shop', false );
		}

		bp_core_new_subnav_item(
			array(
				'name'            => $this->get_label( $tab, __( 'Cart', 'buddycommerce' ) ),
				'slug'            => $tab_slug,
				'parent_url'      => trailingslashit( $args['user_domain'] . $parent_slug ),
				'parent_slug'     => $parent_slug,
				'screen_function' => bcommerce_get_view_callback( $tab ),
				'position'        => bcommerce_get_user_nav_item_position_setting( $tab ),
				'user_has_access' => $args['access'],
			)
		);
	}

	/**
	 * Add shop tab for user.
	 *
	 * @param int   $user_id user id.
	 * @param array $args args.
	 */
	private function add_checkout_tab( $user_id, $args ) {
		$tab = 'checkout';
		if ( ! bcommerce_is_user_nav_item_enabled( $tab ) ) {
			return;
		}

		$tab_slug       = bcommerce_get_tab_slug( $tab, false );

		if ( bcommerce_is_top_level_user_nav_item( $tab ) ) {
			bp_core_new_nav_item(
				array(
					'name'                    => $this->get_label( $tab, __( 'Checkout', 'buddycommerce' ) ),
					'slug'                    => $tab_slug,
					'position'                => bcommerce_get_user_nav_item_position_setting( $tab ),
					'screen_function'         => bcommerce_get_view_callback( $tab ),
					'default_subnav_slug'     => $tab,
					'show_for_displayed_user' => $args['access'],
				)
			);

			return;
		}

		$parent_slug = bcommerce_get_user_nav_item_parent_slug_setting( $tab );
		if ( ! $parent_slug ) {
			$parent_slug = bcommerce_get_tab_slug( 'shop', false );
		}

		bp_core_new_subnav_item(
			array(
				'name'            => $this->get_label( $tab, __( 'Checkout', 'buddycommerce' ) ),
				'slug'            => $tab_slug,
				'parent_url'      => trailingslashit( $args['user_domain'] . $parent_slug ),
				'parent_slug'     => $parent_slug,
				'screen_function' => bcommerce_get_view_callback( $tab ),
				'position'        => bcommerce_get_user_nav_item_position_setting( $tab ),
				'user_has_access' => $args['access'],
			)
		);
	}


	/**
	 * Add shop tab for user.
	 *
	 * @param int   $user_id user id.
	 * @param array $args args.
	 */
	private function add_shop_tab( $user_id, $args ) {

		$tab = 'shop';
		if ( ! bcommerce_is_user_nav_item_enabled( $tab ) ) {
			return;
		}


		// shop must be top level item.
		if ( ! bcommerce_is_top_level_user_nav_item( $tab ) ) {
			return;
		}

		$tab_slug       = bcommerce_get_tab_slug( $tab, false );
		$default_subtab = $this->get_subtab( $tab_slug );

		bp_core_new_nav_item(
			array(
				'name'                    => $this->get_label( $tab, __( 'Shopping', 'buddycommerce' ), wc_get_customer_order_count( $user_id ) ),
				'slug'                    => $tab_slug,
				'position'                => bcommerce_get_user_nav_item_position_setting( $tab ),
				'screen_function'         => bcommerce_get_view_callback( $default_subtab ),
				'default_subnav_slug'     => bcommerce_get_tab_slug( $default_subtab ),
				'show_for_displayed_user' => $args['access'],
			)
		);

	}

	/**
	 * Add Subscriptions tab for user.
	 *
	 * @param int   $user_id user id.
	 * @param array $args args.
	 */
	private function add_subscriptions_tab( $user_id, $args ) {

		$tab = 'subscriptions';
		if ( ! bcommerce_is_user_nav_item_enabled( $tab ) ) {
			return;
		}

		$tab_slug       = bcommerce_get_tab_slug( $tab, true );

		if ( bcommerce_is_top_level_user_nav_item( $tab ) ) {
			bp_core_new_nav_item(
				array(
					'name'                    => $this->get_label( $tab, __( 'Subscriptions', 'buddycommerce' ) ),
					'slug'                    => $tab_slug,
					'position'                => bcommerce_get_user_nav_item_position_setting( $tab ),
					'screen_function'         => bcommerce_get_view_callback( $tab ),
					'default_subnav_slug'     => $tab,
					'show_for_displayed_user' => $args['access'],
				)
			);

			return;
		}

		$parent_slug = bcommerce_get_user_nav_item_parent_slug_setting( $tab );
		if ( ! $parent_slug ) {
			$parent_slug = bcommerce_get_tab_slug( 'shop', false );
		}

		bp_core_new_subnav_item(
			array(
				'name'            => $this->get_label( $tab, __( 'Subscriptions', 'buddycommerce' ) ),
				'slug'            => $tab_slug,
				'parent_url'      => trailingslashit( $args['user_domain'] . $parent_slug ),
				'parent_slug'     => $parent_slug,
				'screen_function' => bcommerce_get_view_callback( $tab ),
				'position'        => bcommerce_get_user_nav_item_position_setting( $tab ),
				'user_has_access' => $args['access'],
			)
		);
	}

	/**
	 * Add Membership area tab for user.
	 *
	 * @param int   $user_id user id.
	 * @param array $args args.
	 *
	 * plugin: WooCommerce Memberships.
	 */
	private function add_membership_tab( $user_id, $args ) {

		$tab = 'members_area';
		if ( ! bcommerce_is_user_nav_item_enabled( $tab ) ) {
			return;
		}

		$tab_slug       = bcommerce_get_tab_slug( $tab, true );

		if ( bcommerce_is_top_level_user_nav_item( $tab ) ) {
			bp_core_new_nav_item(
				array(
					'name'                    => $this->get_label( $tab, __( 'My Membership', 'buddycommerce' ) ),
					'slug'                    => $tab_slug,
					'position'                => bcommerce_get_user_nav_item_position_setting( $tab ),
					'screen_function'         => bcommerce_get_view_callback( $tab ),
					'default_subnav_slug'     => $tab,
					'show_for_displayed_user' => $args['access'],
				)
			);

			return;
		}

		$parent_slug = bcommerce_get_user_nav_item_parent_slug_setting( $tab );
		if ( ! $parent_slug ) {
			$parent_slug = bcommerce_get_tab_slug( 'shop', false );
		}

		bp_core_new_subnav_item(
			array(
				'name'            => $this->get_label( $tab, __( 'Subscriptions', 'buddycommerce' ) ),
				'slug'            => $tab_slug,
				'parent_url'      => trailingslashit( $args['user_domain'] . $parent_slug ),
				'parent_slug'     => $parent_slug,
				'screen_function' => bcommerce_get_view_callback( $tab ),
				'position'        => bcommerce_get_user_nav_item_position_setting( $tab ),
				'user_has_access' => $args['access'],
			)
		);
	}

	/**
	 * Get teh default sub tab for a top level tab.
	 *
	 * @param string $tab_slug tab slug.
	 *
	 * @return int|string
	 */
	private function get_subtab( $tab_slug ) {

		$wootabs = bcommerce_get_wc_tabs_details();
		unset( $wootabs['shop'] );
		$tab = '';

		$maybe_slug = '';

		foreach ( $wootabs as $wootab => $args ) {

			if ( ! bcommerce_is_user_nav_item_enabled( $wootab ) ) {
				continue;
			}

			// shop must be top level item.
			if ( bcommerce_is_top_level_user_nav_item( $tab ) ) {
				continue;
			}

			$parent_slug = bcommerce_get_user_nav_item_parent_slug_setting( $tab );
			if ( empty( $parent_slug ) ) {
				$maybe_slug = $wootab;
			} elseif ( $parent_slug === $tab_slug ) {
				$tab = $wootab;
				break;
			}
		}

		if ( ! $tab ) {
			$tab = $maybe_slug;
		}

		return $tab;
	}

	/**
	 * Get tab level.
	 *
	 * @param string $tab tab id.
	 * @param string $default default.
	 * @param int    $count count.
	 *
	 * @return string
	 */
	private function get_label( $tab, $default = '', $count = null ) {
		$label = bcommerce_get_user_nav_item_label_setting( $tab );
		if ( ! $label ) {
			$label = $default;
		}

		if ( null === $count ) {
			return $label;
		}

		$class = ( 0 === $count ) ? 'no-count' : 'count';

		return $label . sprintf(
				' <span class="%s">%s</span>',
				esc_attr( $class ),
				bp_core_number_format( $count )
			);
	}

}
