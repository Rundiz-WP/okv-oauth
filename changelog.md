# Change log

## Version 1.x

### 1.5.6
2025-03-24

* Delete widget on uninstall, delete old options on activate/update the plugin.
* Add new hooks about reset password, retrieve password.
* Update option without autoload to improve performance.

### 1.5.5
2025-03-18

* Fix load text domain too early.

### 1.5.4
2024-12-12

* New wp-script (Node.js script) build block and create new **-rtl.css** file automatically.

### 1.5.3
2023-12-23

* Fix check settings that use OAuth only.

### 1.5.2
2023-11-30

* Fix use wp_remote_xxx() instead of cURL in Google OAuth API class.
* Add display link to edit profile option into block.
* Add filters hook supported in block and legacy widget.

### 1.5.1
2023-11-24

* Add legacy login links widget (Hot update). This widget have more options and can work with PolyLang Free while block need PolyLang Pro.

### 1.5
2023-11-23

* Facebook OAuth API no longer supported (the plugin author has no Facebook account anymore).
* Fix incorrect check select box value with key that is number.
* Add block for display register, login, logout links.
* Update code following PHPCS guide.

### 1.4.4
2023-01-24

* Remove row meta hook.
* Improve code by escape, sanitize as reported by PHP CodeSniffer & WordPress coding standard.
* Fix method MUST always return string in **App/Libraries/ErrorsCollection.php** at `getErrorMessage()` method.

### 1.4.3
2022-12-20

* Fix "PHP Deprecated:  Creation of dynamic property".

### 1.4.2
2020-05-01

* Fetch text content of design guide to display in the settings page.
* Update Google OAuth URLs.
* Update Facebook OAuth URLs.
* Add Logger class and log the API result in debug mode (WP_DEBUG constant must be defined to `true`).

### 1.4.1
2019-12-05

* Fix remove login password field (for OAuth only option) to compatible with WordPress 5.3
* Add filter to not allow "Both sites and user accounts can be registered" register option on network settings (multi-site).

### 1.4
2018-12-08

* Update translation text.
* Add new translation template (.POT) file.
* Move enqueue styles from many places but same name into common library and enqueue just name.
* Add translators help.
* Support WordPress 5.0+

### 1.3.3
2018-10-18

* Update Facebook API to v3.1
* Use custom option for `prompt` in Google login.
* Add more custom paramters for Google login.

### 1.3.2
2018-03-08

* Add more coverage action for WooCommerce. This is including edit account in WooCommerce page.
* Update translation.
* Change hook into WooCommerce my account position, from bottom to top.

### 1.3
2018-03-07

* Move all the buttons to template.
* Add filter to button icons. So that any themes or plugins can override to use other icon html than FontAwesome 4.

### 1.2.4
2018-01-26

* Fix page not found on OAuth login after theme changed.

### 1.2.3
* Fix update hook not working.
* Fix rewrite rule did not add when update.
* Fix load language.

### 1.2
2018-01-04

* Improvement on multi-site registration page (wp-signup.php).
* Move OAuth redirect page to custom route front-end page. This reduce a lot of URLs in the allowed list and of course the theme developer may need to re-design these pages (2 pages) for their websites (sorry for the inconvenient).
* Many bugs fixed.

### 1.1.1
* Typo fixed.
* Add warning setting on multi-site enabled.

### 1.1
* Add WooCommerce support (partly support, developers have to script and style manually).
* Fix prevent user who logged in and change their email to non OAuth provider while Rundiz OAuth settings is using OAuth only.
* Because of the users can no longer manually change their email if using OAuth only, Add the buttons to change email using OAuth.
* Fix remove activation key, password nag that is generated with normal WordPress register function.

### 1.0
* Rewrite new code.
* Add Facebook login.

## Version 0.x

### 0.6.1
* Fix setting to do not use OAuth and cannot login.

### 0.6
* Add login expiration settings, this can also be override by other plugins.

### 0.5
* Fix uninstall/delete the plugin and get error.

### 0.4
* Fix php class dependency hell that some other plugin use different version of Google class and cause error.
* Rename the plugin.

### 0.3
* Update to prevent registration from original form while settings to use OAuth only.
* Minor UI fixed.

### 0.25
* Change information and add donation link.

### 0.2
* Modify text translation. This make translation really works.
* Add Thai language.

### 0.1
* Beginning.