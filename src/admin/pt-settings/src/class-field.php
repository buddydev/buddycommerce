<?php
/**
 * Base Field class
 *
 * @package Press_Themes\PT_Settings
 */

namespace Press_Themes\PT_Settings;

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstracts a Setting Field
 *
 * This class abstracts the Settings field
 *
 * For your custom fields, you may extend this class and its render(), sanitize(), get_value() method
 */
class Field {

	/**
	 * Unique field id.
	 *
	 * @var string unique field id
	 */
	private $id;

	/**
	 * Unique field name, almost same as id
	 *
	 * @var string field name
	 */
	private $name;

	/**
	 * Settings field label
	 *
	 * @var string Label for the settings field
	 */
	private $label;

	/**
	 * Settings field description
	 *
	 * @var string description of the setting field
	 */
	private $desc;

	/**
	 * Field type
	 *
	 * @var string Field Type
	 *
	 * @since 1.0.0
	 */
	private $type = 'text';

	/**
	 * Associative array of key=>val pair for multiselect,select checkbox etc
	 *
	 * @var mixed
	 */
	private $options; // An array of key =>label for radio/multichebox etc.

	/**
	 * Text field length
	 *
	 * @var string used for generating classes of the input element
	 */
	private $size; // to apply class and size in case of wysiwyg.

	/**
	 * Default value
	 *
	 * @var mixed the default value of the current field
	 */
	private $default = '';

	/**
	 * Anything extra
	 *
	 * @var mixed any extra data passed to the field implementation
	 */
	private $extra = '';

	/**
	 * Sanitize callback
	 *
	 * @var callable callable function/method used to sanitize the field data
	 */
	private $sanitize_cb;

	/**
	 * Field constructor.
	 *
	 * @param array $field Associative array of options.
	 */
	public function __construct( $field ) {

		$defaults = array(
			'id'          => '',
			'name'        => '',
			'label'       => '',
			'desc'        => '',
			'type'        => 'text',
			// default type is text. allowd values are text|textarea|checkbox|radio|password|image|file.
			'options'     => '',
			'size'        => 'regular',
			'sanitize_cb' => '',
			'default'     => '',
			'extra'       => '',
		);

		$args = wp_parse_args( $field, $defaults );

		$this->id = $args['id'];

		$this->name = $args['name'];

		if ( ! $this->id ) {
			$this->id = $this->name;
		}

		$this->label = $args['label'];
		$this->desc  = $args['desc'];

		$this->type = $args['type'];

		$this->options = $args['options'];

		$this->sanitize_cb = $args['sanitize_cb'];

		$this->size = $args['size'];

		$this->default = $args['default'];
		$this->extra   = $args['extra'];

	}

	/**
	 * Get the value of a property
	 *
	 * @param string $property any valid property name.
	 *
	 * @return mixed|boolean  the value of the property or false
	 */
	public function get( $property ) {

		if ( isset( $this->{$property} ) ) {
			return $this->{$property};
		}

		return false;

	}

	/**
	 * Get the id of this field( as supplied while registering teh field, if not given, is same as fiel name )
	 *
	 * @return string field id
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get Field name
	 *
	 * @return string field name (as supplied while registering teh field )
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get the registered label fro this field
	 *
	 * @return string
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Get the description text for this field
	 *
	 * @return string
	 */
	public function get_desc() {
		return $this->desc;
	}

	/**
	 * Get current field type
	 *
	 * @return string field type( e.g text|checkbox etc)
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get given options for this field
	 *
	 * @return mixed
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Get teh size
	 *
	 * @return string
	 */
	public function get_size() {
		return $this->size;
	}

	/**
	 * Get default value for this field
	 *
	 * @return mixed
	 */
	public function get_default() {
		return $this->default;
	}

	/**
	 * Just a placeholder,. allows child classes to process value
	 *
	 * @param mixed $value the value.
	 *
	 * @return mixed
	 */
	public function get_value( $value ) {
		return $value;
	}

	/**
	 * Get the sanitization callback for this field
	 *
	 * @return callable|false
	 */
	public function get_sanitize_cb() {

		if ( ! empty( $this->sanitize_cb ) && is_callable( $this->sanitize_cb ) ) {
			$cb = $this->sanitize_cb;
		} else {
			$cb = array( $this, 'sanitize' );
		}

		return $cb;

	}

	/**
	 * Sanitize options callback for Settings API
	 *
	 * Only used if the option name is global
	 * If the option name stored in options table is not unique and used as part of optgroup, this method is not callde
	 *
	 * @param mixed $value the value to sanitize.
	 *
	 * @return mixed sanitized value
	 */
	public function sanitize( $value ) {

		// If callback is set, call it.
		if ( in_array( $this->type, array( 'textarea', 'rawtext', 'wysiwyg' ) ) ) {
			$value = sanitize_textarea_field( $value );
		} elseif ( ! is_array( $value ) ) {
			$value = sanitize_text_field( $value );
		}

		return $value;

	}

	/**
	 * Display the form elements
	 *
	 * Override it in the child classes to show the output
	 *
	 * @param mixed $args field args.
	 */
	public function render( $args ) {

		$method_name = 'callback_' . $this->get_type();

		if ( method_exists( $this, $method_name ) ) {
			call_user_func( array( $this, $method_name ), $args );
		}

	}

	/**
	 * Helper methods to generate the form elements for settings fields
	 *
	 * These are fallback, if you are adding a new field type, please override render method in your class instead of using this
	 *
	 * The inspiration for these display methods were the Settings api class by Tareq<>
	 */

	/**
	 * Displays a text field for a settings field
	 *
	 * @param array $args settings field args.
	 */
	public function callback_text( $args ) {

		$value = esc_attr( $args['value'] );
		$size  = $this->get_size();

		printf( '<input type="text" class="%1$s-text pt-settings-field-type-text" id="%2$s" name="%2$s" value="%3$s"/>', esc_attr( $size ), esc_attr( $args['option_key'] ), esc_attr( $value ) );
		printf( '<span class="pt-settings-field-description"> %s </span>', esc_html( $this->get_desc() ) );

	}

	/**
	 * Helper methods to generate the form elements for settings fields
	 *
	 * These are fallback, if you are adding a new field type, please override render method in your class instead of using this
	 *
	 * The inspiration for these display methods were the Settings api class by Tareq<>
	 */

	/**
	 * Displays a text field for a settings field
	 *
	 * @param array $args settings field args.
	 */
	public function callback_number( $args ) {

		$value = esc_attr( $args['value'] );
		$size  = $this->get_size();

		printf( '<input type="text" class="%1$s-text pt-settings-field-type-number" id="%2$s" name="%2$s" value="%3$s"/>', esc_attr( $size ), esc_attr( $args['option_key'] ), esc_attr( $value ) );
		printf( '<span class="pt-settings-field-description"> %s </span>', esc_html( $this->get_desc() ) );

	}

	/**
	 * Displays a checkbox for a settings field
	 *
	 * @param array $args settings field args.
	 */
	public function callback_checkbox( $args ) {

		$value = esc_attr( $args['value'] );

		$id = $this->get_id();


		printf( '<input type="checkbox" class="checkbox pt-settings-field-type-checkbox" id="%1$s" name="%1$s" value="1" %3$s />', esc_attr( $args['option_key'] ), esc_attr( $value ), checked( $value, 1, false ) );
		printf( '<label for="%1$s"> %2$s</label>', esc_attr( $args['option_key'] ), esc_html( $this->get_desc() ) );

	}

	/**
	 * Displays a multi checkbox a settings field
	 *
	 * @param array $args settings field args.
	 */
	public function callback_multicheck( $args ) {

		$id      = $this->get_id();
		$value   = $args['value'];
		$options = $this->get_options();

		foreach ( $options as $key => $label ) {
			$checked = isset( $value[ $key ] ) ? $value[ $key ] : 0;
			printf( '<input type="checkbox" class="checkbox pt-settings-field-type-checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="%2$s" %3$s />', esc_attr( $args['option_key'] ), esc_attr( $key ), checked( $checked, $key, false ) );
			printf( '<label for="%1$s[%3$s]"> %2$s </label><br />', esc_attr( $args['option_key'] ), esc_html( $label ), esc_attr( $key ) );
		}

		printf( '<span class="pt-settings-field-description"> %s </span>', wp_kses_data( $this->get_desc() ) );

	}

	/**
	 * Displays a multicheckbox a settings field
	 *
	 * @param array $args settings field args.
	 */
	public function callback_radio( $args ) {

		$id = $this->get_id();

		$value   = $args['value'];
		$options = $this->get_options();

		foreach ( $options as $key => $label ) {
			printf( '<input type="radio" class="radio pt-settings-field-type-radio" id="%1$s[%3$s]" name="%1$s" value="%3$s"%4$s />', esc_attr( $args['option_key'] ), esc_attr( $id ), esc_attr( $key ), checked( $value, $key, false ) );
			printf( '<label for="%1$s[%4$s]"> %3$s</label><br>', esc_attr( $args['option_key'] ), esc_attr( $id ), wp_kses_data( $label ), esc_attr( $key ) );
		}

		printf( '<span class="pt-settings-field-description"> %s</label>', wp_kses_data( $this->get_desc() ) );

	}

	/**
	 * Displays a selectbox for a settings field
	 *
	 * @param array $args settings field args.
	 */
	public function callback_select( $args ) {

		$id    = $this->get_id();
		$value = esc_attr( $args['value'] );

		$options = $this->get_options();
		$size    = $this->get_size();

		printf( '<select class="%1$s pt-settings-field-type-select" name="%2$s" id="%2$s">', esc_attr( $size ), esc_attr( $args['option_key'] ), esc_attr( $id ) );

		foreach ( $options as $key => $label ) {
			printf( '<option value="%s"%s>%s</option>', esc_attr( $key ), selected( $value, $key, false ), wp_kses_data( $label ) );
		}

		printf( '</select>' );
		printf( '<span class="pt-settings-field-description"> %s </label>', wp_kses_data( $this->get_desc() ) );

	}

	/**
	 * Displays a textarea for a settings field
	 *
	 * @param array $args settings field args.
	 */
	public function callback_textarea( $args ) {

		$value = $args['value'];
		$size  = $this->get_size();

		printf( '<textarea rows="5" cols="55" class="%1$s-text pt-settings-field-type-textarea" id="%2$s" name="%2$s">%3$s</textarea>', esc_attr( $size ), esc_attr( $args['option_key'] ), esc_attr( $value ) );
		printf( '<br /><span class="pt-settings-field-description"> %s </span>', wp_kses_data( $this->get_desc() ) );

	}

	/**
	 * Displays a textarea for a settings field
	 *
	 * @param array $args settings field args.
	 */
	public function callback_html( $args ) {
		echo wp_kses_post( $this->get_desc() );
	}


	/**
	 * Displays a password field for a settings field
	 *
	 * @param array $args settings field args.
	 */
	public function callback_password( $args ) {

		$value = esc_attr( $args['value'] );
		$size  = $this->get_size();
		printf( '<input type="password" class="%1$s-text pt-settings-field-type-password" id="%2$s" name="%2$s" value="%3$s"/>', esc_attr( $size ), esc_attr( $args['option_key'] ), esc_attr( $value ) );

		printf( '<span class="pt-settings-field-description"> %s </span>', wp_kses_data( $this->get_desc() ) );

	}

}
