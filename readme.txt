=== Rundiz OAuth ===
Contributors: okvee
Tags: oauth, google, social login, social connect
Tested up to: 6.9
Requires at least: 5.0
Requires PHP: 5.4
Stable tag: 1.6.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Use OAuth such as Google, LINE to login and register.

== Description ==
Use Oauth such as Google and LINE account to login, register member in WordPress website.

Both Google and LINE already have "Multi factor" or "2 factor" authentication.  
So, instead of implementing the 2FA (2 factor authentication) into your WordPress and add those keys into your user's authenticator app on smart phone (which may already have a lot of them). Just use OAuth system!

It is very easy and much secure for your users on your WordPress website. (Depends on your user security settings on those providers).  
Since v 1.0 your users can register using OAuth in one click from your website and one click to allow/continue on OAuth provider website.

You can also change login expiration by using remember login.  
You can set how your user login use OAuth with normal login form, or OAuth only, or disable OAuth login from Rundiz OAuth settings page.

This project is maintain by <a href="https://rundiz.com" target="author_site">Rundiz.com</a>. Feel free to rate and comments.<br>

= System requirement =
Open SSL PHP extension.

== Installation ==
1. Upload "okv-oauth" folder to the "/wp-content/plugins/" directory or use add from plugins management page.
2. Activate the plugin through the "Plugins" menu in WordPress.

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
= 1.6.0 =
2025-07-01

* Update to FontAwesome 6 from 4.
* Move manual code that called to OAuth multiple classes & methods to one that call them all per setting, request query string.
* Add LINE OAuth.
* Update code following PHPCS guide.
* Bump version from v1.5.7

Previous version updates:  
Please read on changelog.md

== Knowledge ==

* [Multi factor authentication](https://en.wikipedia.org/wiki/Multi-factor_authentication).
* [Google 2 step authentication](https://www.google.com/landing/2step/).
* [OAuth](https://oauth.net/2/).
