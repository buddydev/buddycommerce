<?php
/**
 * User Profile Track Order page content.
 *
 * @package    BuddyCommerce
 * @subpackage templates\default\members
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

echo do_shortcode( '[woocommerce_order_tracking]' );
