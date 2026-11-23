<?php
require __DIR__ . '/bootstrap.php';

function expect_same( $expected, $actual, $message ) {
	if ( $expected !== $actual ) {
		throw new RuntimeException( $message . ': expected ' . var_export( $expected, true ) . ', got ' . var_export( $actual, true ) );
	}
}

$test_options = array();
expect_same( 'WordPress', apply_filters( 'wp_mail_from_name', 'WordPress' ), 'Blank sender name passes through' );
expect_same( 'wordpress@example.test', apply_filters( 'wp_mail_from', 'wordpress@example.test' ), 'Blank sender address passes through' );

$test_options[ HDY_LOGIN_BRANDING_OPTION_EMAIL_FROM_NAME ] = '<b>Example Sender</b>';
$test_options[ HDY_LOGIN_BRANDING_OPTION_EMAIL_FROM ]      = 'sender@example.test';
expect_same( 'Example Sender', apply_filters( 'wp_mail_from_name', 'WordPress' ), 'Sender name is sanitized and applied' );
expect_same( 'sender@example.test', apply_filters( 'wp_mail_from', 'wordpress@example.test' ), 'Valid sender email is applied' );

$test_options[ HDY_LOGIN_BRANDING_OPTION_EMAIL_FROM ] = 'not-an-email';
expect_same( 'wordpress@example.test', apply_filters( 'wp_mail_from', 'wordpress@example.test' ), 'Invalid sender email passes through unchanged' );

add_filter( 'wp_mail_from', function ( $email ) { return 'plugin@example.test'; }, 10 );
$test_options[ HDY_LOGIN_BRANDING_OPTION_EMAIL_FROM ] = 'sender@example.test';
expect_same( 'plugin@example.test', apply_filters( 'wp_mail_from', 'wordpress@example.test' ), 'Later mail plugin filters can override HDY' );

expect_same( '', hdylb_sanitize_support_url( 'javascript:alert(1)' ), 'Unsafe support URL is rejected' );
expect_same( 'https://support.example.test/help', hdylb_sanitize_support_url( 'https://support.example.test/help' ), 'HTTPS support URL is accepted' );

ob_start();
do_action( 'login_footer' );
$html = ob_get_clean();
expect_same( '', $html, 'Support link is absent without a saved URL' );

$test_options[ HDY_LOGIN_BRANDING_OPTION_SUPPORT_URL ]      = 'https://support.example.test/help?a=1&b=2';
$test_options[ HDY_LOGIN_BRANDING_OPTION_SUPPORT_LABEL ]    = '<strong>Need help</strong>';
$test_options[ HDY_LOGIN_BRANDING_OPTION_SUPPORT_NEW_TAB ] = 1;
foreach ( array( 'login', 'register', 'lostpassword', 'resetpass', 'confirm_admin_email' ) as $screen ) {
	$GLOBALS['action'] = $screen;
	ob_start();
	do_action( 'login_footer' );
	$html = ob_get_clean();
	expect_same( true, false !== strpos( $html, 'Need help' ), 'Support link displays on ' . $screen );
	expect_same( true, false !== strpos( $html, 'target="_blank" rel="noopener noreferrer"' ), 'New-tab link is safe on ' . $screen );
	expect_same( true, false !== strpos( $html, 'a=1&amp;b=2' ), 'Support URL is escaped on ' . $screen );
}

$test_options[ HDY_LOGIN_BRANDING_OPTION_SUPPORT_NEW_TAB ] = 0;
ob_start();
do_action( 'login_footer' );
$html = ob_get_clean();
expect_same( false, false !== strpos( $html, 'target="_blank"' ), 'Same-tab is the default behavior' );

hdylb_register_settings();
expect_same( 0, $test_registered[ HDY_LOGIN_BRANDING_OPTION_SUPPORT_NEW_TAB ]['default'], 'New-tab option defaults to unchecked' );
expect_same( 'hdylb_sanitize_sender_email', $test_registered[ HDY_LOGIN_BRANDING_OPTION_EMAIL_FROM ]['sanitize_callback'], 'Sender email uses its validator' );
echo "PASS: sender validation and precedence, support URL safety, footer rendering, and settings defaults.\n";
