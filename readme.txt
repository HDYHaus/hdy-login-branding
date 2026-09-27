=== HDY Login Branding ===
Contributors: mariaojob
Tags: login, logo, branding, admin
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.1.1-rc.2
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Brand WordPress login, registration, and password-recovery screens from Settings.
Built by HDY Haus (https://hdyhaus.com).

== Description ==
Customize the WordPress login screen without touching code.

Features:
* Enable or disable the custom login logo.
* Choose a logo from the Media Library.
* Logo links to the site homepage when enabled.
* Customize the login page background color.
* Customize the login button label, background, and text color.
* Customize registration heading and button text.
* Customize lost-password and reset-password instructions and button text.
* Organize settings with keyboard-accessible Shared Branding, Login, Registration, and Password Recovery tabs.
* Use shared colors or override page background, button background, and button text colors per flow.
* Preview changes with button contrast feedback before saving.

== Installation ==
1. Upload the `hdy-login-branding` folder to `/wp-content/plugins/` Or in your WordPress admin, go to Plugins > Add New > Upload.
2. Activate the plugin through the Plugins screen in WordPress.
3. Go to Settings > HDY Login Branding.
4. Enable the logo and select an image from the Media Library.

== Frequently Asked Questions ==
= What size should the logo be? =
The login screen uses a 320x120 box. Larger images are scaled to fit.

= Where does the logo link to? =
When the custom logo is enabled and set, it links to the homepage.

== Screenshots ==
1. Plugin settings page with custom logo selection and preview.
2. Color picker settings for login page background and login button styles.
3. Customized WordPress login page with branded logo, background, and button text.

== Changelog ==
= 1.1.1-rc.2 =
* Keep the selected branding tab open after saving settings.

= 1.1.1-rc.1 =
* Development test candidate; not a WordPress.org release.
* Added accessible settings tabs, live preview, and unsaved-change protection.
* Added per-flow color overrides while preserving existing shared colors.
* Added button contrast feedback and responsive settings layouts.

= 1.1.0 =
* Organized settings into login, registration, and password-recovery sections.
* Added registration heading and button text settings.
* Added lost-password and reset-password instruction and button text settings.
* Scoped custom button labels to their matching authentication flow.

= 1.0.3 =
* Added a Settings action link on the WordPress Plugins screen.

= 1.0.2 =
* Renamed plugin to HDY Login Branding for WordPress.org review.
* Updated prefixes, text domain, and asset handles for directory compliance.
* Removed bundled screenshot assets from the plugin package.

= 1.0.1 =
* Added login page background color customization with WordPress color picker support.
* Added screenshot assets and expanded screenshot descriptions.
* Updated plugin and readme license metadata to GPL-3.0-or-later.
* Improved coding standards compliance with local PHPCS/WPCS tooling.

= 1.0.0 =
* Initial release.
