<?php
/**
 * Admin Settings Pages Helper.
 *
 * @package    BuddyCommerce
 * @subpackage Admin
 * @copyright  Copyright (c) 2018, BuddyDev.Com
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Ravi Sharma, Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyCommerce\Admin;

use \Press_Themes\PT_Settings\Page;
use Press_Themes\PT_Settings\Panel;

// Exit if file accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin_Settings_Helper
 */
class Admin_Settings_Helper {

	/**
	 * Admin Menu slug
	 *
	 * @var string
	 */
	private $menu_slug;

	/**
	 * Used to keep a reference of the Page, It will be used in rendering the view.
	 *
	 * @var \Press_Themes\PT_Settings\Page
	 */
	private $page;

	/**
	 * Boot settings
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Setup settings
	 */
	public function setup() {

		$this->menu_slug = 'buddycommerce-settings';

		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_menu' ) );

		add_filter( 'plugin_action_links_' . buddycommerce()->basename, array( $this, 'settings_links' ) );
	}

	/**
	 * Add Settings link o n plugins screen
	 *
	 * @param array $actions links to be shown in the plugin list context.
	 *
	 * @return array
	 */
	public function settings_links( $actions ) {
		$actions['view-bcommerce-settings'] = sprintf( '<a href="%1$s" title="%2$s">%2$s</a>', admin_url( 'options-general.php?page=buddycommerce-settings' ), __( 'Settings', 'buddypress-member-types-pro' ) );
		$actions['view-bcommerce-docs']     = sprintf( '<a href="%1$s" title="%2$s" target="_blank">%2$s</a>', 'https://buddydev.com/docs/buddypress-member-types-pro/getting-started-buddypress-member-types-pro/', __( 'Documentation', 'buddypress-member-types-pro' ) );

		return $actions;
	}

	/**
	 * Show/render the setting page
	 */
	public function render() {
		$this->page->render();
	}

	/**
	 * Is it the setting page?
	 *
	 * @return bool
	 */
	private function needs_loading() {

		global $pagenow;

		// We need to load on options.php otherwise settings won't be reistered.
		if ( 'options.php' === $pagenow ) {
			return true;
		}

		if ( isset( $_GET['page'] ) && $_GET['page'] === $this->menu_slug ) {
			return true;
		}

		return false;
	}

	/**
	 * Initialize the admin settings panel and fields
	 */
	public function init() {

		if ( ! $this->needs_loading() ) {
			return;
		}

		$page = new Page( 'buddycommerce_settings', __( 'BuddyCommerce', 'buddycommerce' ) );

		$doc_url = 'https://buddydev.com/docs/buddycommerce/configuring-woocommerce-as-buddypress-profile-tabs/';

		// General settings tab.
		$panel = $page->add_panel( 'account-page', _x( 'WooCommerce Account Pages', 'Admin settings panel title', 'buddycommerce' ) );
		$panel->add_section( 'account-page-help-section', __( 'Help', 'buddycommerce' ), sprintf( __( "Setup WooCommerce account pages as BuddyPress tabs. Need help with tabs? Please see the <a href='%s'>documentation</a> here." ), $doc_url ) );

		$tabs = $this->get_wc_tabs();

		$this->add_tab_details( 'shop', $tabs['shop'], $panel );
		$this->add_tab_details( 'orders', $tabs['orders'], $panel );
		$this->add_tab_details( 'track_orders', $tabs['track_orders'], $panel );
		$this->add_tab_details( 'downloads', $tabs['downloads'], $panel );
		$this->add_tab_details( 'addresses', $tabs['addresses'], $panel );
		$this->add_tab_details( 'payment_methods', $tabs['payment_methods'], $panel );

		$panel_special = $page->add_panel( 'special-pages', _x( 'WooCommerce Special Pages ', 'Admin settings panel title', 'buddycommerce' ) );
		$panel_special->add_section( 'special-page-help-section', __( 'Help', 'buddycommerce' ), sprintf( __( "Setup WooCommerec special pages as BuddyPress profile tabs. Need help with tabs? Please see the <a href='%s'>documentation</a> here." ), $doc_url ) );
		$this->add_tab_details( 'cart', $tabs['cart'], $panel_special );
		$this->add_tab_details( 'checkout', $tabs['checkout'], $panel_special );


		$this->add_misc_panel( $page );

		if ( class_exists( 'WC_Subscriptions' ) ) {
			$this->add_tab_details( 'subscriptions', isset( $tabs['subscriptions'] ) ? $tabs['subscriptions'] : array(), $panel_special );
		}

		if ( class_exists( 'WC_Memberships_Loader' ) ) {
			$this->add_tab_details( 'members_area', isset( $tabs['members_area'] ) ? $tabs['members_area'] : array(), $panel_special );
		}

		// Save page for future reference.
		$this->page = $page;

		do_action( 'buddycommerce_settings', $page );

		// allow enabling options.
		$page->init();
	}

	/**
	 * Add miscellaneous panel
	 *
	 * @param Page $page page object.
	 */
	public function add_misc_panel( $page ) {
		$doc_url = 'https://buddydev.com/docs/buddycommerce/configuring-woocommerce-as-buddypress-profile-tabs/';
		$panel   = $page->add_panel( 'misc', _x( "Miscellaneous", 'Admin settings panel title', 'buddycommerce' ) );
		$panel->add_section( 'misc-page-help-section', __( 'Help', 'buddycommerce' ), sprintf( __( "Setup admin bar items priority. Need help? Please see the <a href='%s'>documentation</a> here." ), $doc_url ) );

		$section = $panel->add_section( 'misc-adminbar-id', __( 'Adminbar action priority', 'buddycommerce' ) );

		$section->add_field(
			array(
				'name'    => 'admin_bar_priority_id',
				'label'   => __( 'Admin bar menu addition priority', 'buddycommerce' ),
				'desc'    => __( 'It controls where the top level menu items appear in admin bar', 'buddycommerce' ),
				'type'    => 'text',
				'default' => 99,
			)
		);
	}

	/**
	 * Add a section with tab details.
	 *
	 * @param string $tab tab id.
	 * @param array  $args args.
	 * @param Panel  $panel panel object.
	 */
	private function add_tab_details( $tab, $args, $panel ) {
		$args    = wp_parse_args(
			$args,
			array(
				'label'        => '',
				'desc'         => '',
				'slug'         => '',
				'enabled'      => 0,
				'position'     => 85,
				'is_top_level' => 0,
			)
		);
		$section = $panel->add_section( $tab, $args['label'], $args['desc'] );

		$section->add_fields( $this->get_tab_fields( $tab, $args ) );
	}

	/**
	 * Add Menu
	 */
	public function add_menu() {

		add_options_page(
			_x( 'BuddyCommerce', 'Admin settings page title', 'buddycommerce' ),
			_x( 'BuddyCommerce', 'Admin settings menu label', 'buddycommerce' ),
			'manage_options',
			$this->menu_slug,
			array( $this, 'render' )
		);
	}

	/**
	 * Get WooCommerce tabs
	 *
	 * @return array
	 */
	private function get_wc_tabs() {
		return bcommerce_get_wc_tabs_details();
	}

	/**
	 * Get tab settings.
	 *
	 * @param string $tab tab slug.
	 * @param array  $args args.
	 *
	 * @return array
	 */
	private function get_tab_fields( $tab, $args ) {
		$fields = array(
			array(
				'name'    => 'is_user_' . $tab . '_enabled',
				'label'   => __( 'Is enabled', 'buddycommerce' ),
				'type'    => 'checkbox',
				'default' => $args['enabled'],
				'desc'    => __( 'Add this as a tab/sub tab on profile.', 'buddycommerce' ),
			),
			array(
				'name'    => 'user_nav_' . $tab . '_label',
				'label'   => __( 'Label', 'buddycommerce' ),
				'type'    => 'text',
				'default' => $args['label'],
				'desc'    => __( 'BuddyPress User profile tab label', 'buddycommerce' ),
			),
			array(
				'name'    => 'user_nav_' . $tab . '_slug',
				'label'   => __( 'Slug', 'buddycommerce' ),
				'type'    => 'text',
				'default' => $args['slug'],
				'desc'    => __( 'BuddyPress Profile tab slug.', 'buddycommerce' ) . ' ' . ( isset( $args['endpoint'] ) && $args['endpoint'] ? __( 'For WooCommerce endpoints, if slug is not specified, It takes the default slug from WooCommerce Advance settings', 'buddycommerce' ) : '' ),
			),
			array(
				'name'    => 'user_nav_' . $tab . '_position',
				'label'   => __( 'Position', 'buddycommerce' ),
				'type'    => 'text',
				'default' => $args['position'],
				'desc'    => __( 'Lower number means tab will be added early.', 'buddycommerce' ),
			),
			array(
				'name'    => 'is_user_' . $tab . '_top_level_item',
				'label'   => __( 'Add as top level nav item?', 'buddycommerce' ),
				'type'    => 'checkbox',
				'default' => $args['is_top_level'],
				'desc'    => __( 'If checked, tab will appear as top level user nav item.', 'buddycommerce' ),
			),


		);
		if ( ! isset( $args['toplevel_only'] ) ) {
			$fields[] = array(
				'name'    => 'user_nav_' . $tab . '_parent_slug',
				'label'   => __( 'Parent Tab', 'buddycommerce' ),
				'type'    => 'text',
				'default' => '',
				'desc'    => __( 'Only used if this tab is not added as top level. If specified, a sub nav will be added to the parent tab. If not specified, will be added as sub tab of "shop".', 'buddycommerce' ),
			);
			$fields[] = array(
				'name'    => 'user_nav_' . $tab . '_parent_admin_bar_id',
				'label'   => __( 'Parent Tab Admin bar id', 'buddycommerce' ),
				'type'    => 'text',
				'default' => '',
				'desc'    => __( 'If this tab is a child tab of a custom tab, please specify parent tab\'s admin bar id. see docs for help.', 'buddycommerce' ),
			);
		} else {
			$fields[] = array(
				'name'    => 'user_nav_' . $tab . '_default_slug',
				'label'   => __( 'Default subnav', 'buddycommerce' ),
				'type'    => 'text',
				'default' => '',
				'desc'    => __( 'Default subnav slug.', 'buddycommerce' ),
			);
		}


		if ( isset( $args['redirect'] ) ) {

			$fields[] = array(
				'name'    => 'user_nav_' . $tab . '_redirect',
				'label'   => __( 'Redirect?', 'buddycommerce' ),
				'type'    => 'checkbox',
				'default' => '',
				'desc'    => $args['redirect_description'],
			);
		}

		return $fields;
	}
}
