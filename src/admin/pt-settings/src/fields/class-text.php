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
class Text extends Field {

	/**
	 * Field_Text constructor.
	 *
	 * @param array $field settings.
	 */
	public function __construct( $field ) {
		parent::__construct( $field );
	}

	/**
	 * Render the field
	 *
	 * @param mixed $args settings.
	 */
	public function render( $args ) {
		parent::render( $args );
	}

}
