=== Category Write Panels ===
Plugin URI: http://www.seo-jerusalem.com/home/seo-friendly-web-development/wordpress-category-write-panels-plugin/
Version: 1.0.3
Contributors: R.J. Kaplan, SEO Jerusalem
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7746509
Tags: write panel, category, custom
Requires at least: 2.8.4
Tested up to: 2.9

Automatically creates separate write and edit panels for each category

== Description ==

Easily manage multiple content types in your WordPress site. This plugin automatically creates separate write and edit panels for each category. Every top level category will have it's own top level menu with a write panel and an edit posts page. Subcategories can be used the way categories are normaly used, each write panel showing only it's sub categories.

Basically this plugin does the base of what flutter and pods do, but 10 times faster, more reliable and simpler.

**Developers:**

Developers can manipulate write panels individually by getting the current main category ID. This can be done with the global variable `$cwp_postcat`. The category name can be obtained with `$cwp_postcatname`.

**Warnings:**

* This plugin may (or may not) break if you are using any plugins that manipulate the default WordPress menus.
* This plugin will probably break if you have more then 16 top level categories

== Installation ==

1. Upload `category_panels.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. You are set. no setup or customization required.


== Screenshots ==

1. Multiple write panels, created automatically from top level WordPress categories
2. Custom write panels outlined.

== Changelog ==

= 1.0 =
* Initial release.

= 1.0.1 =
* Fixed issue where all menu items used the same ID, causing validation errors.