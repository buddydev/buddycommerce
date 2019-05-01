<?php
/**
 * Settings Page Generator
 *
 * @package \Press_Themes\PT_Settings
 */

namespace Press_Themes\PT_Settings;
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class represents an Admin page
 *
 * It could be a newly generated page or just an existing page
 * If the page exists, It will inject the sections/fields to that page
 */
class Page {

	/**
	 * Settings page slug
	 *
	 * @var string
	 */
	private $page = '';

	/**
	 * Page title.
	 *
	 * @var string
	 */
	private $title = '';

	/**
	 * The option name to be stored in options table
	 *
	 * If using individual field name as option is not enabled, this is used to store all the options in a multidimensional array
	 *
	 * @var string
	 */
	private $option_name = '';

	/**
	 * Options group name
	 *
	 * @var string
	 */
	private $optgroup = '';

	/**
	 * Settings Panel array
	 *
	 * @var  \Press_Themes\PT_Settings\Panel[]
	 */
	private $panels = array();

	/**
	 * Callback stack.
	 *
	 * @var array
	 */
	private $cb_stack = array(); // field_name=>callback stack.

	/**
	 * Use unique option name for each settings
	 *
	 * @var boolean if enabled, each field will be individually stored in the options table
	 */
	private $use_unique_option = false;

	/**
	 * Are we using global options on Multisite
	 *
	 * @var bool
	 */
	private $is_network_mode = false;

	/**
	 * Are we usign it with BuddyPress options mod( using bp_get_option)
	 *
	 * @var bool
	 */
	private $is_bp_mode = false;

	/**
	 *  Settings Page constructor
	 *
	 * @param string $page unique page slug.
     * @param string $title page title.
	 */
	public function __construct( $page, $title = '' ) {

		$this->page  = $page;
		$this->title = $title;

		$this->set_option_name( $page );
		$this->set_optgroup( $page ); // Set optgroup same as page by default.
	}

	/**
	 * Registers settings sections and fields
	 *
	 * This should be called at admin_init action
	 * If you are using existing page, make sure to attach your admin_init hook to low priority
	 */
	public function init() {

		$global_option_name = $this->get_option_name();

		// Check if the option exists, if not, let us add it.
		if ( ! $this->using_unique_option() ) {

			if ( false === get_option( $global_option_name ) ) {
				add_option( $global_option_name );
			}
		}

		// Register settings sections for every section.
		foreach ( $this->panels as $panel ) {

			$sections = $panel->get_sections();

			foreach ( $sections as $section ) {

				// For individual section.
				if ( $section->get_disc() ) {
					$desc     = '<div class="pt-inside-description">' . wp_kses_data( $section->get_disc() ) . '</div>';
					$callback = function () use ( $desc ) {
						echo $desc;
					};
				} else {
					$callback = '__return_empty_string';
				}

				$section_id = $panel->get_id() . '-' . $section->get_id();

				add_settings_section( $section_id, $section->get_title(), $callback, $this->get_page() );

				// Register settings fields.
				foreach ( $section->get_fields() as $field ) {

					$option_name = $global_option_name . '[' . $field->get_name() . ']';
					// When using local.
					if ( $this->using_unique_option() ) {

						if ( false === get_option( $field->get_name() ) ) {
							add_option( $field->get_name() );
						}
						// Override option name.
						$option_name = $field->get_name();
					}

					$args = array(
						'section'    => $section_id,
						'std'        => $field->get_default(),
						'option_key' => $option_name,
						'value'      => $this->get_option( $field ),
						'base_name'  => $global_option_name,
					);

					$this->cb_stack[ $field->get_id() ] = $field->get_sanitize_cb();

					add_settings_field( $option_name, $field->get_label(), array(
						$field,
						'render',
					), $this->get_page(), $section_id, $args );

					// When using local option.
					if ( $this->using_unique_option() ) {
						register_setting( $this->get_optgroup(), $field->get_name(), array( $field, 'sanitize' ) );
					}
				}
			}

			// When using only one option to store all values.
			if ( ! $this->using_unique_option() ) {
				register_setting( $this->get_optgroup(), $this->get_option_name(), array( $this, 'sanitize_options' ) );
			}
		}
	}

	/**
	 * Add settings panel to the page
	 *
	 * @param string $id panel id.
	 * @param string $title panel title.
	 * @param string $desc panel description.
	 *
	 * @return Panel
	 */
	public function add_panel( $id, $title, $desc = '' ) {

		$panel_id = $id;

		$this->panels[ $panel_id ] = new Panel( $id, $title, $desc );

		return $this->panels[ $panel_id ];
	}

	/**
	 * Add multiple panels
	 *
	 * @param Panel[] $panels array of panels to add to this settinmg page.
	 *
	 * @return $this
	 */
	public function add_panels( $panels ) {

		foreach ( $panels as $id => $title ) {
			$this->add_panel( $id, $title );
		}

		return $this;
	}

	/**
	 * Get the panel by ID
	 *
	 * @param string $id panel id to be retrieved.
	 *
	 * @return Panel|null
	 */
	public function get_panel( $id ) {
		return isset( $this->panels[ $id ] ) ? $this->panels[ $id ] : null;
	}

	/**
	 * Get the Page object
	 *
	 * Mainly used for generating the settings form
	 *
	 * @return string page slug
	 */
	public function get_page() {
		return $this->page;
	}

	/**
	 * Get the value of a settings field
	 *
	 * @param Field $field the field object.
	 *
	 * @return string
	 */
	public function get_option( $field ) {

		$option  = $field->get_name();
		$default = $field->get_default();

		if ( ! isset( $default ) ) {
			$default = '';
		}

		$value = null;

		$function_name = 'get_option'; // Use get_option function.

		// if the page is in network mode, use get_site_option.
		if ( $this->is_network_mode() ) {
			$function_name = 'get_site_option';
		} elseif ( $this->is_bp_mode() && function_exists( 'bp_get_option' ) ) {
			$function_name = 'bp_get_option';
		}

		// Are we using single option to store all settings? If yes, let us do it.
		if ( ! $this->using_unique_option() ) {

			$options = $function_name( $this->get_option_name() );
			// if option is not set, it is most probably the first run.
			if ( ! $options ) {
				$value = $default;
			} elseif ( isset( $options[ $option ] ) ) {
				$value = $options[ $option ];
			} elseif ( 'checkbox' != $field->get_type() && 'multicheck' != $field->get_type() ) {
				$value = $default;
			}
		} else {
			// For individual option.
			$value = $function_name( $option, $default );
		}

		// Let the field process the value.
		$value = $field->get_value( $value );

		return $value;
	}

	/**
	 * If use unique option is enabled, each setting field is stored in the options table as individual item, so an item can be retrieved as get_option('setting_field_name');
	 * otherwise, all the setting field option is stored in a single option as array and that name of option is page_name or option_name depending on which one is set
	 *
	 * @return $this
	 */
	public function use_unique_option() {

		$this->use_unique_option = true;

		return $this;
	}

	/**
	 * Should we store all settings using a single option key?
	 *
	 * @return $this
	 */
	public function use_single_option() {

		$this->use_unique_option = false;

		if ( ! isset( $this->option_name ) ) {

			$this->set_option_name( $this->page );

		}

		return $this;
	}

	/**
	 * Is it using unique option
	 *
	 * @return bool are we using unique options to store each field
	 */
	public function using_unique_option() {
		return $this->use_unique_option;
	}

	/**
	 * Set the network mode (for multisite)
	 *
	 * @return $this
	 */
	public function set_network_mode() {

		$this->is_network_mode = true;

		return $this;
	}

	/**
	 * Check if it is network mode?
	 *
	 * @return bool
	 */
	public function is_network_mode() {
		return $this->is_network_mode;
	}

	/**
	 * Set BuddyOPress mod on.
	 *
	 * @return $this
	 */
	public function set_bp_mode() {

		$this->is_bp_mode = true;

		return $this;
	}

	/**
	 * Check if it is BuddyPress Mode
	 *
	 * @return bool
	 */
	public function is_bp_mode() {
		return $this->is_bp_mode;
	}

	/**
	 * Reset all modes
	 *
	 * @return $this
	 */
	public function reset_mode() {

		$this->is_network_mode = false;
		$this->is_bp_mode      = false;

		return $this;
	}

	/**
	 * Set an option name if you want. It is only used if using_unique_option is disabled
	 *
	 * @param string $option_name name of the option.
	 *
	 * @return $this
	 */
	public function set_option_name( $option_name ) {

		$this->option_name = $option_name;

		return $this;
	}

	/**
	 * Get the option name
	 *
	 * @return string
	 */
	public function get_option_name() {
		return $this->option_name;
	}

	/**
	 * Set option group
	 *
	 * @param string $optgroup Name of the option group.
	 *
	 * @return $this
	 */
	public function set_optgroup( $optgroup ) {
		$this->optgroup = $optgroup;

		return $this;
	}

	/**
	 * Get the optgroup name
	 *
	 * @return string
	 */
	public function get_optgroup() {
		return $this->optgroup;
	}

	/**
	 * Show heading.
	 */
	private function show_title() {

		if ( $this->title ) {
			$title = $this->title;// wp_kses_data( $this->title );
			echo "<h1>{$title}</h1>";
		}
	}

	/**
	 * Show navigations as tab
	 *
	 * Shows all the settings section labels as tab
	 */
	public function show_navigation() {

		$html = '<h2 class="nav-tab-wrapper pt-settings-tab-wrapper">';

		foreach ( $this->panels as $panel ) {
			// Do not show nav if it is empty.
			if ( $panel->is_empty() ) {
				continue;
			}

			$html .= sprintf( '<a href="#%1$s" class="nav-tab pt-settings-nav-tab" id="%1$s-tab">%2$s</a>', $panel->get_id(), $panel->get_title() );
		}

		$html .= '</h2>';

		echo $html;
	}

	/**
	 * Show the settings forms
	 *
	 * This function displays every sections in a different form
	 */
	public function show_form() {
		?>
        <div class="metabox-holder pt-settings-metabox-holder">
            <div class="postbox options-postbox pt-options-box">
                <form method="post" action="<?php echo admin_url( 'options.php' );?>">
					<?php settings_fields( $this->get_optgroup() ); ?>

					<?php foreach ( $this->panels as $panel ) : ?>
						<?php
						if ( $panel->is_empty() ) {
							continue;
						}
						?>
                        <div id="<?php echo esc_attr( $panel->get_id() ); ?>" class="pt-settings-panel-tab">

							<?php $sections = $panel->get_sections(); ?>

							<?php foreach ( $sections as $section ) : ?>
								<?php $section_id = $panel->get_id() . '-' . $section->get_id(); ?>
                                <div id="<?php echo esc_attr( $section_id ); ?>"
                                     class="pt-settings-section-block <?php echo esc_attr( $section_id ); ?>">
									<?php do_action( 'pt_settings_form_top_' . $section_id, $section ); ?>

									<?php $this->do_settings_sections( $this->get_page(), $section_id ); ?>
									<?php do_action( 'pt_settings_form_bottom_' . $section_id, $section ); ?>
                                </div>
							<?php endforeach; ?>

                            <div class="pt-settings-submit">
								<?php submit_button(); ?>
                            </div>
                        </div>
					<?php endforeach; ?>

                </form>
				<?php $this->styles(); ?>
            </div>
        </div>
		<?php
		$this->script();
	}

	public function styles() {
		?>
        <style type="text/css">
            .pt-settings-page-wrap h1 + .pt-settings-tab-wrapper {
                margin-top:15px;
            }
            .pt-options-box {
                padding: 10px;
            }

            .pt-settings-section-block {
                padding: 15px 12px;
                background: #fff;
            }

            .pt-settings-section-block:nth-child(odd) {
                background: #f8f8f8;
            }

            .pt-settings-section-block h3 {
                padding: 0 0;
                font-size: 20px;
            }
            .pt-settings-section-block .pt-settings-field-description {
                font-style:italic;
                display: block;
            }
            .pt-settings-field-type-number {
                width: 3em;
            }
            .pt-settings-submit {
                padding-left: 10px;
            }
        </style>
		<?php
	}

	/**
	 * Render the page
	 */
	public function render() {
		$slug = sanitize_html_class( $this->option_name );
		echo "<div class='wrap pt-settings-page-wrap {$slug}'>";
		$this->show_title();
		$this->show_navigation();
		$this->show_form();

		echo '</div>';
	}

	/**
	 * Generate settings sections for the page
	 *
	 * @param string $page the page slug where the view will be generated.
	 * @param string $section_id the section id for which settings have to be displayed/generated.
	 */
	public function do_settings_sections( $page, $section_id ) {

		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections ) || ! isset( $wp_settings_sections[ $page ] ) ) {

			return;
		}

		$section = $wp_settings_sections[ $page ][ $section_id ];

		if ( $section['title'] ) {
			echo "<h3>{$section['title']}</h3>\n";
		}

		if ( $section['callback'] && is_callable( $section['callback'] ) ) {
			call_user_func( $section['callback'], $section );
		}

		if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
			return;
		}

		echo '<table class="form-table">';

		do_settings_fields( $page, $section['id'] );

		echo '</table>';

	}

	/**
	 * Sanitize options callback for Settings API
	 *
	 * @param mixed $options options o sanitize.
	 *
	 * @return mixed sanitized option
	 */
	public function sanitize_options( $options ) {

		foreach ( $options as $option_slug => $option_value ) {

			$sanitize_callback = isset( $this->cb_stack[ $option_slug ] ) ? $this->cb_stack[ $option_slug ] : false;

			// If callback is set, call it.
			if ( $sanitize_callback && is_callable( $sanitize_callback ) ) {
				$options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
				continue;
			}

			// Treat everything that's not an array as a string.
			if ( ! is_array( $option_value ) ) {
				$options[ $option_slug ] = sanitize_text_field( $option_value );
				continue;
			}
		}

		return $options;
	}

	/**
	 * Tab JavaScript codes
	 *
	 * This code uses Local Storage for displaying/saving active tabs
	 */
	public function script() {
		?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                // Switches option sections.
                $('.pt-settings-panel-tab').hide();
                var activetab = '';
                //check for the active tab stored in the local storage
                if (typeof(localStorage) != 'undefined') {
                    activetab = localStorage.getItem('pt-settings-active-tab');
                }
                //if active tab is set, show it
                if (activetab != '' && $(activetab).length) {
                    $(activetab).fadeIn();
                } else {
                    //otherwise show the first tab
                    $('.pt-settings-panel-tab:first').fadeIn();
                }

                if (activetab != '' && $(activetab + '-tab').length) {
                    $(activetab + '-tab').addClass('nav-tab-active');
                }
                else {
                    $('.pt-settings-tab-wrapper a:first').addClass('nav-tab-active');
                }

                //on click of the tab navigation
                $('.pt-settings-tab-wrapper a').click(function (evt) {
                    $('.pt-settings-tab-wrapper a').removeClass('nav-tab-active');
                    $(this).addClass('nav-tab-active').blur();
                    var clicked_group = $(this).attr('href');

                    if (typeof(localStorage) != 'undefined') {
                        localStorage.setItem("pt-settings-active-tab", $(this).attr('href'));
                    }

                    $('.pt-settings-panel-tab').hide();
                    $(clicked_group).fadeIn();
                    evt.preventDefault();
                });
            });
        </script>
		<?php
	}

}
