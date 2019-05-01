<?php
/**
 * This page contains the class that represents a setting panel(tab).
 *
 * @package \Press_Themes\PT_Settings
 */

namespace Press_Themes\PT_Settings;

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Panel allows us to keep multiple section inside the tabbed page.
 * Each tab is a panel
 */
class Panel {
	/**
	 * Unique panel id
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Panel title
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Panel description
	 *
	 * @var string
	 */
	private $desc = '';

	/**
	 * Sections contained in this panel
	 *
	 * @var \Press_Themes\PT_Settings\Section[]
	 */
	private $sections = array();

	/**
	 * Panel constructor.
	 *
	 * @param string $id id of the panel, must be unique.
	 * @param string $title title of the panel(tab).
	 * @param string $desc description for the panel.
	 */
	public function __construct( $id, $title, $desc = '' ) {

		$this->id    = $id;
		$this->title = $title;
		$this->desc  = $desc;

	}

	/**
	 * Add new Setting Section
	 *
	 * @param string $id section id.
	 * @param string $title section title.
	 * @param string $desc section description.
	 *
	 * @return Section
	 */
	public function add_section( $id, $title, $desc = '' ) {

		$section_id = $id;

		$this->sections[ $section_id ] = new Section( $id, $title, $desc );

		return $this->sections[ $section_id ];

	}

	/**
	 * Add multiple sections to this panel
	 *
	 * @param \Press_Themes\PT_Settings\Section[] $sections array of sections to add to this panel.
	 *
	 * @return $this
	 */
	public function add_sections( $sections ) {

		foreach ( $sections as $id => $title ) {
			$this->add_section( $id, $title );
		}

		return $this;

	}

	/**
	 * Get a section by id
	 *
	 * @param string $id section id.
	 *
	 * @return null|Section
	 */
	public function get_section( $id ) {
		return isset( $this->sections[ $id ] ) ? $this->sections[ $id ] : null;
	}

	/**
	 * Get all sections in this panel
	 *
	 * @return Section[]
	 */
	public function get_sections() {
		return $this->sections;
	}

	/**
	 * Setters
	 */

	/**
	 * Set panel id
	 *
	 * @param string $id the panel id.
	 *
	 * @return $this
	 */
	public function set_id( $id ) {

		$this->id = $id;

		return $this;

	}

	/**
	 * Set title
	 *
	 * @param string $title panel title.
	 *
	 * @return $this
	 */
	public function set_title( $title ) {

		$this->title = $title;

		return $this;

	}

	/**
	 * Set description
	 *
	 * @param string $desc panel description.
	 *
	 * @return $this
	 */
	public function set_description( $desc ) {

		$this->desc = $desc;

		return $this;

	}

	/**
	 * Get the panel id
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the panel title
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Get the panel description
	 *
	 * @return string
	 */
	public function get_disc() {
		return $this->desc;
	}

	/**
	 * Check if the panel is empty?
	 *
	 * A panel is considered empty if it is registered but have no sections added
	 *
	 * @return boolean
	 */
	public function is_empty() {

		if ( empty( $this->sections ) ) {
			return true;
		}

		return false;

	}

}
