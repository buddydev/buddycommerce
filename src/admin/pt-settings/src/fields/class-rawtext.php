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
class Rawtext extends Field {
	/**
	 * Field_Rawtext constructor.
	 *
	 * @param array $field settings.
	 */
	public function __construct( $field ) {
		parent::__construct( $field );
	}

	/**
	 * Displays a raw textarea for a settings field
	 *
	 * @param array $args settings field args.
	 */
	public function render( $args ) {

		$value = $args['value'];
		$size  = $this->get_size();

		printf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s" name="%2$s">', esc_attr( $size ), esc_attr( $args['option_key'] ) );
		echo esc_textarea( $value );
		echo '</textarea>';
		printf( '<br /><span class="pt-settings-field-description"> %s </span>', wp_kses_data( $this->get_desc() ) );
	}
}
