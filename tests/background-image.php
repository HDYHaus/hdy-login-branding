<?php
require __DIR__ . '/bootstrap.php';

function expect_background_same( $expected, $actual, $message ) {
	if ( $expected !== $actual ) {
		throw new RuntimeException( $message . ': ' . json_encode( $actual ) );
	}
}

$test_image_ids       = array( 42 => true );
$test_attachment_urls = array( 42 => 'https://example.test/uploads/login-background.jpg' );

expect_background_same( 42, hdylb_sanitize_image_id( '42' ), 'Image attachment is accepted' );
expect_background_same( 0, hdylb_sanitize_image_id( '99' ), 'Non-image attachment is rejected' );

hdylb_register_settings();
expect_background_same(
	'hdylb_sanitize_image_id',
	$test_registered['hdylb_background_image_id']['sanitize_callback'],
	'Background image setting uses image validation'
);

$test_options = array(
	'hdylb_background_color'    => '#112233',
	'hdylb_background_image_id' => 42,
);
hdylb_login_styles();
$css = $test_inline_styles['login'] ?? '';

if ( false === strpos( $css, 'background-color:#112233' ) ) {
	throw new RuntimeException( 'Configured background color is retained beneath the image' );
}
if ( false === strpos( $css, 'background-image:url("https://example.test/uploads/login-background.jpg")' ) ) {
	throw new RuntimeException( 'Selected background image is rendered' );
}
if ( false === strpos( $css, 'background-size:cover' ) || false === strpos( $css, 'background-position:center' ) ) {
	throw new RuntimeException( 'Responsive background defaults are rendered' );
}

$test_options['hdylb_background_image_id'] = 0;
$test_inline_styles                         = array();
hdylb_login_styles();
if ( false !== strpos( $test_inline_styles['login'] ?? '', 'background-image:url(' ) ) {
	throw new RuntimeException( 'Removing the image restores color-only styling' );
}

ob_start();
hdylb_render_settings_page();
$html = ob_get_clean();
if ( false === strpos( $html, 'name="hdylb_background_image_id"' ) || false === strpos( $html, 'id="hdy-login-branding-background-select"' ) ) {
	throw new RuntimeException( 'Background image controls are rendered' );
}

echo "PASS: background image validation, rendering, reset behavior, responsive defaults, and settings controls.\n";
