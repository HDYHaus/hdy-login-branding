# HDY Login Branding

HDY Login Branding customizes WordPress login, registration, and password-recovery screens from the WordPress admin.

Use it to replace the default WordPress logo, choose shared or per-page colors, and customize authentication instructions and button labels without editing theme files.

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
- Use four keyboard-accessible tabs: Shared Branding, Login, Registration, and Password Recovery.
- Customize registration headings and lost/reset-password instructions and button labels.
- Override background, button background, and button text colors for each flow, or inherit shared colors.
- Preview changes with button contrast feedback and an unsaved-changes warning.
- Stay on the selected settings tab after saving.

## Settings

**Shared Branding** controls the logo and default colors for authentication screens. Existing saved logo and color settings remain compatible.

**Login** controls the login button label and optional colors. **Registration** controls its heading, button label, and optional colors. **Password Recovery** has separate lost-password and reset-password text and color controls.

Each flow starts with **Use shared colors** enabled. Disable it to set that flow's colors. Blank color overrides inherit the corresponding shared color; enabling inheritance again retains saved overrides. Blank text fields preserve WordPress default copy.

Switch tabs with arrow keys or Home/End. All settings share one Save all changes button, and switching tabs keeps unsaved edits. Without JavaScript, all sections remain visible. The preview illustrates branding, not third-party registration fields; verify the actual pages for integration behavior.

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
php -l includes/settings.php
php tests/colors.php
node --check assets/admin.js
node tests/save-tab.cjs
git diff --check
rg -n "custom_login_logo|customLoginLogo|HDY_CUSTOM_LOGIN_LOGO" .
```

Release packages should be published with the clean filename `hdy-login-branding.zip`, even when the plugin version is `1.0.3` or later.

The PHP regression harness uses WordPress stubs, not a live database. The JavaScript regression checks the save return URL, not a complete browser round trip. Also run official Plugin Check and verify real authentication pages, third-party registration fields, keyboard navigation, and mobile layouts on a development site.

See [release notes and validation status](docs/release-1.1.1-rc.2.md). The current candidate is for testing; GitHub preparation does not authorize WordPress.org publication.

## License

HDY Login Branding is licensed under the GPL-3.0-or-later license. See [LICENSE](LICENSE).
