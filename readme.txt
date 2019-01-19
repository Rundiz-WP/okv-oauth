=== Rundiz OAuth ===
Contributors: okvee
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9HQE4GVV4KTZE
Tags: oauth, google, google login, google register, facebook, facebook login, facebook register, social login, social connect
Requires at least: 4.6.0
Tested up to: 5.0.3
Stable tag: 1.4
Requires PHP: 5.4
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
= 1.4 =
2018-12-08

* Update translation text.
* Add new translation template (.POT) file.
* Move enqueue styles from many places but same name into common library and enqueue just name.
* Add translators help.
* Support WordPress 5.0+

= 1.3.3 =
2018-10-18

* Update Facebook API to v3.1
* Use custom option for `prompt` in Google login.
* Add more custom paramters for Google login.

= 1.3.2 =
2018-03-08

* Add more coverage action for WooCommerce. This is including edit account in WooCommerce page.
* Update translation.
* Change hook into WooCommerce my account position, from bottom to top.

= 1.3 =
2018-03-07

* Move all the buttons to template.
* Add filter to button icons. So that any themes or plugins can override to use other icon html than FontAwesome 4.

= 1.2.4 =
2018-01-26

* Fix page not found on OAuth login after theme changed.

= 1.2.3 =
* Fix update hook not working.
* Fix rewrite rule did not add when update.
* Fix load language.

= 1.2 =
2018-01-04

* Improvement on multi-site registration page (wp-signup.php).
* Move OAuth redirect page to custom route front-end page. This reduce a lot of URLs in the allowed list and of course the theme developer may need to re-design these pages (2 pages) for their websites (sorry for the inconvenient).
* Many bugs fixed.

= 1.1.1 =
* Typo fixed.
* Add warning setting on multi-site enabled.

= 1.1 =
* Add WooCommerce support (partly support, developers have to script and style manually).
* Fix prevent user who logged in and change their email to non OAuth provider while Rundiz OAuth settings is using OAuth only.
* Because of the users can no longer manually change their email if using OAuth only, Add the buttons to change email using OAuth.
* Fix remove activation key, password nag that is generated with normal WordPress register function.

= 1.0 =
* Rewrite new code.
* Add Facebook login.

Previous version updates:

= 0.x =
Please read on changelog.md

== Knowledge ==

* [Multi factor authentication](https://en.wikipedia.org/wiki/Multi-factor_authentication).
* [Google 2 step authentication](https://www.google.com/landing/2step/).
* [OAuth](https://oauth.net/2/).
