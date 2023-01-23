=== Rundiz OAuth ===
Contributors: okvee
Tags: oauth, google, google login, google register, facebook, facebook login, facebook register, social login, social connect
Tested up to: 6.2
Requires at least: 5.0
Requires PHP: 5.4
Stable tag: 1.4.3
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
WordPress 4.6.0 or higher

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
= 1.4.3 =
2022-12-20

* Fix "PHP Deprecated:  Creation of dynamic property".

= 1.4.2 =
2020-05-01

* Fetch text content of design guide to display in the settings page.
* Update Google OAuth URLs.
* Update Facebook OAuth URLs.
* Add Logger class and log the API result in debug mode (WP_DEBUG constant must be defined to `true`).

= 1.4.1 =
2019-12-05

* Fix remove login password field (for OAuth only option) to compatible with WordPress 5.3
* Add filter to not allow "Both sites and user accounts can be registered" register option on network settings (multi-site).

= 1.4 =
2018-12-08

* Update translation text.
* Add new translation template (.POT) file.
* Move enqueue styles from many places but same name into common library and enqueue just name.
* Add translators help.
* Support WordPress 5.0+

Previous version updates:  
Please read on changelog.md

== Knowledge ==

* [Multi factor authentication](https://en.wikipedia.org/wiki/Multi-factor_authentication).
* [Google 2 step authentication](https://www.google.com/landing/2step/).
* [OAuth](https://oauth.net/2/).
