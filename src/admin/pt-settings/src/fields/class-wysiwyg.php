<?php
/**
 * Rawtext Field class
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
 * For example
 * Here is the text field rendering
 */
class Wysiwyg extends Field {

	/**
	 * Render the field
	 *
	 * @param mixed $args settings.
	 */
	public function render( $args ) {

		$value = wpautop( $args['value'] );
		$size  = $this->get_size();

		if ( 'regular' === $size ) {
			$size = '500px';
		}

		echo '<div style="width: ' . $size . ';">';
		$key = str_replace( array( '[', ']' ), array( '_', '_' ), $args['option_key'] );
		wp_editor( $value, $key, array(
			'teeny'         => true,
            'textarea_rows' => 10,
            'textarea_name' => $args['option_key'],
		) );

		echo '</div>';

		printf( '<br /><span class="pt-settings-field-description"> %s </span>', wp_kses_data( $this->get_desc() ) );

	}

	public function sanitize( $value ) {
	    return sanitize_textarea_field($value );
	}
}
