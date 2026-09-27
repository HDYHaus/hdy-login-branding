<?php
require __DIR__ . '/bootstrap.php';
function expect_same( $a, $b, $message ) {
	if ( $a !== $b ) { throw new RuntimeException( $message . ': ' . json_encode( $b ) ); }
}
$test_options = array( 'hdylb_background_color' => '#111111', 'hdylb_button_color' => '#2271b1', 'hdylb_button_text_color' => '#ffffff' );
$shared = hdylb_get_flow_colors( 'register' );
expect_same( '#111111', $shared['background_color'], 'Existing color is inherited' );
$test_options['hdylb_register_background_color'] = '#ff0000';
expect_same( $shared, hdylb_get_flow_colors( 'register' ), 'Stored override stays inactive until enabled' );
$test_options['hdylb_register_shared'] = 0;
$test_options['hdylb_register_button_color'] = 'invalid';
expect_same( '#ff0000', hdylb_get_flow_colors( 'register' )['background_color'], 'Override applies' );
expect_same( '#2271b1', hdylb_get_flow_colors( 'register' )['button_color'], 'Invalid or blank override inherits' );
expect_same( $shared, hdylb_get_flow_colors( 'login' ), 'Registration colors do not leak to login' );
expect_same( $shared, hdylb_get_flow_colors( 'checkemail' ), 'Other states keep shared colors' );
$test_options['hdylb_resetpass_shared'] = 0;
$test_options['hdylb_resetpass_button_color'] = '#333';
expect_same( '#333', hdylb_get_flow_colors( 'rp' )['button_color'], 'Reset alias gets reset colors' );
expect_same( $shared, hdylb_get_flow_colors( 'retrievepassword' ), 'Lost-password alias remains independent' );
$test_options['hdylb_register_shared'] = 1;
expect_same( $shared, hdylb_get_flow_colors( 'register' ), 'Re-enabling inheritance restores shared colors' );
expect_same( '#ff0000', $test_options['hdylb_register_background_color'], 'Toggle keeps stored override' );
hdylb_register_settings();
expect_same( 1, $test_registered['hdylb_register_shared']['default'], 'New installs inherit by default' );
expect_same( 'hdylb_sanitize_button_color', $test_registered['hdylb_resetpass_button_color']['sanitize_callback'], 'Overrides are sanitized' );
ob_start();
hdylb_render_settings_page();
$html = ob_get_clean();
$dom = new DOMDocument();
@$dom->loadHTML( $html );
$xpath = new DOMXPath( $dom );
expect_same( 4, $xpath->query( '//section' )->length, 'Four sections render without JavaScript' );
foreach ( $xpath->query( '//input[@type="text"] | //textarea' ) as $field ) {
	$id = $field->getAttribute( 'id' );
	expect_same( 1, $xpath->query( '//label[@for="' . $id . '"]' )->length, 'Text field has associated label' );
}
echo "PASS: color inheritance, isolation, aliases, saved overrides, sanitization registration, and labels.\n";
