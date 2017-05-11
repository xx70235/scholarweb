=== Hide My Wordpress ===
Contributors: johndarrel
Tags: hide my wp,hide wp-admin,hide wp-login,hide my site,security plugin,,wordpress security,tips,apps,wordpress apps,plugin,wordpress plugin,url,admin,login,path,paths,seo
Requires at least: 4.0
Tested up to: 4.7
Stable tag: trunk
Donate link: https://wpplugins.tips/wordpress

Hide My WP it's a security plugin. You can change and hide Wordpress Admin and Login URLs to increases your Wp security against hacker's bots.

== Description ==

Protect your Wordpress website by hiding the Wordpress Admin and Login URLs to increases your Wp security against hacker's bots.

The plugin has <strong>over 50.000 active installs</strong>. Thank you all for your trust and support!

> <strong>Free Features:</strong>
>
> *   <strong>Hide Wordpress wp-admin</strong> URL and redirect it to 404 page or a custom page
> *   <strong>Hide Wordpress wp-login.php</strong> and redirect it to 404 page or a custom page
> *   <strong>Change the wp-admin and wp-login</strong> URLs
>

The FREE version does not work for Multisites, Nginx and IIS. Only the PRO version does!

To <strong>hide all the common Wordpress paths</strong> you need the PRO version. Check all the PRO features below.

[youtube https://www.youtube.com/watch?v=gwRKHQTNkh0]

The admin URL is the most common path that hackers use to break your WordPress site.

If you don't protect yourself, you will end up having a hacked site sooner or later.

This is a free version of the plugin so you can use it for all your blogs without any restrictions.

Note: The plugin requires custom permalinks. Make sure you have it activated at Settings > Permalinks


> <strong>PRO features:</strong>
>
> *   Hide WordPress wp-admin URL
> *   Hide WordPress wp-login.php
> *   Custom admin and login URL
> *   Custom wp-includes path
> *   Custom wp-content path
> *   Random plugins name
> *   Random themes name
> *   Random themes style path
> *   Custom plugins path
> *   Custom uploads path
> *   Custom authors path
> *   Custom comment URL
> *   Custom category path
> *   Custom tags path
> *
> *   Remove the meta ids
> *   Hide _wpnonce key in forms
> *   Hide wp-image and wp-post classes
> *   Hide Emojicons if you don't use them
> *   Disable Rest API access
> *   Disable Embed scripts
> *   Disable WLW Manifest scripts
> *
> *   Brute Force Attack Protection
> *   Math function in Login Page
> *   Custom attempts, timeout, message
> *
> *   Support for Wordpress Multisites
> *   Support for Nginx
> *   Support for IIS
> *   Support for LiteSpeed
> *   Support for Apache
> *   Support for Bitnami Servers
>
> *   Recommended by Wp Rocket plugin
>     http://docs.wp-rocket.me/article/854-is-wp-rocket-compatible-with-hide-my-wp
>
> <strong>Protection against: </strong>
>
> * Brute Force Attacks,
> * SQL Injection Attacks
> * Cross Site Scripting (XSS)
>
> See all the <strong>PRO features</strong>:
> <a href="https://wpplugins.tips/wordpress">https://wpplugins.tips/wordpress</a>
>
> Hide My WP <strong>Knowledge Base</strong>:
> <a href="http://hidemywp.co">http://hidemywp.co</a>

== Installation ==
1. Log In as an Admin on your Wordpress blog.
2. In the menu displayed on the left, there is a "Plugins" tab. Click it.
3. Now click "Add New".
4. There, you have the buttons: "Search | Upload | Featured | Popular | Newest". Click "Upload".
5. Upload the hide-my-wp.zip file.
6. After the upload it's finished, click Activate Plugin.

== Screenshots ==
1. Choose the desired level of WordPress security for your site
2. Change the URLs wp-admin and wp-login.php to different URLs
3. Choose to hide the wp-admin and wp-login.php to increase the Wordpress security and hackers will get 404 errors
4. Login to your site with the new login URL
5. You'll be redirected to the new admin URL

== Upgrade Notice ==
Since version 1.1.022 if you hide the admin path you will not be able to access the admin path as visitor. You need to go to the login path.

== Changelog ==
= 1.1.025 (11 May 2017) =
* Added login redirect loop protection
* Added the option to hide the new admin path. This is optional now
* You can call the new admin path without /

= 1.1.023 (2 May 2017) =
* Compatible with BoddyBoss Theme
* Fixed small bugs
* Fixed the $user->has_cap error on login

= 1.1.022 (21 Apr 2017) =
* Fixed rewrite for automatic upgrades
* Compatible with WP 4.7.4

= 1.1.021 =
* Fixed redirect for Forgot Password and Register options
* Fixed redirect for /login for several themes
* Protect from errors in case the user enters both admin and login with the same name

= 1.1.019 =
* Fixed the Submenues for the last version of Wordpress

= 1.1.018 =
* Added the custom redirect page for the hidden paths
* Removed the generator information

= 1.1.017 =
* Fixed the notification issue for some themes
* Fixed wp-admin redirect to login in some cases
* Compatible with WP 4.7.3

= 1.1.016 =
* Better hide the wp-admin path from not logged in users in the Lite Mode
* We added the support feature for you

= 1.1.015 =
* Compatible with WP 4.7.2
* Improved the hide URLs feature
* Removed the wp-config.php insertion
* Fixed the loop on sign in
* Prevent errors for IIS server
* Added recommended websites

= 1.1.012 =
* Compatible with WP 4.7
* Fixed memory load alert
* Fixed small bugs

= 1.1.011 =
* Fixed https ajax in http frontend
* Settings are not lost after plugin or theme activation

= 1.1.010 =
* Fixed redirect if the 404 page doesn't exists

= 1.1.009 =
* Changed the Lite options
* Fixed small bugs
* Compatible with the last version of Wordpress
* Compatible with Wordpress 4.6.1

= 1.1.007 =
* Remove all data on plugin deactivate
* Update saved data on user logout
* Don't change the settings unless the user logs out from admin

= 1.1.006 =
* Send URLs and safe parameter by email on important changes
* Fixed small bugs

= 1.1.005 =
* Fixed save_mod_rewrite_rules issue
* Compatible with Wordpress 4.6
* Fixed small bugs for plugin css

= 1.1.003 =
* Fixed issues with Nginx, IIS, and Litespeed servers
* Prevent hiding the wp-admin and wp-login in Lite Mode
* Improved login with the safe parameter


= 1.1.002 =
* Hide the /wp-login path

= 1.1.001 =
* Compatible with Wordpress 4.5
* Main Wordpress security features

== Frequently Asked Questions ==
= Is this plugin working on WP Multisite? =

Yes, this feature is only available on the Hide My WordPress PRO version.

The PRO version also works with Apache, Nginx, IIS and LiteSpeed servers

Please visit: <a href="https://wpplugins.tips/wordpress">https://wpplugins.tips/wordpress</a>


= I forgot the custom login and admin URLs. What now? =

Don't panic.

You can still access your site with the secure parameter
http://domainname/wp-login.php?hmw_disable=[your_code]

= Locked out of my site!  I set the plugin, and when I left I can't manage to get in =

Rename the plugin directory /wp-content/plugins/hide_my_wp so that the plugins wouldn't hide the wp-admin path

Save it and it should be back from where you left it.

Make sure you remember the secure parameter, and it will be much simpler.

= Is this plugin working if I don't have custom permalinks on my site? =

No. You need to have custom permalinks set on in Settings > Permalinks.

You will get a notification in the Settings page if something is not setup right.


= What to do before I deactivate the plugin? =

It's better to switch to Default mode in Settings > Hide my wp.

If you don't, the plugin will automatically change your site back to the safe URLs, and it will tell you what to do in case you don't have write permission for the config files

_______________________________________________________________________

= Is this Plugin free of charge? =

Yes. The Lite features will always be free.

To unlock all the features, please visit: Please visit: <a href="https://wpplugins.tips/wordpress">https://wpplugins.tips/wordpress</a>

= Is this plugin enough to protect my website from all hackers? =

The free version of Hide My WP hides the wp-admin and wp-login as described but will not protect you from all the hackers attack.

Hide My Wordpress PRO hides all the common paths and patterns used but bots to detect that you are using Wordpress.

We also recommend you to install Premium Themes and Plugins and not just any Wordpress plugin because the free plugins are usually made by beginners and they don't have security knowledge.
