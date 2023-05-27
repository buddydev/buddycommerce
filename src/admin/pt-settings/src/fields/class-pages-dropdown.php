<?php
/**
 * Page Dropdown field.
 *
 * @package Press_Themes\PT_Settings
 */

namespace Press_Themes\PT_Settings\Fields;

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Press_Themes\PT_Settings\Field;

/**
 * Class Pages_Dropdown
 */
class Pages_Dropdown extends Field {

	/**
	 * Pages_Dropdown constructor.
	 *
	 * @var string page key.
	 */
	private $key = '';

	/**
	 * Pages_Dropdown constructor.
	 *
	 * @param array $field field options.
	 */
	public function __construct( $field ) {
		parent::__construct( $field );
		$this->key = isset( $field['extra']['key'] ) ? trim( $field['extra']['key'] ) : '';
	}

	/**
	 * Displays WordPress dropdown pages for a settings field
	 *
	 * @param array $args settings field args.
	 */
	public function render( $args ) {

		wp_enqueue_script( 'pt-settings-page-create' );

		$args['name']             = $args['option_key'];
		$args['selected']         = $args['value'];
		$args['echo']             = 0;
		$args['show_option_none'] = __( 'Select Page', 'pt-settings' );

		$dropdown = wp_dropdown_pages( $args );

		if ( empty( $args['selected'] ) ) {
			$dropdown .= sprintf(
				'<a href="%1$s" class="%2$s" data-action="%3$s" data-key="%4$s" data-nonce="%5$s">%6$s</a>',
				'#',
				'button pt-settings-create-page-button',
				'pt_settings_create_page',
				$this->key,
				wp_create_nonce( 'pt-settings-create-page' ),
				__( 'Create', 'pt-settings' )
			);

			$dropdown .= '<div class="pt-settings-create-page-status"></div>';
		} else {
			// we have a page to show.
			$dropdown .= sprintf(
				'<a href="%1$s" class="%2$s">%3$s</a>',
				get_permalink( $args['selected'] ),
				'button pt-settings-view-page-button',
				_x( 'View', 'Ading Settings page panel, page view label', 'pt-settings' )
			);
		}

		echo $dropdown;
	}
}
