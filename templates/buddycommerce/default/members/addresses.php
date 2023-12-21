<?php
/**
 * User Address View/Edit template.
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

// Print all notices.
woocommerce_output_all_notices();

// Our end point is 'edit-address', we need the address type to pass.
// Magic.
// We are telling wooCommerce which type of address to edit.
do_action( 'woocommerce_account_edit-address_endpoint', get_query_var( 'edit-address' ) );


