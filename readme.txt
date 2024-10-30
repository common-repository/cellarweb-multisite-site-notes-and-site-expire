===  CellarWeb Multisite Site Notes and Site Expire ===
Plugin Name:        CellarWeb Multisite Site Notes and Site Expire
Contributors: rhellewellgmailcom
Donate link: https://cellarweb.com/
Author URI: https://www.cellarweb.com
Plugin URI: https://www.cellarweb.com/wordpress-plugins/
Tags:  multisite notes expire expiration
Requires at least: 4.9
Tested up to: 6.5
Version: 1.00
Stable tag: 1.00
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

 For multisites, adds ability of the network super-admin to 'expire' a blog (subsite) automatically. Will redirect the expired site to another page. Adds a 'notes' area for each blog for super-admin notes. All settings only available to the super-admin through the network Sites/Edit Sites page.

== Description ==

This plugin allows the multi-site administrator to automatically expire a site on a specific date. This is useful for subsites that require regular payments to keep the site active.

 If your multisite has a yearly subscription, for example, then you can set a subsite (blog) to automatically expire on a certain date - say one year plus a 'grace' period. If the next subscription payment is not received by that date, then the site will automatically be 'deleted' from public view. (A 'deleted' site's data is still available and can be 'un-deleted' by the super-admin by changing the site expiration date. A 'deleted' site is not purged.)

Once the subscription payment is made, it is easy to change the expiration date.

There is a setting to specify the 'redirect' URL for deleted sites. You could set this URL to a 'that site is no longer available' page on your main site (or any site). By default, the redirect will go to your site's 404 page.

There is also a 'notes' area where you can put site notes.

All settings are via a 'Notes' tab on the Edit Site screen, which is only available to the super-admin. Blogs (sub-sites) do not see any of these settings. There is a Settings page for the plugin, but it is only information about the plugin settings. All settings are done via the Network, Sites, Edit Site page.

The networks' "main" site only displays the "Notes" textarea, as you don't want your main site to expire.


== Installation ==

This section describes how to install the plugin and get it working.


1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. 'Network Activate' the plugin through the 'Plugins' screen on the main site's Dashboard in WordPress.
1. Change settings via the Network, Sites, Edit Sites page. There is only an information screen in the main sites' Settings page.

== Frequently Asked Questions ==

= Where are the Settings? =

They are on the "Notes" tab of the Network, Sites, Edit Site screen. They are not available in the main site's (or subsite) Setting screen, although the plugin Settings screen has information about the settings.

See the screenshot for the available settings.

== Screenshots ==

1. The Network, Sites screen, showing an expired site.
1. The "Notes" settings page, available only on the Network, Sites, Edit Site screen.


== Changelog ==

**Version 1.00 (10 Feb 2022) **
- Initial Release

== Upgrade Notice ==

** Version 1.00  (10 Feb 2022) **
- Initial Release
