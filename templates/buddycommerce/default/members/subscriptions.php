<?php
/**
 * User Profile subscriptions template.
 *
 * @package    BuddyCommerce
 * @subpackage templates\default\members
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.1
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;
// needs current page to be passed.
do_action( 'woocommerce_account_subscriptions_endpoint' );
