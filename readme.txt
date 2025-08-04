=== BuddyCommerce: WooCommerce and BuddyPress Integration  ===
Contributors: buddydev, sbrajesh
Tags: woocommerce,buddypress,woocommerce-for-buddypress
Requires at least: 5.0
Tested up to: 6.8.2
Stable tag: 1.0.8
Requires PHP: 5.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Highly Flexible WooCommerce to BuddyPress integration which puts site admins in the complete control.

== Description ==
on a BuddyPress & WooCommerce site, BuddyCommerce helps listing user's orders, address, downloads etc on their BuddyPres profile.
You can integrate all or part of the functionality from WooCommerce account to BuddyPress profile.

It is a lightweight plugin aimed at providing quick and easy integration between BuddyPress and WooCommerce.

In the current version, we support:
* WooCommerce user account to BuddyPress User profile integration.
* Configurable redirection from WooCommerce account to BuddyPress profile tab(Can be enabled/disabled).
* You can limit which WooCommerce account content appears on BuddyPress profile.
* WooCommerce Orders history & Track Orders on BuddyPress user profile.
* WooCommerce Address View & Manage(edit/update) address on BuddyPress user profile.
* WooCommerce user downloads availability from BuddyPress user profile.
* WooCommerce user payment methods on BuddyPress user profile.
* **All content can be added as a top level BuddyPress profile tab or sub tab of any other existing BuddyPress tab**
* New: WooCommerce Subscriptions on BuddyPress User Profile
* New: WooCommerce Membership on BuddyPress User Profile
* Better handling of checkout endpoints.
* Works with BuddyBoss platform

For each of the tab content you can configure:-
* Whether the tab is enabled on BuddyPress profile or not.
* Label, slug, position for the BuddyPress Tab/Subtab and admin bar menu item.
* Whether the tab appears as top level user profile tab or child of some existing tab.
* Whether to redirect from WooCommerce account end points to BuddyPress profile.
* And some more..


We hope you will enjoy the integration. Please help us make it better by sharing your idea on our
 * [Blog Post](https://buddydev.com/introducing-buddycommerce-woocommerce-and-buddypress-integration/
 * [BuddyDev Forums](https://buddydev.com/support/forums/)

= Contribute =
The plugin is developed over github. Please [contribute](https://github.com/buddydev/buddycommerce).

We are working on a detailed guide for the plugin. Please keep an eye on the future updates for more. Demo is coming soon.

= Support =
Please use [BuddyDev Support Forums](https://buddydev.com/support/forums/) for the support.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/buddycommerce` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Visit your BuddyPress profile to see it in action.


== Frequently Asked Questions ==
= Where Do I get Support? =
Please use [BuddyDev Support Forums](https://buddydev.com/support/forums/) for the support.

= Does it need WooCommerce & BuddyPress both? =
Yes, it does. If both WooCommerce & BuddyPress are not active, the plugin will not do anything.

= What is the supported BuddyPress version? =
BuddyPress 8.0+, Tested with BuddyPress 14.3.4

= What is the supported WooCommerce version? =
WooCommerce 6.0+, Tested with WooCommerce 10.0.4

= Does it work with BuddyBoss platform and theme? =
Yes, it does.

= Can I suggest a new feature? =
Sure. We will love to hear your ideas. Please share your thoughts on [BuddyDev Support Forums](https://buddydev.com/support/forums/)
or get in touch via our contact form on BuddyDev.

= I need help with BuddyPress or WooCommerce, Can you help?
Yes. We offer paid development/customization support for BuddyPress & WooCommerce.
Please get in touch by providing the details [here](https://buddydev.com/buddypress-custom-development-service/)

== Screenshots ==

1. Admin settings for shop screenshot-1.png
1. Admin settings for orders screenshot-2.png
1. User profile orders page screenshot-3.png
1. User edit address under settings(if configured) screenshot-4.png

== Changelog ==
= 1.0.8 =
* Fix fatal error on BuddyPress 14.0 due to deprecation of 'bp_use_wp_admin_bar()' function, props Matt Jones(lamellama).

= 1.0.7 =
* Fix not showing error notices on saving address.

= 1.0.6 =
* Fix optional parameter notice in 8.1+

= 1.0.5 =
* Fix notices for spl_autoload_register() on PHP 8.0+.

= 1.0.4 =
* Fix add payment method showing existing payment methods and double button.

= 1.0.3 =
* Fix orders pagination.

= 1.0.2 =
* Fix payment method actions(Add/Set default, delete) for the shops allowing saving of payment methods.

= 1.0.1 =
* Added support for WooCommerce Subscriptions
* Added support for WooCommerce Membership
* Better handling of checkout endpoint

= 1.0.0 =
* Initial release.
