# HDY Login Branding

HDY Login Branding is a WordPress plugin that lets site owners replace the default WordPress login logo and adjust basic login screen styling from Settings.

It is built for beginners and small teams who want the login page to feel like part of the same brand experience as the rest of the website, without editing theme files or writing custom code.

## Features

- Enable or disable a custom login logo.
- Choose a logo from the WordPress Media Library.
- Link the selected login logo to the site homepage.
- Customize the login page background color.
- Customize the login button label.
- Customize the login button background and text color.

## WordPress.org Identity

- Plugin name: **HDY Login Branding**
- Slug: `hdy-login-branding`
- Text domain: `hdy-login-branding`
- Primary plugin file: `hdy-login-branding.php`
- Landing page: https://hdyhaus.com/wp-plugins/custom-login-logo/

## Installation

1. Download the latest plugin zip.
2. In WordPress, go to **Plugins > Add New Plugin > Upload Plugin**.
3. Upload the zip and activate **HDY Login Branding**.
4. Go to **Settings > HDY Login Branding**.
5. Choose your logo, colors, and button text, then save.

## Development Notes

The plugin uses the `hdylb_*` PHP prefix, `HDY_LOGIN_BRANDING_*` constants, `hdy-login-branding-*` CSS and script handles, and the `hdyLoginBranding` JavaScript object.

Screenshot image files are intentionally not bundled in the plugin runtime package. WordPress.org screenshots should be uploaded separately to the plugin SVN `assets/` directory after approval.

## Validation

Before packaging a release:

```bash
php -l hdy-login-branding.php
rg -n "custom_login_logo|customLoginLogo|HDY_CUSTOM_LOGIN_LOGO" .
```

The only expected `custom-login-logo` reference is the HDY Haus landing page URL, unless that published page slug changes later.

## License

GPL-3.0-or-later. See [LICENSE](LICENSE).
