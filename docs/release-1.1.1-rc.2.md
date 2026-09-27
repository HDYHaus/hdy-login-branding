# HDY Login Branding 1.1.1-rc.2

Development release candidate for issue #12. Not a WordPress.org release.

## Changes Since 1.0.3

- Organize settings into Shared Branding, Login, Registration, and Password Recovery tabs.
- Add registration heading and button labels plus separate lost/reset-password instructions and button labels.
- Scope custom text to the matching authentication action; blank fields retain WordPress defaults.
- Preserve shared logo and color settings and add optional per-flow background, button background, and button text colors.
- Add live branding previews, button contrast feedback, accessible tab navigation, and unsaved-change protection.
- Preserve the selected tab after saving (rc.2 fix).
- Retain WordPress validation messages and third-party registration content.

## Compatibility

No option migration is required. Existing colors become shared defaults; per-flow overrides start disabled. Other authentication states retain shared styling without individual text settings. Blank overrides inherit the shared value.

## Validation Record

- Local PHP syntax and WordPress Coding Standards checks passed.
- Stub-based PHP regression tests cover color inheritance, per-flow isolation, aliases, option registration, and labels.
- JavaScript syntax and save-return-URL regression tests passed for all four tabs.
- Local desktop/mobile preview and keyboard checks passed, including contrast warnings and retaining edits across tabs.
- The site owner reported official Plugin Check found no errors on the dev-site candidate before the rc.2 fix.
- The site owner confirmed the authentication-flow, color/text, mobile, and TrustGate checks were satisfactory on dev.hdyhaus.com.
- The site owner separately confirmed rc.2 retains the selected tab after saving.
- Official Plugin Check has not been independently rerun for rc.2; rerun against the final production package before publication.

## Package And Release Boundary

The draft GitHub release contains `hdy-login-branding.zip`, byte-identical to the locally tested `hdy-login-branding-1.1.1-rc.2.zip`. It contains only the runtime PHP, assets, readme, and license under a single `hdy-login-branding/` directory; tests and development documentation are excluded.

The pull request closes #12 when merged. Keep the candidate release as a draft until approved. A production release needs final version metadata, final-package validation, and explicit owner approval before publishing to WordPress.org.
