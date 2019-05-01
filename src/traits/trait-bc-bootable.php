<?php
/**
 * Bootable Trait.
 *
 * Allows us to implement uniform booting strategy for all our client classes.
 *
 * @package    BuddyCommerce
 * @subpackage Traits
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyCommerce\Traits;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Add static boot method.
 */
trait BC_Bootable {

	/**
	 * Boot class.
	 *
	 * @return $this
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
		return $self;
	}
}
