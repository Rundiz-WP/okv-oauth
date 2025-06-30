=== Rundiz OAuth ===
Contributors: okvee
Tags: oauth, google, social login, social connect
Tested up to: 6.8
Requires at least: 5.0
Requires PHP: 5.4
Stable tag: 1.5.6
License: MIT
License URI: https://opensource.org/licenses/MIT

Use OAuth such as Google, Facebook to login and register.

== Description ==
Use Oauth such as Google and Facebook account to login, register member in WordPress website. Control how your wp-login page will be used. Force use only OAuth or use them both.

Both Google and Facebook already have "Multi factor" or "2 factor" authentication. 
So, instead of implementing the 2FA (2 factor authentication) into your WordPress and add those keys into your user's authenticator app on smart phone (which might too much list of them). Just use OAuth system!

It is very easy and much secure for your users on your WordPress website. (Depends on your user security settings on those providers).
Since v 1.0 your users can register using OAuth in one click from your website and one click to allow/continue on OAuth provider website. Just few clicks and done.

You can also change login expiration by using remember login or use OAuth only for login method.

This project is maintain by <a href="https://rundiz.com" target="author_site">Rundiz.com</a>. Feel free to rate and comments.<br>

= System requirement =
PHP 5.4 or higher<br>
Open SSL PHP extension.<br>
WordPress 5.0 or higher

== Installation ==
1. Upload "okv-oauth" folder to the "/wp-content/plugins/" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Access plugin setup page.
4. Follow setup instruction on screen.

== Frequently Asked Questions ==

= Can I force use only OAuth login or register? =
Yes, check your settings.

= Does multi-site work? =
Yes, it is.

= Does it support WooCommerce? =
Yes, it does. If you set to use OAuth only then your users cannot change to use other email than those are available via OAuth.

= Does it support hosted domain for G Suite? =
Yes, this is new feature since 1.3.3. However, please enter your settings in **Other parameters** fields in the settings page.

== Screenshots ==
1. Administrator settings
2. Admin settings/Google
3. Admin settings/Facebook
4. Register use WordPress and OAuth
5. Register use OAuth only
6. Login use WordPress and OAuth
7. Login use OAuth only

== Changelog ==
= 1.5.7 =
xxxx-xx-xx

* Update to FontAwesome 6 from 4.
* Move manual code that called to OAuth multiple classes & methods to one that call them all per setting, request query string.

= 1.5.6 =
2025-03-24

* Delete widget on uninstall, delete old options on activate/update the plugin.
* Add new hooks about reset password, retrieve password.
* Update option without autoload to improve performance.

= 1.5.5 =
2025-03-18

* Fix load text domain too early.

= 1.5.4 =
2024-12-12

* New wp-script (Node.js script) build block and create new **-rtl.css** file automatically.

= 1.5.3 =
2023-12-23

* Fix check settings that use OAuth only.

= 1.5.2 =
2023-11-30

* Fix use wp_remote_xxx() instead of cURL in Google OAuth API class.
* Add display link to edit profile option into block.
* Add filters hook supported in block and legacy widget.

= 1.5.1 =
2023-11-24

* Add legacy login links widget (Hot update). This widget have more options and can work with PolyLang Free while block need PolyLang Pro.

= 1.5 =
2023-11-23

* Facebook OAuth API no longer supported (the plugin author has no Facebook account anymore).
* Fix incorrect check select box value with key that is number.
* Add block for display register, login, logout links.
* Update code following PHPCS guide.


Previous version updates:  
Please read on changelog.md

== Knowledge ==

* [Multi factor authentication](https://en.wikipedia.org/wiki/Multi-factor_authentication).
* [Google 2 step authentication](https://www.google.com/landing/2step/).
* [OAuth](https://oauth.net/2/).
