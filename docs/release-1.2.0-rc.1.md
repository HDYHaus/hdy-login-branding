# HDY Login Branding 1.2.0-rc.1

Development release candidate for issues #10 and #12. Not a WordPress.org release.

## Changes Since 1.0.3

- Organize settings into Shared Branding, Login, Registration, and Password Recovery tabs.
- Add registration heading and button labels plus separate lost/reset-password instructions and button labels.
- Scope custom text to the matching authentication action; blank fields retain WordPress defaults.
- Preserve shared logo and color settings and add optional per-flow background, button background, and button text colors.
- Add a shared authentication-screen background image selected from the WordPress Media Library.
- Center and scale the background image responsively with color fallback and remove/reset controls.
- Include the background image in the live branding preview.
- Add button contrast feedback, accessible tab navigation, unsaved-change protection, and selected-tab persistence after saving.
- Retain WordPress validation messages and third-party registration content.
- Point plugin and author metadata to the HDY Login Branding product page without duplicating WordPress's View details link.

## Compatibility

No option migration is required. Existing colors become shared defaults; per-flow overrides start disabled. Other authentication states retain shared styling without individual text settings. Blank overrides inherit the shared value. Sites without a selected background image retain their existing color-only or WordPress default background.

The source readme declares `Tested up to: 7.1`. WordPress.org ignores patch numbers for this field, so this covers WordPress 7.1.2 after the approved readme and release tag are published to plugin SVN.

## Validation Record

- PHP syntax checks passed for the plugin, settings module, and background-image regression test.
- Stub-based PHP regression tests passed for color inheritance, per-flow isolation, aliases, option registration, labels, background-image validation, rendering, responsive defaults, and reset behavior.
- JavaScript syntax and save-return-URL regression tests passed for all four tabs.
- The release archive passed integrity checks and excludes tests and development documentation.
- Official Plugin Check passed all selected categories with no errors on dev.hdyhaus.com.
- Live desktop and mobile login views were verified with a selected background image.
- Registration and password-recovery screens were verified with the shared background image.
- Removing the test image restored the previously saved color-only background.

## Package And Release Boundary

The development package contains only runtime PHP, admin assets, `readme.txt`, and the license under a single `hdy-login-branding/` directory. Tests, GitHub documentation, release notes, and WordPress.org screenshot assets are excluded.

Pull request #14 closes #10 when merged. Issue #12 was completed by pull request #13. A production release still requires final version metadata, a matching SVN tag, final-package validation, and explicit owner approval before publishing to WordPress.org.
