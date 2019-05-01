<?php
/**
 * This page contains the class that represents a setting section.
 *
 * @package \Press_Themes\PT_Settings
 */

namespace Press_Themes\PT_Settings;

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Section
 *
 * Represents the sections on a settings page.
 */
class Section {

	/**
	 * Unique section Id for the page
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Section title
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Section description
	 *
	 * @var string
	 */
	private $desc = '';

	/**
	 * All the fields in this section
	 *
	 * @var array  of fields
	 */
	private $fields = array();

	/**
	 * Section constructor.
	 *
	 * @param string $id section id.
	 * @param string $title section title.
	 * @param string $desc section description.
	 */
	public function __construct( $id, $title, $desc = '' ) {

		$this->id    = $id;
		$this->title = $title;
		$this->desc  = $desc;

	}

	/**
	 * Adds a field to this section
	 *
	 * We can use it to chain and add multiple fields in a go
	 *
	 * @param array $field the setting field to add to the section.
	 *
	 * @return \Press_Themes\PT_Settings\Section
	 */
	public function add_field( $field ) {

		// Check if a field class with name Field_$type exists, use it.
		$type = 'text';

		if ( isset( $field['type'] ) ) {
			$type = $field['type'];
		} //text/radio etc

		$field_class = '\Press_Themes\PT_Settings\Field';

		// Guess a field type class.
		$type_class = $this->get_field_type_class( $type );

		// Class exists should try to autoload.
		if ( class_exists( $type_class ) && is_subclass_of( $type_class, $field_class ) ) {
			$field_class = $type_class;
		}

		$field_object = new $field_class( $field );

		$id = $field_object->get_id();

		// Let us store the field.
		$this->fields[ $id ] = $field_object;

		return $this;
	}

	/**
	 * Add multiple settings Field at once
	 *
	 * @param \Press_Themes\PT_Settings\Field[] $fields array of settings fields.
	 *
	 * @return $this
	 */
	public function add_fields( $fields ) {

		foreach ( $fields as $field ) {
			$this->add_field( $field );
		}

		return $this;

	}

	/**
	 * Set the fields for this section with given array of Fields object
	 *
	 * @param \Press_Themes\PT_Settings\Field[] $fields array of fields.
	 *
	 * @return $this
	 */
	public function set_fields( $fields ) {
		// If set fields is called, first reset fields.
		$this->reset_fields();

		$this->add_fields( $fields );

		return $this;

	}

	/**
	 * Resets fields in this section
	 */
	public function reset_fields() {

		unset( $this->fields );

		$this->fields = array();

		return $this;

	}

	/**
	 * Setters
	 */

	/**
	 * Set the section id
	 *
	 * @param string $id section id.
	 *
	 * @return $this
	 */
	public function set_id( $id ) {

		$this->id = $id;

		return $this;
	}

	/**
	 * Set the section title
	 *
	 * @param string $title section title to set.
	 *
	 * @return $this
	 */
	public function set_title( $title ) {

		$this->title = $title;

		return $this;
	}

	/**
	 * Set the section description
	 *
	 * @param string $desc description to set.
	 *
	 * @return $this
	 */
	public function set_description( $desc ) {

		$this->desc = $desc;

		return $this;
	}

	/**
	 * Get the section id
	 *
	 * @return string Section id
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get section title
	 *
	 * @return string Section title
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Get section description
	 *
	 * @return string section description.
	 */
	public function get_disc() {
		return $this->desc;
	}

	/**
	 * Get a multidimensional array of the setting fields Objects in this section
	 *
	 * @return \Press_Themes\PT_Settings\Field[]
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * Get the field object
	 *
	 * @param string $name name of the field.
	 *
	 * @return \Press_Themes\PT_Settings\Field
	 */
	public function get_field( $name ) {
		return $this->fields[ $name ];
	}

	/**
	 * Get the fully qualified class name for givel setting field type
	 *
	 * @param string $type any valid field type e.g text, checkbox etc.
	 *
	 * @return string field class name.
	 */
	private function get_field_type_class( $type ) {
		return apply_filters( 'pt_settings_field_type_class', '\\Press_Themes\\PT_Settings\\Fields\\' . ucfirst( $type ), $type, $this );
	}
}
