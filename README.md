# HDY Login Branding

HDY Login Branding is a simple WordPress plugin for customizing the login screen from the WordPress admin.

Use it to replace the default WordPress logo, choose login page colors, and change the login button text without editing theme files or writing custom code.

## Plugin Details

- Plugin name: **HDY Login Branding**
- WordPress slug: `hdy-login-branding`
- Text domain: `hdy-login-branding`
- Main plugin file: `hdy-login-branding.php`
- GitHub repo: https://github.com/HDYHaus/hdy-login-branding
- Built by: [HDY Haus](https://hdyhaus.com)

## Features

- Replace the default WordPress login logo.
- Choose a logo from the WordPress Media Library.
- Link the selected logo to your site homepage.
- Change the login page background color.
- Change the login button text.
- Change the login button background and text colors.
- Manage everything from **Settings > HDY Login Branding**.

## Installation

1. Download the plugin zip.
2. In WordPress, go to **Plugins > Add New Plugin > Upload Plugin**.
3. Upload the zip file.
4. Activate **HDY Login Branding**.
5. Go to **Settings > HDY Login Branding** and choose your logo, colors, and button text.

## Development

The plugin uses namespaced, plugin-specific identifiers:

- PHP functions, options, and hooks use the `hdylb_*` prefix.
- PHP constants use the `HDY_LOGIN_BRANDING_*` prefix.
- CSS classes, IDs, and script handles use the `hdy-login-branding-*` prefix.
- JavaScript uses the `hdyLoginBranding` object.

Screenshot files are not bundled in the runtime plugin package. WordPress.org screenshots should be uploaded separately to the plugin SVN `assets/` directory after approval.

## Validation

Run these checks before packaging a release:

```bash
php -l hdy-login-branding.php
git diff --check
rg -n "custom_login_logo|customLoginLogo|HDY_CUSTOM_LOGIN_LOGO" .
```

## License

HDY Login Branding is licensed under the GPL-3.0-or-later license. See [LICENSE](LICENSE).
