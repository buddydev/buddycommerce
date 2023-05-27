<?php
/**
 * Ajax request handling class
 *
 * @package Press_Themes\PT_Settings
 */

namespace Press_Themes\PT_Settings;

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Ajax_Handler
 */
class Ajax_Handler {

	/**
	 * Ajax_Handler constructor.
	 */
	public function __construct() {
		$this->setup();
	}

	/**
	 * Setup callback to related ajax action
	 */
	public function setup() {
		add_action( 'wp_ajax_pt_settings_create_page', array( $this, 'create_page' ) );
	}

	/**
	 * Create page
	 */
	public function create_page() {

		check_ajax_referer( 'pt-settings-create-page', '_wpnonce', true );

		$key = isset( $_POST['key'] ) ? trim( $_POST['key'] ) : '';

		if ( ! $key || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid action.', 'peoplepress' ) )
			);
		}

		$page_details = apply_filters( 'pt_settings_field_dropdown_page_details', array(), $key );

		if ( empty( $page_details ) || ! is_array( $page_details ) || empty( $page_details['title'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Page details not found.', 'peoplepress' ) ) );
		}

		$data = array(
			'post_content' => $page_details['content'],
			'post_title'   => $page_details['title'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
		);

		$page_id = wp_insert_post( $data );

		wp_send_json_success(
			array(
				'page_id'    => $page_id,
				'page_title' => $page_details['title'],
				'message'    => __( 'Page created successfully.', 'peoplepress' ),
				'link'       => sprintf(
					'<a href="%1$s" class="%2$s">%3$s</a>',
					get_permalink( $page_id ),
					'button pt-settings-view-page-button',
					_x( 'View', 'Ading Settings page panel, page view label', 'pt-settings' )
				),
			)
		);
	}
}
