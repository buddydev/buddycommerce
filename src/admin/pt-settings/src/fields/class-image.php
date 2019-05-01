<?php
/**
 * Image Upload Field class
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
 * Used for Image field type
 */
class Image extends Field {

	/**
	 * Field_Image constructor.
	 *
	 * @param array $field field options.
	 */
	public function __construct( $field ) {

		parent::__construct( $field );
	}

	/**
	 * Render the upload field.
	 *
	 * @param mixed $args options.
	 */
	public function render( $args ) {
		wp_enqueue_media();
		wp_enqueue_script( 'pt-settings-media-uploader' );

		// Attachment url.
		$value = esc_attr( $args['value'] );
		// $size  = $this->get_size();

		// We need to show this imaage.
		if ( $value ) {
			$image = "<img src='{$value}' />";
		} else {
			$image = "<img src='' />";
		}

		$id = $args['option_key'];

		?>
        <div class='pt-settings-image-placeholder'>
			<?php
			$class = '';
			if ( $value ) {
				$class = 'pt-settings-image-action-visible';
			}
			echo $image;
			?>
            <br />
            <a href="#" class="pt-settings-delete-image <?php echo $class; ?>"><?php _e( 'Remove', 'pt-settings' );?></a>
        </div>

		<?php
		printf( '<input type="hidden" class="pt-settings-hidden-image-url" id="%1$s" name="%1$s" value="%2$s"/>', esc_attr( $id ), esc_attr( $value ) );

		printf( '<input type="button" class="button pt-settings-upload-image-button" id="%1$s_button" value="%2$s" data-id="%1$s" data-btn-title="%3$s" data-uploader-title="%3$s" />', esc_attr( $id ), 'Browse', 'Select' );

		printf( '<span class="pt-settings-field-description">%s</span>', wp_kses_data( $this->get_desc() ) );

	}
}
