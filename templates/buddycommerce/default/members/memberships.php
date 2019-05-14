<?php
/**
 * User My Membership Page content.
 *
 * Used for WooCommerce memberships plugin.
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

echo '<div class="wc-membership-contents">';
$endpoint = wc_memberships_get_members_area_endpoint();

$located  = wc_locate_template( 'myaccount/my-membership-navigation.php' );

if ( $located && is_readable( $located ) ) {
	require $located;
}

do_action( "woocommerce_account_{$endpoint}_endpoint" );

echo '</div>';
