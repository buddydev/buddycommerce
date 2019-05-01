<?php
/**
 * User Payment methods list template.
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
?>
<div class="bcommerce-payment-methods">
	<?php do_action( 'woocommerce_account_payment-methods_endpoint' ); ?>
</div>

