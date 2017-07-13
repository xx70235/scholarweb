=== Visual Admin Customizer ===
Contributors: whiteshadow
Tags: admin, customization, branding, hide
Requires at least: 4.6
Tested up to: 4.7
Stable tag: 1.0

Hide almost any part of the WordPress admin by using a visual editor.

== Description ==
Hide almost any part of the WordPress admin area by using a visual, point-and-click editor. You can hide things from all users or only from specific roles. You can also add your own CSS to the WordPress admin (per role).

This plugin does not have a fixed list of things it can customize. Instead, it displays a live version of the WordPress dashboard and lets you select the parts of the page that you want to hide. You can hide almost any unique element (but see the "Limitations" section below).

Here are a few examples of things you can hide:
* Dashboard widgets.
* Post editor meta boxes.
* Custom fields.
* Screen options.
* Individual buttons.
* Text boxes, checkboxes, dropdown lists and most other form fields.

= Requirements =
* PHP 5.3 and up.
* A modern browser like Firefox, Chrome or IE 9 and up. 

= Limitations =
* This is not a security tool. The changes it makes are cosmetic. Users who are familiar with web development can bypass them. Don't use it for security-critical tasks.
* It's not great at hiding dynamic content like dialog windows, pop-up menus, or any content that changes frequently or depending on the logged-in user.
* It's intended for customizing *unique* things like specific meta boxes or buttons. If you want to do something like hide *all* "Edit" links on a page or change the appearance of *all* "Save Changes" buttons, you'll have to write the CSS code yourself or use a different plugin.

== Installation ==
1. Install the plugin through the WordPress "Plugins" screen, or upload the plugin files to the `/wp-content/plugins/visual-admin-customizer` directory.
2. Activate the plugin through the "Plugins" screen.
3. To start customizing the admin panel, go to "Tools -> Admin Customizer".

== Screenshots ==

1. Plugin configuration screen
2. Add custom CSS to the WordPress admin

== Changelog ==
= 1.0 =
* Initial release.